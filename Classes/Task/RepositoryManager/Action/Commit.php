<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke (tommy@van-tomas.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Commit.php
 *
 * Task center task action for committing migrations/changes into a data repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

/**
 * Task center task action for committing migrations/changes into a data repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Task_RepositoryManager_Action_Commit extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	protected static $changeOptionTemplate = '<option value="%changeName%">%changeName%</option>';

	public function checkAccess() {
		return TRUE;
	}

	public function getOptions() {
		$user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.author.label'),
			'field' => '<input name="author" value="' . $user->getAuthorRFC2822Formatted() . '" size="60" disabled="disabled" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.subject.label'),
			'field' => '<input name="subject" value="" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.description.label'),
			'field' => '<textarea name="description" cols="60" rows="5"></textarea>',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.change.label'),
			'field' => '<select name="change[]" multiple="multiple" size="10">' . $this->getChanges() . '</select>',
		);
	}

	public function getChanges() {
		$options = array();

		$changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');
		$changes = $changeRepository->findAll();

		foreach ($changes as $change) {
			$replacePairs = array(
				'%changeName%' => $change->getName(),
			);

			$options[] = strtr(self::$changeOptionTemplate, $replacePairs);
		}

		return implode(LF, $options);
	}

	public function process() {
		try {
			$content = $this->commit();

			$content .= $this->updateGitIgnore();

			$content .= $this->deleteCommittedChanges();
		} catch (Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}

	protected function commit() {
		$user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');

		$command = t3lib_div::makeInstance('Tx_Dbmigrate_Task_RepositoryManager_Command_Git_Commit');
		$command->setArguments(array(
			'%changesPath%' => t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation),
			'%commitMessage%' => escapeshellarg(t3lib_div::_GP('subject') . LF . LF . t3lib_div::_GP('description')),
			'%author%' => escapeshellarg($user->getAuthorRFC2822Formatted()),
			'%changes%' => escapeshellcmd(implode(' ', t3lib_div::_GP('change'))),
		));
		$command->execute();

		return 'Successfully committed the selected changes into the repository.';
	}

	protected function updateGitIgnore() {
		$ignoreFilePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.gitignore');

		$fh = @fopen($ignoreFilePath, 'a');

		$this->raiseExceptionIf(FALSE === $fh, 'The .gitignore file couldn\'t be openend for writing.');

		$changes = t3lib_div::_GP('change');

		foreach ($changes as $change) {
			@fwrite($fh, $change . LF);
		}

		@fclose($fh);

		return 'Successfully updated .gitignore.';
	}

	protected function deleteCommittedChanges() {
		$changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');

		$changes = t3lib_div::_GP('change');

		foreach ($changes as $change) {
			$changeRepository->removeOneByName($change);
		}

		$command = t3lib_div::makeInstance('Tx_Dbmigrate_Task_RepositoryManager_Command_Git_UpdateIndex');
		$command->setArguments(array(
			'%changesPath%' => Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation,
			'%changes%' => implode(' ', t3lib_div::_GP('change')),
		));
		$command->execute();

		return 'Successfully removed committed changes.';
	}
}
?>

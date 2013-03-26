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

	protected static $commitCommand = 'cd %changesPath% && git add -f %changes% && git commit -m %commitMessage% --author=%author% %changes% && git push origin master 2>&1';

	protected static $updateIndexCommand = 'cd %changesPath% && git update-index --assume-unchanged %changes% 2>&1';

	protected static $authorTemplate = '%name% <%email%>';

	public function checkAccess() {
		return TRUE;
	}

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.author.label'),
			'field' => '<input name="author" value="' . $this->getAuthor() . '" size="60" disabled="disabled" />',
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
			$replacePairs = array(
				'%changesPath%' => t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation),
				'%commitMessage%' => escapeshellarg(t3lib_div::_GP('subject') . LF . LF . t3lib_div::_GP('description')),
				'%author%' => escapeshellarg($this->getAuthor()),
				'%changes%' => escapeshellcmd(implode(' ', t3lib_div::_GP('change'))),
			);

			$command = strtr(self::$commitCommand, $replacePairs);

			$this->executeCommand($command, 'The committing failed. Please see the following output for details:');

			return 'Successfully committed the selected changes into the repository.';
	}

	// @TODO: inject Tx_Dbmigrate_Backend_User instance and handle this there!!!
	protected function getAuthor() {
		$user = $GLOBALS['BE_USER']->user;

		$name = $user['username'];

		if ('' !== $user['realName']) {
			$name = $user['realName'];
		}

		$email = sprintf('%s@%s', $user['username'], t3lib_div::getIndpEnv('HTTP_HOST'));

		if ('' !== $user['email']) {
			$email = $user['email'];
		}

		$replacePairs = array(
			'%name%' => $name,
			'%email%' => $email,
		);

		return strtr(self::$authorTemplate, $replacePairs);
	}

	protected function updateGitIgnore() {
		$ignoreFilePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.gitignore');

		$fh = @fopen($ignoreFilePath, 'a');

		if (FALSE === $fh) {
			throw new Exception('The .gitignore file couldn\'t be openend for writing.');
		}

		$changes = t3lib_div::_GP('change');

		foreach ($changes as $change) {
			@fwrite($fh, $change . LF);
		}

		@fclose($fh);

		return 'Successfully updated .gitignore.';
	}

	protected function deleteCommittedChanges() {
		$changesPath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation);

		$changes = t3lib_div::_GP('change');

		foreach ($changes as $change) {
			$changePath = $changesPath . $change;

			@unlink($changePath);

			if (TRUE === file_exists($changePath)) {
				throw new Exception(sprintf('Failure during removing a committed change %s. Please check directory permissions.', $change));
			}
		}

		$replacePairs = array(
			'%changesPath%' => $changesPath,
			'%changes%' => implode(' ', t3lib_div::_GP('change')),
		);

		$command = strtr(self::$updateIndexCommand, $replacePairs);

		$this->executeCommand($command, 'Updating the index for setting the "assume unchanged" flag for the commited changes failed. Please see the following output for details:');

		return 'Successfully removed committed changes.';
	}
}
?>
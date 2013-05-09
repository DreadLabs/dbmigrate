<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager\Action;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \DreadLabs\Dbmigrate\Domain\Repository\ChangeRepository;

/**
 * Commit.php
 *
 * Task center task action for committing migrations/changes into a data repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class CommitAction extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractAction {

	protected static $changeOptionTemplate = '<option value="%changeName%">%changeName%</option>';

	public function checkAccess() {
		return TRUE;
	}

	public function getOptions() {
		$user = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Backend\\User');

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

		$changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
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
		} catch (\Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}

	protected function commit() {
		$user = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Backend\\User');

		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\Git\\CommitCommand');
		$command->setArguments(array(
			'%changesPath%' => ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation),
			'%commitMessage%' => escapeshellarg(GeneralUtility::_GP('subject') . LF . LF . GeneralUtility::_GP('description')),
			'%author%' => escapeshellarg($user->getAuthorRFC2822Formatted()),
			'%changes%' => escapeshellcmd(implode(' ', GeneralUtility::_GP('change'))),
		));
		$command->execute();

		return 'Successfully committed the selected changes into the repository.';
	}

	protected function updateGitIgnore() {
		$ignoreFilePath = ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation . '.gitignore');

		$fh = @fopen($ignoreFilePath, 'a');

		$this->raiseExceptionIf(FALSE === $fh, 'The .gitignore file couldn\'t be openend for writing.');

		$changes = GeneralUtility::_GP('change');

		foreach ($changes as $change) {
			@fwrite($fh, $change . LF);
		}

		@fclose($fh);

		return 'Successfully updated .gitignore.';
	}

	protected function deleteCommittedChanges() {
		$changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');

		$changes = GeneralUtility::_GP('change');

		foreach ($changes as $change) {
			$changeRepository->removeOneByName($change);
		}

		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\Git\\UpdateIndexCommand');
		$command->setArguments(array(
			'%changesPath%' => ChangeRepository::$storageLocation,
			'%changes%' => implode(' ', GeneralUtility::_GP('change')),
		));
		$command->execute();

		return 'Successfully removed committed changes.';
	}
}
?>
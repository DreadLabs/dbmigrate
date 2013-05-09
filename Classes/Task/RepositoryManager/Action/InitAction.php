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

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \DreadLabs\Dbmigrate\Domain\Repository\ChangeRepository;

/**
 * Init.php
 *
 * Task center task action for initializing a migration/change data working copy repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class InitAction extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractAction {

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Domain\Repository\ChangeRepository
	 */
	protected $changeRepository = NULL;

// 	public function initialize() {
// 		$this->changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
// 	}

	public function checkAccess() {
		// @TODO: \DreadLabs\Dbmigrate\Backend\User instance!!!
		return $GLOBALS['BE_USER']->isAdmin();
	}

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.projectName.label'),
			'field' => '<input name="projectName" value="' . $this->configuration->getNormalizedSystemSiteName() . '" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.repository.label'),
			'field' => '<input name="repository" value="" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.default.label'),
			'field' => '<textarea name="default" cols="60" rows="5">' . implode(' ', $this->configuration->getDefaultTables()) . '</textarea>',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.additional.label'),
			'field' => '<textarea name="additional" cols="60" rows="5">' . implode(' ', $this->configuration->getAdditionalTables()) . '</textarea>',
		);
	}

	public function process() {
		try {
			$content = $this->createRepository();

			$content .= $this->createBaseline();

			$content .= $this->createIgnoreFile();
		} catch (\Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}

	protected function createRepository() {
		$this->raiseExceptionIf(
			file_exists(ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation . '.git')),
			'The repository is already initialized!'
		);

		$this->initRepository();

		$this->addRepositoryRemote();

		return 'The repository was successfully initialized.';
	}

	protected function initRepository() {
		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\Git\\InitCommand');
		$command->setArguments(array(
			'%targetPath%' => escapeshellcmd(ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation)),
		));
		$command->execute();
	}

	protected function addRepositoryRemote() {
		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\Git\\RemoteAddCommand');
		$command->setArguments(array(
			'%targetPath%' => escapeshellcmd(ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation)),
			'%remoteName%' => 'origin',
			'%remotePath%' => escapeshellcmd(GeneralUtility::_GP('repository')),
		));
		$command->execute();
	}

	protected function createBaseline() {
		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\MysqlDumpCommand');
		$command->setArguments(array(
			'%user%' => TYPO3_db_username,
			'%host%' => TYPO3_db_host,
			'%password%' => TYPO3_db_password,
			'%database%' => TYPO3_db,
			'%default%' =>  escapeshellcmd(GeneralUtility::_GP('default')),
			'%additional%' => escapeshellcmd(GeneralUtility::_GP('additional')),
			'%targetPath%' => ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation),
			'%projectName%' => escapeshellcmd($this->configuration->getNormalizedSystemSiteName(GeneralUtility::_GP('projectName'))),
		));
		$command->execute();

		return 'The base line dump was successfully created.';
	}

	protected function createIgnoreFile() {
		$ignoreFilePath = ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation . '.gitignore');

		$this->raiseExceptionIf(TRUE === file_exists($ignoreFilePath), '.gitignore file already exists. Overwrite will not happen!');

		$fh = @fopen($ignoreFilePath, 'w');

		$this->raiseExceptionIf(FALSE === $fh, 'The .gitignore file could not be written. Please check the access rights!');

		fclose($fh);

		return 'Successfully written .gitignore.';
	}
}
?>
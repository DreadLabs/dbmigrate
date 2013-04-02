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
 * Init.php
 *
 * Task center task action for initializing a migration/change data working copy repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

/**
 * Task center task action for initializing a migration/change data working copy repository.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Task_RepositoryManager_Action_Init extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	/**
	 *
	 * @var Tx_Dbmigrate_Domain_Repository_ChangeRepository
	 */
	protected $changeRepository = NULL;

// 	public function initialize() {
// 		$this->changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');
// 	}

	public function checkAccess() {
		// @TODO: Tx_Dbmigrate_Backend_User instance!!!
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
		} catch (Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}

	protected function createRepository() {
		$this->raiseExceptionIf(
			file_exists(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.git')),
			'The repository is already initialized!'
		);

		$this->initRepository();

		$this->addRepositoryRemote();

		return 'The repository was successfully initialized.';
	}

	protected function initRepository() {
		$command = t3lib_div::makeInstance('Tx_Dbmigrate_Task_RepositoryManager_Command_Git_Init');
		$command->setArguments(array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation)),
		));
		$command->execute();
	}

	protected function addRepositoryRemote() {
		$command = t3lib_div::makeInstance('Tx_Dbmigrate_Task_RepositoryManager_Command_Git_RemoteAdd');
		$command->setArguments(array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation)),
			'%remoteName%' => 'origin',
			'%remotePath%' => escapeshellcmd(t3lib_div::_GP('repository')),
		));
		$command->execute();
	}

	protected function createBaseline() {
		$command = t3lib_div::makeInstance('Tx_Dbmigrate_Task_RepositoryManager_Command_MysqlDump');
		$command->setArguments(array(
			'%user%' => TYPO3_db_username,
			'%host%' => TYPO3_db_host,
			'%password%' => TYPO3_db_password,
			'%database%' => TYPO3_db,
			'%default%' =>  escapeshellcmd(t3lib_div::_GP('default')),
			'%additional%' => escapeshellcmd(t3lib_div::_GP('additional')),
			'%targetPath%' => t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation),
			'%projectName%' => escapeshellcmd($this->configuration->getNormalizedSystemSiteName(t3lib_div::_GP('projectName'))),
		));
		$command->execute();

		return 'The base line dump was successfully created.';
	}

	protected function createIgnoreFile() {
		$ignoreFilePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.gitignore');

		$this->raiseExceptionIf(TRUE === file_exists($ignoreFilePath), '.gitignore file already exists. Overwrite will not happen!');

		$fh = @fopen($ignoreFilePath, 'w');

		$this->raiseExceptionIf(FALSE === $fh, 'The .gitignore file could not be written. Please check the access rights!');

		fclose($fh);

		return 'Successfully written .gitignore.';
	}
}
?>

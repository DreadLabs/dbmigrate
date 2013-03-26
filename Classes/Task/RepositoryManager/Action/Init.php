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

	protected static $repositoryInitCommand = 'git init %targetPath% 2>&1';

	protected static $repositoryRemoteAddCommand = 'cd %targetPath% && git remote add %remoteName% %remotePath% 2>&1';

	protected static $dumpCommand = 'mysqldump -u%user% -h%host% -p%password% -c --no-create-db %database% %default% %additional% > %targetPath%%projectName%.sql';

	public function checkAccess() {
		// @TODO: Tx_Dbmigrate_Backend_User instance!!!
		return $GLOBALS['BE_USER']->isAdmin();
	}

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.projectName.label'),
			'field' => '<input name="projectName" value="' . $this->getNormalizedProjectNameFromSysSitename() . '" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.repository.label'),
			'field' => '<input name="repository" value="" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.default.label'),
			'field' => '<textarea name="default" cols="60" rows="5">' . implode(' ', Tx_Dbmigrate_Configuration::$defaultTables) . '</textarea>',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.init.field.additional.label'),
			'field' => '<textarea name="additional" cols="60" rows="5">' . implode(' ', $this->configuration->getAdditionalTables()) . '</textarea>',
		);
	}

	protected function getNormalizedProjectNameFromSysSitename($override = NULL) {
		$cleanupPattern = '/[^a-zA-Z0-9]/';
		$sitename = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];

		if (TRUE !== is_null($override)) {
			$sitename = $override;
		}

		$name = preg_replace($cleanupPattern, '', $sitename);

		return strtolower($name);
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
		if (TRUE === file_exists(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.git'))) {
			throw new Exception('The repository is already initialized!');
		}

		$this->initRepository();

		$this->addRepositoryRemote();

		return 'The repository was successfully intialized.';
	}

	protected function initRepository() {
		$replacePairs = array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation)),
		);

		$command = strtr(self::$repositoryInitCommand, $replacePairs);

		$this->executeCommand($command, 'The repository initialization failed. Please see the following output for further details:');
	}

	protected function addRepositoryRemote() {
		$replacePairs = array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation)),
			'%remoteName%' => 'origin',
			'%remotePath%' => escapeshellcmd(t3lib_div::_GP('repository')),
		);

		$command = strtr(self::$repositoryRemoteAddCommand, $replacePairs);

		$this->executeCommand($command, 'The addition of the remote repository failed. Please see the following output for further details:');
	}

	protected function createBaseline() {
		$replacePairs = array(
			'%user%' => TYPO3_db_username,
			'%host%' => TYPO3_db_host,
			'%password%' => TYPO3_db_password,
			'%database%' => TYPO3_db,
			'%default%' =>  escapeshellcmd(t3lib_div::_GP('default')),
			'%additional%' => escapeshellcmd(t3lib_div::_GP('additional')),
			'%targetPath%' => t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation),
			'%projectName%' => escapeshellcmd($this->getNormalizedProjectNameFromSysSitename(t3lib_div::_GP('projectName'))),
		);

		$command = strtr(self::$dumpCommand, $replacePairs);

		$this->executeCommand($command, 'The dumping of the the baseline file failed. Maybe the reason can be found in the output:');

		return 'The base line dump was successfully created!';
	}

	protected function createIgnoreFile() {
		$ignoreFilePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '.gitignore');

		if (TRUE === file_exists($ignoreFilePath)) {
			throw new Exception('.gitignore file already exists. Overwrite will not happen!');
		}

		$fh = @fopen($ignoreFilePath, 'w');

		if (FALSE === $fh) {
			throw new Exception('The .gitignore file could not be written. Please check the access rights!');
		}

		fclose($fh);

		return 'Successfully written .gitignore';
	}
}
?>
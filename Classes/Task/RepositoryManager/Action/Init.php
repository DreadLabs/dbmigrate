<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

class Tx_Dbmigrate_Task_RepositoryManager_Action_Init extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	protected static $repositoryInitCommand = 'git init %targetPath% 2>&1';

	protected static $repositoryRemoteAddCommand = 'cd %targetPath% && git remote add %remoteName% %remotePath% 2>&1';

	protected static $dumpCommand = 'mysqldump -u%user% -h%host% -p%password% -c --no-create-db %database% %default% %additional% > %targetPath%%projectName%.sql';

	public function checkAccess() {
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
		if (TRUE === file_exists(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath . '.git'))) {
			throw new Exception('The repository is already initialized!');
		}

		$this->initRepository();

		$this->addRepositoryRemote();

		return 'The repository was successfully intialized.';
	}

	protected function initRepository() {
		$replacePairs = array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath)),
		);

		$command = strtr(self::$repositoryInitCommand, $replacePairs);

		$this->executeCommand($command, 'The repository initialization failed. Please see the following output for further details:');
	}

	protected function addRepositoryRemote() {
		$replacePairs = array(
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath)),
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
			'%targetPath%' => t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath),
			'%projectName%' => escapeshellcmd($this->getNormalizedProjectNameFromSysSitename(t3lib_div::_GP('projectName'))),
		);

		$command = strtr(self::$dumpCommand, $replacePairs);

		$this->executeCommand($command, 'The dumping of the the baseline file failed. Maybe the reason can be found in the output:');

		return 'The base line dump was successfully created!';
	}

	protected function createIgnoreFile() {
		$ignoreFilePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath . '.gitignore');

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
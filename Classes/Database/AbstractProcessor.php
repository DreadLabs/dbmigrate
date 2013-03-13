<?php
class Tx_Dbmigrate_Database_AbstractProcessor implements t3lib_Singleton {

	protected $isInitialized = FALSE;

	/**
	 *
	 * @var t3lib_beUserAuth
	 */
	protected $backendUser = NULL;

	protected function init() {
		if (FALSE === $this->isInitialized) {
			$this->backendUser = $GLOBALS['BE_USER'];

			$this->isInitialized = TRUE;
		}
	}

	protected function isQueryLoggingEnabled() {
		$state = $this->getBackendUserConfig('dbmigrate:logging:enabled', FALSE);

		return $state;
	}

	protected function getBackendUserConfig($key, $default) {
		$userConfig = $this->backendUser->uc;

		if (TRUE === isset($userConfig[$key])) {
			$configurationValue = $userConfig[$key];
		} else {
			$configurationValue = $default;
		}

		return $configurationValue;
	}

	protected function logQueryForTable($prefix, $table, $query) {
		$observedTables = $this->getBackendUserConfig('dbmigrate:logging:tables', array());

		if (TRUE === array_key_exists($table, $observedTables)) {
			$migrationName = $prefix . t3lib_div::underscoredToUpperCamelCase($table);

			$filePath = $this->getMigrationFileName($migrationName);

			$fh = fopen($filePath, 'w');
			fwrite($fh, $query);
			fclose($fh);
		}
	}

	protected function getMigrationFileName($migrationName) {
// 		$realName = $GLOBALS['BE_USER']->user['realName'];
// 		$username = $GLOBALS['BE_USER']->user['username'];
		$username = $this->backendUser->user['username'];

		$version = 0;

		do {
			$migrationVersion = sprintf('%04d', $version);
			$filename = sprintf('%s-%s-%s-%s.sql', date('Ymd'), $migrationName, $migrationVersion, $username);
			$filePath = t3lib_extMgm::extPath('dbmigrate', 'Resources/Public/Migrations/' . $filename);
			$version = $version + 1;
		} while (file_exists($filePath));

		return $filePath;
	}
}
?>
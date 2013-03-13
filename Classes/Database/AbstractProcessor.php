<?php
class Tx_Dbmigrate_Database_AbstractProcessor implements t3lib_Singleton {

	protected $isInitialized = FALSE;

	/**
	 *
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $backendUser = NULL;

	protected function init() {
		if (FALSE === $this->isInitialized) {
			$this->backendUser = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');

			$this->isInitialized = TRUE;
		}
	}

	protected function isQueryLoggingEnabled() {
		return $this->backendUser->isLoggingEnabled();
	}

	protected function logQueryForTable($prefix, $table, $query) {
		$observedTables = $this->backendUser->getUserConfiguration('dbmigrate:logging:tables', array());

		if (TRUE === array_key_exists($table, $observedTables)) {
			$migrationName = $prefix . t3lib_div::underscoredToUpperCamelCase($table);

			$filePath = $this->getMigrationFileName($migrationName);

			$fh = fopen($filePath, 'w');
			fwrite($fh, $query);
			fclose($fh);
		}
	}

	protected function getMigrationFileName($migrationName) {
		$username = $this->backendUser->getUserName();

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
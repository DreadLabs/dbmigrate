<?php
class Tx_Dbmigrate_Database_AbstractProcessor implements t3lib_Singleton {

	protected $isInitialized = FALSE;

	/**
	 *
	 * @var Tx_Dbmigrate_Configuration
	 */
	protected $configuration = NULL;

	/**
	 *
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $backendUser = NULL;

	protected function init() {
		if (FALSE === $this->isInitialized) {
			$this->configuration = t3lib_div::makeInstance('Tx_Dbmigrate_Configuration');

			$this->backendUser = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');

			$this->isInitialized = TRUE;
		}
	}

	protected function isMonitoringEnabled() {
		return $this->configuration->isMonitoringEnabled();
	}

	protected function logQueryForTable($table, $query) {
		if (TRUE === $this->configuration->isTableActive($table)) {
			$this->writeChange($query);
		}
	}

	protected function writeChange($query) {
		try {
			$filePath = $this->getChangeFileName();

			$fh = fopen($filePath, 'a');
			fwrite($fh, $query . ';' . chr(10));
			fclose($fh);
		} catch (Exception $e) {
			// fail silently
			// @todo: log into sys_log
		}
	}

	protected function getChangeFileName() {
		$date = date('Ymd');
		$username = $this->backendUser->getUserName();
		$changeId = $this->backendUser->getChangeId();
		$changeType = $this->backendUser->getChangeType();

		if (TRUE === is_null($changeId) || TRUE === is_null($changeType)) {
			throw new Exception('There is no change to log in the pipeline!');
		}

		$replacePairs = array(
			'%date%' => $date,
			'%username%' => $username,
			'%changeId%' => $changeId,
			'%changeType%' => $changeType,
		);

		$filePath = $this->configuration->getChangeFilePath($replacePairs);

		return $filePath;
	}
}
?>
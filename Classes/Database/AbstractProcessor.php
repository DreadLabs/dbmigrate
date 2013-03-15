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

	protected function logQueryForTable($table, $query) {
		$observedTables = $this->backendUser->getUserConfiguration('dbmigrate:logging:tables', array());

		if (TRUE === array_key_exists($table, $observedTables)) {
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
		$changeId = $this->backendUser->getUserConfiguration('dbmigrate:change:id', NULL);
		$changeType = $this->backendUser->getUserConfiguration('dbmigrate:change:type', NULL);

		if (TRUE === is_null($changeId) || TRUE === is_null($changeType)) {
			throw new Exception('There is no change to log in the pipeline!');
		}

		$fileName = sprintf('%s-%s-%s.sql', $date, $changeId, $changeType);
		$filePath = t3lib_extMgm::extPath('dbmigrate', 'Resources/Public/Migrations/' . $fileName);

		return $filePath;
	}
}
?>
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
	protected $user = NULL;

	protected function init() {
		if (FALSE === $this->isInitialized) {
			$this->configuration = t3lib_div::makeInstance('Tx_Dbmigrate_Configuration');

			$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');
			$this->user->injectConfiguration($this->configuration);

			$this->isInitialized = TRUE;
		}
	}

	protected function isMonitoringEnabled() {
		return $this->configuration->isMonitoringEnabled();
	}

	protected function logQueryForTable($table, $query) {
		try {
			$this->raiseExceptionUnlessTableIsActive($table);

			$this->writeChange($query);
		} catch (Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
	}

	protected function raiseExceptionUnlessTableIsActive($table) {
		$raiseException = FALSE === $this->configuration->isTableActive($table);

		if ($raiseException) {
			$msg = sprintf('The query is not loggable for the table %s: Table is not active!', $table);
			throw new Exception($msg, 1364320214);
		}
	}

	protected function writeChange($query) {
		$filePath = $this->user->getActiveChangeFilePath();

		$fileHandle = @fopen($filePath, 'a');

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $fileHandle,
			'The file %s could not be opened for writing.',
			$filePath
		);

		$numberOfWrittenBytes = @fwrite($fileHandle, $query . ';' . chr(10));

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $numberOfWrittenBytes,
			'The query could not be written to the given file %s.',
			$filePath
		);

		$closingState = @fclose($fileHandle);

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $closingState,
			'The file %s could not be closed.',
			$filePath
		);
	}

	protected function raiseExceptionUnlessFileOperationWasSuccessful($fileOperation, $messageTemplate, $filePath) {
		if (FALSE === $fileOperation) {
			$msg = sprintf($messageTemplate, $filePath) . ' Please check the file/directory permissions!';
			throw new Exception($msg, 1364321227);
		}
	}
}
?>
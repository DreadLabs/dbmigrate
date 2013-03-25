<?php
class Tx_Dbmigrate_Database_TceMainTransactionHandler implements t3lib_Singleton {

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

	/**
	 *
	 * @var t3lib_db
	 */
	protected $db = NULL;

	public function injectConfiguration(Tx_Dbmigrate_Configuration $configuration = NULL) {
		if (TRUE !== is_null($configuration)) {
			$this->configuration = $configuration;
		} else {
			$this->configuration = t3lib_div::makeInstance('Tx_Dbmigrate_Configuration');
		}
	}

	public function injectUser(Tx_Dbmigrate_Backend_User $user = NULL) {
		if (TRUE !== is_null($user)) {
			$this->user = $user;
		} else {
			$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');
		}
	}

	public function injectDatabase(t3lib_db $db = NULL) {
		if (TRUE !== is_null($db)) {
			$this->db = $db;
		} else {
			$this->db = $GLOBALS['TYPO3_DB'];
		}
	}

	/**
	 * process_cmdmap entry point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processCmdmap_beforeStart(t3lib_TCEmain $tceMain) {
		$this->injectConfiguration();

		$this->injectUser();

		$this->injectDatabase();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange('Command', $this->getChangeId());

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_cmdmap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processCmdmap_afterFinish(t3lib_TCEmain $tceMain) {
		$this->injectConfiguration();

		$this->injectUser();

		$this->injectDatabase();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL, NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}

	/**
	 * process_datamap entry point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_beforeStart(t3lib_TCEmain $tceMain) {
		$this->injectConfiguration();

		$this->injectUser();

		$this->injectDatabase();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange('Data', $this->getChangeId());

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_datamap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_afterAllOperations(t3lib_TCEmain $tceMain) {
		$this->injectConfiguration();

		$this->injectUser();

		$this->injectDatabase();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL, NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}

	protected function getChangeId() {
		$i = 0;

		do {
			$changeId = sprintf(Tx_Dbmigrate_Configuration::$changeIdFormat, $i);

			$filePathCommand = $this->getChangeFilePath($changeId, 'Command');

			$filePathData = $this->getChangeFilePath($changeId, 'Data');

			$i++;
		} while(file_exists($filePathCommand) || file_exists($filePathData));

		return $changeId;
	}

	protected function getChangeFilePath($changeId, $changeType) {
		$date = date('Ymd');
		$username = $this->user->getUserName();

		$replacePairs = array(
			'%date%' => $date,
			'%username%' => $username,
			'%changeId%' => $changeId,
			'%changeType%' => $changeType,
		);

		return $this->configuration->getChangeFilePath($replacePairs);
	}
}
?>
<?php
class Tx_Dbmigrate_Database_TceMainTransactionHandler implements t3lib_Singleton {

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

	protected $changeIdFormat = '%04d';

	protected $changeScriptFilenamePattern = '%s-%s-%s.sql';

	protected $changeScriptPath = 'Resources/Public/Migrations/';

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
		$this->injectUser();

		$this->injectDatabase();

		if ($this->user->isLoggingEnabled()) {
			$this->user->setUserConfiguration('dbmigrate:change:type', 'Command');
			$this->user->setUserConfiguration('dbmigrate:change:id', $this->getChangeId());

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_cmdmap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processCmdmap_afterFinish(t3lib_TCEmain $tceMain) {
		$this->injectUser();

		$this->injectDatabase();

		if ($this->user->isLoggingEnabled()) {
// 			$changeType = $this->user->getUserConfiguration('dmigrate:change:type', NULL);
// 			$changeId = $this->user->getUserConfiguration('dbmigrate:change:id', NULL);

			$this->user->setUserConfiguration('dbmigrate:change:type', NULL);
			$this->user->setUserConfiguration('dbmigrate:change:id', NULL);

			$this->db->store_lastBuiltQuery = FALSE;

// 			$this->finishChange($changeType, $changeId);
		}
	}

	/**
	 * process_datamap entry point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_beforeStart(t3lib_TCEmain $tceMain) {
		$this->injectUser();

		$this->injectDatabase();

		if ($this->user->isLoggingEnabled()) {
			$this->user->setUserConfiguration('dbmigrate:change:type', 'Data');
			$this->user->setUserConfiguration('dbmigrate:change:id', $this->getChangeId());

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_datamap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_afterAllOperations(t3lib_TCEmain $tceMain) {
		$this->injectUser();

		$this->injectDatabase();

		if ($this->user->isLoggingEnabled()) {
			$this->user->setUserConfiguration('dbmigrate:change:type', NULL);
			$this->user->setUserConfiguration('dbmigrate:change:id', NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}

	protected function getChangeId() {
		$i = 0;

		do {
			$changeId = sprintf($this->changeIdFormat, $i);

			$filePathCommand = $this->getChangeScriptPath($changeId, 'Command');

			$filePathData = $this->getChangeScriptPath($changeId, 'Data');

			$i++;
		} while(file_exists($filePathCommand) || file_exists($filePathData));

		return $changeId;
	}

	protected function getChangeScriptPath($changeId, $changeType) {
		$date = date('Ymd');

		$fileName = sprintf($this->changeScriptFilenamePattern, $date, $changeId, $changeType);
		return t3lib_extMgm::extPath('dbmigrate', $this->changeScriptPath . $fileName);
	}

// 	protected function finishChange($changeType, $changeId) {

// 	}
}
?>
<?php
class Tx_Dbmigrate_Backend_User implements t3lib_Singleton {

	/**
	 * 
	 * @var Tx_Dbmigrate_Configuration
	 */
	protected $configuration = NULL;

	public function injectConfiguration(Tx_Dbmigrate_Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getSessionData($key, $default) {
		$sessionData = $GLOBALS['BE_USER']->getSessionData($key);

		if (TRUE === isset($sessionData)) {
			$value = $sessionData;
		} else {
			$value = $default;
		}

		return $value;
	}

	public function setSessionData($key, $value) {
		$GLOBALS['BE_USER']->setAndSaveSessionData($key, $value);
	}

	public function getUserName() {
		return $GLOBALS['BE_USER']->user['username'];
	}

	public function getRealName() {
		return $GLOBALS['BE_USER']->user['realName'];
	}

	public function getChangeId() {
		return $this->getSessionData('dbmigrate:change:id', NULL);
	}

	public function setChangeId($changeId) {
		$this->setSessionData('dbmigrate:change:id', $changeId);
	}

	public function getChangeType() {
		return $this->getSessionData('dbmigrate:change:type', NULL);
	}

	public function setChangeType($changeType) {
		$this->setSessionData('dbmigrate:change:type', $changeType);
	}

	public function setChange($changeType, $changeId) {
		$this->setChangeType($changeType);
		$this->setChangeId($changeId);
	}

	public function getNextFreeChangeId() {
		$replacePairs = array(
			'%username%' => $this->getUserName(),
		);

		$i = 0;

		do {
			$changeId = sprintf(Tx_Dbmigrate_Configuration::$changeIdFormat, $i);

			$replacePairs['%changeId%'] = $changeId;
			$replacePairs['%changeType%'] = 'Command';

			$filePathCommand = $this->configuration->getChangeFilePath($replacePairs);

			$replacePairs['%changeType%'] = 'Data';

			$filePathData = $this->configuration->getChangeFilePath($replacePairs);

			$i++;
		} while(file_exists($filePathCommand) || file_exists($filePathData));

		return $changeId;
	}

	public function hasActiveChange() {
		$hasChangeId = FALSE === is_null($this->getChangeId());
		$hasChangeType = FALSE === is_null($this->getChangeType());

		return $hasChangeId && $hasChangeType;
	}

	public function getActiveChangeFilePath() {
		if (FALSE === $this->hasActiveChange()) {
			throw new Exception('The user has no active change!', 1364321013);
		}

		$replacePairs = array(
			'%username%' => $this->getUserName(),
			'%changeId%' => $this->getChangeId(),
			'%changeType%' => $this->getChangeType(),
		);

		return $this->configuration->getChangeFilePath($replacePairs);
	}

	public function getNumberOfUncommittedChanges() {
		$fileNamePattern = '/^(.*)' . $this->getUserName() . '(.*)$/';
		$filePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Configuration::$changePath);
		$files = t3lib_div::getFilesInDir($filePath, 'sql', FALSE);

		$count = 0;

		foreach ($files as $file) {
			if (0 === preg_match($fileNamePattern, $file)) {
				continue;
			}

			$count++;
		}

		return $count;
	}
}
?>
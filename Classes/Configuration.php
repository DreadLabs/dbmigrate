<?php
class Tx_Dbmigrate_Configuration implements t3lib_Singleton {

	protected $configuration = array();

	public static $changeFileNameFormat = '%date%-%username%-%changeId%-%changeType%.sql';

	public static $changeIdFormat = '%04d';

	public static $changePath = 'Resources/Public/Migrations/';

	public function __construct() {
		$this->configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate'];
	}

	public function isMonitoringEnabled() {
		return (bool) $this->configuration['monitoringEnabled'];
	}

	public function isTableExisting($tableName) {
		return isset($this->configuration['monitoringTables'][$tableName]);
	}

	public function isTableActive($tableName) {
		$isTableExisting = $this->isTableExisting($tableName);
		$isTableActive = $this->configuration['monitoringTables'][$tableName]['active'];

		return $isTableExisting && $isTableActive;
	}

	public function getChangeFilePath($replacePairs) {
		$filePath = strtr(self::$changeFileNameFormat, $replacePairs);

		return t3lib_extMgm::extPath('dbmigrate', self::$changePath . $filePath);
	}

	public function getNextFreeChangeIdForUser($username) {
		$replacePairs = array(
			'%date%' => date('Ymd'),
			'%username%' => $username,
		);

		$i = 0;

		do {
			$changeId = sprintf(self::$changeIdFormat, $i);

			$replacePairs['%changeId%'] = $changeId;
			$replacePairs['%changeType%'] = 'Command';

			$filePathCommand = $this->getChangeFilePath($replacePairs);

			$replacePairs['%changeType%'] = 'Data';

			$filePathData = $this->getChangeFilePath($replacePairs);

			$i++;
		} while(file_exists($filePathCommand) || file_exists($filePathData));

		return $changeId;
	}
}
?>
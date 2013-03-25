<?php
class Tx_Dbmigrate_Configuration implements t3lib_Singleton {

	public static $defaultTables = array(
		'backend_layout',
		'be_groups',
		'be_users',
		'fe_groups',
		'fe_users',
		'pages',
		'pages_language_overlay',
		'sys_action',
		'sys_action_asgr_mm',
		'sys_category',
		'sys_category_record_mm',
		'sys_collection',
		'sys_collection_entries',
		'sys_domain',
		'sys_file',
		'sys_filemounts',
		'sys_file_collection',
		'sys_file_reference',
		'sys_history',
		'sys_language',
		'sys_news',
		'sys_note',
		'sys_refindex',
		'sys_registry',
		'sys_template',
		'sys_workspace',
		'sys_workspace_stage',
		'tt_content',
		'tx_rsaauth_keys',
		'tx_rtehtmlarea_acronym',
		'tx_scheduler_task',
	);

	public static $changeFileNameFormat = '%date%-%username%-%changeId%-%changeType%.sql';

	public static $changeIdFormat = '%04d';

	public static $changePath = 'Resources/Public/Migrations/';

	protected $configuration = array();

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

	public function getAdditionalTables() {
		$tables = $this->configuration['monitoringTables'];

		$additionalTables = array();

		foreach ($tables as $table => $tableConfiguration) {
			if (TRUE === in_array($table, self::$defaultTables)) {
				continue;
			}

			$additionalTables[] = $table;
		}

		return $additionalTables;
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
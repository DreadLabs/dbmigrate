<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate'] = array(
	'monitoringEnabled' => TRUE,
	'monitoringTables' => array(
		'backend_layout' => array(
			'active' => TRUE,
		),
		'be_groups' => array(
			'active' => TRUE,
		),
		'be_users' => array(
			'active' => TRUE,
			'blacklist' => array(
				// Ignore changes on user configuration field as this gets updated
				// on certain actions in the backend and the user doesn't know this,
				// but would get a commit wizard dialog. User Configuration must be
				// dumped/migrated/deployed separately.
				'uc',
			),
		),
		'fe_groups' => array(
			'active' => TRUE,
		),
		'fe_users' => array(
			'active' => TRUE,
		),
		'pages' => array(
			'active' => TRUE,
		),
		'pages_language_overlay' => array(
			'active' => TRUE,
		),
		'sys_collection' => array(
			'active' => TRUE,
		),
		'sys_collection_entries' => array(
			'active' => TRUE,
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_collection_entries',
		),
		'sys_domain' => array(
			'active' => TRUE,
		),
		'sys_history' => array(
			'active' => TRUE,
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_history',
		),
		'sys_language' => array(
			'active' => TRUE,
		),
		'sys_refindex' => array(
			'active' => TRUE,
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_refindex',
		),
		'sys_template' => array(
			'active' => TRUE,
		),
		'sys_workspace' => array(
			'active' => TRUE,
		),
		'sys_workspace_stage' => array(
			'active' => TRUE,
		),
		'tt_content' => array(
			'active' => TRUE,
		),
		'tx_rtehtmlarea_acronym' => array(
			'active' => TRUE,
		),
		'tx_scheduler_task' => array(
			'active' => TRUE,
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.tx_scheduler_task',
		),
	),
);
?>

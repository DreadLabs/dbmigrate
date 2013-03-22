<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate'] = array(
	'loggingEnabled' => TRUE,
	'loggingTables' => array(
		'backend_layout' => array(
			'active' => TRUE,
			'icon' => 'tcarecords-backend_layout-default',
		),
		'be_groups' => array(
			'active' => TRUE,
			'icon' => 'status-user-group-backend',
		),
		'be_users' => array(
			'active' => TRUE,
			'icon' => 'status-user-backend',
		),
		'fe_groups' => array(
			'active' => TRUE,
			'icon' => 'status-user-group-frontend',
		),
		'fe_users' => array(
			'active' => TRUE,
			'icon' => 'status-user-frontend',
		),
		'pages' => array(
			'active' => TRUE,
			'icon' => 'apps-pagetree-page-default',
		),
		'pages_language_overlay' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-content-page-language-overlay',
		),
		'sys_collection' => array(
			'active' => TRUE,
			'icon' => 'apps-clipboard-list', 
		),
		'sys_collection_entries' => array(
			'active' => TRUE,
			'icon' => 'apps-clipboard-list',
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_collection_entries',
		),
		'sys_domain' => array(
			'active' => TRUE,
			'icon' => 'apps-pagetree-page-domain',
		),
		'sys_history' => array(
			'active' => TRUE,
			'icon' => 'actions-document-history-open',
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_history',
		),
		'sys_language' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-sys_language',
		),
		'sys_refindex' => array(
			'active' => TRUE,
			'icon' => 'extensions-dbmigrate-table-sys_refindex',
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.sys_refindex',
		),
		'sys_template' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-content-template',
		),
		'sys_workspace' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-sys_workspace',
		),
		'sys_workspace_stage' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-sys_workspace',
		),
		'tt_content' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-content-text',
		),
		'tx_rtehtmlarea_acronym' => array(
			'active' => TRUE,
			'icon' => 'extensions-dbmigrate-table-tx_rtehtmlarea_acronym', 
		),
		'tx_scheduler_task' => array(
			'active' => TRUE,
			'icon' => 'extensions-dbmigrate-table-tx_scheduler_task' ,
			'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.item.title.tx_scheduler_task',
		),
	),
);
?>
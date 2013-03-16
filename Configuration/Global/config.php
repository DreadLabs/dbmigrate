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
			'active' => FALSE,
			'icon' => '', 
		),
		'sys_collection_entries' => array(
			'active' => FALSE,
			'icon' => '',
		),
		'sys_domain' => array(
			'active' => TRUE,
			'icon' => 'apps-pagetree-page-domain',
		),
		'sys_history' => array(
			'active' => TRUE,
			'icon' => 'actions-document-history-open',
		),
		'sys_language' => array(
			'active' => TRUE,
			'icon' => 'mimetypes-x-sys_language',
		),
		'sys_refindex' => array(
			'active' => FALSE,
			'icon' => '', 
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
			'active' => FALSE,
			'icon' => '', 
		),
		'tx_scheduler_task' => array(
			'active' => FALSE,
			'icon' => '' 
		),
	),
);
?>
<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

Tx_Dbmigrate_Utility_ExtensionManagement::loadConfiguration();

if (TYPO3_MODE == 'BE') {
	Tx_Dbmigrate_Utility_ExtensionManagement::addAjaxControllers();

	Tx_Dbmigrate_Utility_ExtensionManagement::addToolbarItem($_EXTKEY);

	Tx_Dbmigrate_Utility_ExtensionManagement::addTCEMainHooks($_EXTKEY);

	Tx_Dbmigrate_Utility_ExtensionManagement::addQueryProcessors();

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['sys_action']['Tx_Dbmigrate_Task_RepositoryManager'] = array(
		'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:task.title',
		'description' => 'Provides repository maintenance tasks which should be used as a back log by editors.',
		'icon' => 'EXT:dbmigrate/Resources/Public/Images/database.png'
	);
}
?>
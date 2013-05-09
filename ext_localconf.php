<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

\DreadLabs\Dbmigrate\Utility\ExtensionManagement::loadConfiguration();

if (TYPO3_MODE == 'BE') {
	\DreadLabs\Dbmigrate\Utility\ExtensionManagement::addAjaxControllers();

	\DreadLabs\Dbmigrate\Utility\ExtensionManagement::addToolbarItem($_EXTKEY);

	//\DreadLabs\Dbmigrate\Utility\ExtensionManagement::addTCEMainHooks($_EXTKEY);

	\DreadLabs\Dbmigrate\Utility\ExtensionManagement::addQueryProcessors();

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['sys_action']['DreadLabs\\Dbmigrate\\Task\\RepositoryManager'] = array(
		'title' => 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:task.title',
		'description' => 'Provides repository maintenance tasks which should be used as a back log by editors.',
		'icon' => 'EXT:dbmigrate/Resources/Public/Images/database.png'
	);
}
?>
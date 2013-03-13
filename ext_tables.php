<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_dbmigrate::enable'] = 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->enable';
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_dbmigrate::disable'] = 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->disable';
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_dbmigrate::toggle_table'] = 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->toggleTable';

	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath($_EXTKEY, 'Classes/Backend/Toolbar.php');
}

// -- styles & icons
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['name'] = 'dbmigrate';
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['stylesheetDirectories']['structure'] = 'EXT:' . ($_EXTKEY) . '/Resources/Public/Css/';
//$GLOBALS['TBE_STYLES']['stylesheets']['dbmigrate'] = t3lib_extMgm::siteRelPath('dbmigrate') . 'Resources/Public/Css/dbmigrate.css';

$dbmigrateIcons = array(
	'database' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/database.png'
);

t3lib_SpriteManager::addSingleIcons($dbmigrateIcons, 'dbmigrate');
?>
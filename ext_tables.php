<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Static', 'dbmigrate');

// -- styles & icons
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['name'] = 'dbmigrate';
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['stylesheetDirectories']['structure'] = 'EXT:' . ($_EXTKEY) . '/Resources/Public/Css/';
//$GLOBALS['TBE_STYLES']['stylesheets']['dbmigrate'] = t3lib_extMgm::siteRelPath('dbmigrate') . 'Resources/Public/Css/dbmigrate.css';

$dbmigrateIcons = array(
	'database' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/database.png'
);

t3lib_SpriteManager::addSingleIcons($dbmigrateIcons, 'dbmigrate');
?>
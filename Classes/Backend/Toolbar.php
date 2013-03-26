<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke (tommy@van-tomas.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Toolbar.php
 *
 * Provides the backend toolbar item & toolbar item menu
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * Provides the backend toolbar item & toolbar item menu
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Backend_Toolbar implements backend_toolbarItem {

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $toolbarItemTemplate = '<a href="#" class="toolbar-item">%icon%</a>';

	protected static $toolbarItemMenuStart = '<ul class="toolbar-item-menu" style="display: none;">';

	protected static $toolbarItemMenuEnd = '</ul>';

	protected static $toolbarItemMenuItemTemplate = '<li class="no-link">%icon% %title%</li>';

	protected static $toolbarItemMenuItemLinkTemplate = '<li><a href="%href%">%icon% %title%</a></li>';

	protected static $taskActionLinkTemplate = '/typo3/mod.php?M=user_task&SET[function]=sys_action.Tx_Dbmigrate_Task_RepositoryManager&select=%select%';

	protected static $toolbarItemMenuItemDividerTemplate = '<li class="divider">%title%</li>';

	protected $toolbarItemMenu = array();

	/**
	 * reference back to the backend object
	 *
	 * @var	TYPO3backend
	 */
	protected $backendReference;

	/**
	 *
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $user = NULL;

	/**
	 * 
	 * @var Tx_Dbmigrate_Domain_Repository_ChangeRepository
	 */
	protected $changeRepository = NULL;

	/**
	 * constructor that receives a back reference to the backend
	 *
	 * @param	TYPO3backend	TYPO3 backend object reference
	 */
	public function __construct(TYPO3backend &$backendReference = NULL) {
		$this->backendReference = $backendReference;

		$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');

		$this->changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');
		$this->changeRepository->injectUser($this->user);
	}

	/**
	 * checks whether the user has access to this toolbar item
	 *
	 * @return  boolean  TRUE if user has access, FALSE if not
	 */
	public function checkAccess() {
		return TRUE;
	}

	/**
	 * renders the toolbar item
	 *
	 * @return	string	the toolbar item rendered as HTML string
	 */
	public function render() {
		$this->addJavascriptToBackend();

		$this->addToolbarItem();

		$this->addToolbarItemMenu();

		return implode(LF, $this->toolbarItemMenu);
	}

	/**
	 * returns additional attributes for the list item in the toolbar
	 *
	 * @return	string		list item HTML attibutes
	 */
	public function getAdditionalAttributes() {
		return ' id="tx-dbmigrate-menu"';
	}

	/**
	 * adds the neccessary javascript ot the backend
	 *
	 * @return	void
	 */
	protected function addJavascriptToBackend() {
		$this->backendReference->addJavascriptFile(
			t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Javascript/tx_dbmigrate.js'
		);
	}

	protected function addToolbarItem() {
		$icon = t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database', array(
			'title' => $this->getTranslation('toolbar.item.title')
		));

		$replacePairs = array(
			'%icon%' => $icon,
		);

		$this->toolbarItemMenu[] = strtr(self::$toolbarItemTemplate, $replacePairs);
	}

	protected function getTranslation($key) {
		return $GLOBALS['LANG']->sL(self::$translationCatalogue . ':' . $key, TRUE);
	}

	protected function addToolbarItemMenu() {
		$this->toolbarItemMenu[] = self::$toolbarItemMenuStart;

		$informationalItems = $this->getInformationalMenuItems();
		$this->addToolbarItemMenuItems($informationalItems);

		$this->addToolbarItemMenuDivider();

		$taskActionItems = $this->getTaskActionMenuItems();
		$this->addToolbarItemMenuItems($taskActionItems);

		$this->toolbarItemMenu[] = self::$toolbarItemMenuEnd;
	}

	protected function getInformationalMenuItems() {
		$items = array();

		$replacePairs = array(
			'%count%' => '<span id="dbmigrate-count-uncommitedchanges">' . $this->changeRepository->countAllByUser() . '</span>',
		);

		$items[] = array(
			'icon' => t3lib_iconWorks::getSpriteIcon('status-dialog-information'),
			'title' => strtr($this->getTranslation('toolbar.item.menu.informational.uncommitedchanges'), $replacePairs),
		);

		return $items;
	}

	protected function addToolbarItemMenuItems($items) {
		foreach ($items as $_ => $item) {
			$itemHasLink = TRUE === isset($item['href']);

			$replacePairs = array(
				'%href%' => $itemHasLink ? $item['href'] : '',
				'%icon%' => $item['icon'],
				'%title%' => $item['title'],
			);

			$template = self::$toolbarItemMenuItemTemplate;

			if ($itemHasLink) {
				$template = self::$toolbarItemMenuItemLinkTemplate;
			}

			$this->toolbarItemMenu[] = strtr($template, $replacePairs);
		}
	}

	protected function addToolbarItemMenuDivider() {
		$replacePairs = array(
			'%title%' => $this->getTranslation('toolbar.item.menu.divider'),
		);

		$this->toolbarItemMenu[] = strtr(self::$toolbarItemMenuItemDividerTemplate, $replacePairs);
	}

	protected function getTaskActionMenuItems() {
		$items = array();

		$items[] = $this->getTaskActionMenuItem('commit');

		$items[] = $this->getTaskActionMenuItem('pull');

		$items[] = $this->getTaskActionMenuItem('review');

		return $items;
	}

	protected function getTaskActionMenuItem($action) {
		$item = array(
			'href' => str_replace('%select%', $action, self::$taskActionLinkTemplate),
			'icon' => t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database-' . $action),
			'title' => $this->getTranslation('toolbar.item.menu.action.' . $action),
		);

		return $item;
	}
}

if (TYPO3_MODE == 'BE') {
	/* @var $_backend TYPO3backend */
	$GLOBALS['TYPO3backend']->addToolbarItem('dbmigrate', 'Tx_Dbmigrate_Backend_Toolbar');
}
?>
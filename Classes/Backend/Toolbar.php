<?php
class Tx_Dbmigrate_Backend_Toolbar implements backend_toolbarItem {

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $toolbarItemMenuStart = '<ul class="toolbar-item-menu" style="display: none;">';

	protected static $toolbarItemMenuEnd = '</ul>';

	protected static $toolbarItemMenuItemTemplate = '<li class="no-link">%icon% %title%</li>';

	protected static $toolbarItemMenuItemLinkTemplate = '<li><a href="%href%">%icon% %title%</a></li>';

	protected $EXTKEY = 'dbmigrate';

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
	 * constructor that receives a back reference to the backend
	 *
	 * @param	TYPO3backend	TYPO3 backend object reference
	 */
	public function __construct(TYPO3backend &$backendReference = NULL) {
		$this->backendReference = $backendReference;

		$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');
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
			t3lib_extMgm::extRelPath($this->EXTKEY) . 'Resources/Public/Javascript/tx_dbmigrate.js'
		);
	}

	protected function addToolbarItem() {
		$title = $this->getTranslation('toolbar.item.title');
		$icon = t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database', array(
			'title' => $title
		));

		$this->toolbarItemMenu[] = '<a href="#" class="toolbar-item">' . $icon . '</a>';
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
			'%count%' => '<span id="dbmigrate-count-uncommitedchanges">' . $this->user->getNumberOfUncommittedChanges() . '</span>',
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
		$sectionSubtitle = $this->getTranslation('toolbar.item.menu.divider');

		$divider = array();

		$divider[] = '<li class="divider">&nbsp;</li>';
		$divider[] = '<li class="header">' . $sectionSubtitle . '</li>';
		$divider[] = '<li class="divider">&nbsp;</li>';

		$this->toolbarItemMenu[] = implode(LF, $divider);
	}

	protected function getTaskActionMenuItems() {
		$items = array();

		$items[] = array(
			'href' => '/typo3/mod.php?M=user_task&SET[function]=sys_action.Tx_Dbmigrate_Task_RepositoryManager&select=commit',
			'icon' => t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database-commit'),
			'title' => $this->getTranslation('toolbar.item.menu.action.commit'),
		);

		$items[] = array(
			'href' => '/typo3/mod.php?M=user_task&SET[function]=sys_action.Tx_Dbmigrate_Task_RepositoryManager&select=pull',
			'icon' => t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database-pull'),
			'title' => $this->getTranslation('toolbar.item.menu.action.pull'),
		);

		$items[] = array(
			'href' => '/typo3/mod.php?M=user_task&SET[function]=sys_action.Tx_Dbmigrate_Task_RepositoryManager&select=review',
			'icon' => t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database-review'),
			'title' => $this->getTranslation('toolbar.item.menu.action.review'),
		);

		return $items;
	}
}

if (TYPO3_MODE == 'BE') {
	/* @var $_backend TYPO3backend */
	$GLOBALS['TYPO3backend']->addToolbarItem('dbmigrate', 'Tx_Dbmigrate_Backend_Toolbar');
}
?>
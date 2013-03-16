<?php
class Tx_Dbmigrate_Backend_Toolbar implements backend_toolbarItem {

	protected $EXTKEY = 'dbmigrate';

	protected $toolbarItemMenu = array();

	/**
	 * reference back to the backend object
	 *
	 * @var	TYPO3backend
	 */
	protected $backendReference;

	protected static $ajaxUrlTemplate = 'ajax.php?ajaxID=%s%s';

	protected static $toolbarItemMenuStart = '<ul class="toolbar-item-menu" style="display: none;">';

	protected static $toolbarItemMenuEnd = '</ul>';

	protected static $toolbarItemMenuItemTemplate = '<li data-visible-if="%visible-if%"><a href="%href%">%icon% %title%</a></li>';

	protected $allowedTables = array();

	/**
	 * constructor that receives a back reference to the backend
	 *
	 * @param	TYPO3backend	TYPO3 backend object reference
	 */
	public function __construct(TYPO3backend &$backendReference = NULL) {
		$this->backendReference = $backendReference;

		$globalExtensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate'];
		$tableConfigurations = $globalExtensionConfiguration['loggingTables'];

		foreach ($tableConfigurations as $tableName => $tableConfiguration) {
			if (FALSE === $tableConfiguration['active']) {
				continue;
			}

			$this->allowedTables[$tableName] = $tableConfiguration['icon'];
		}
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
		$title = $GLOBALS['LANG']->sL('LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.title', TRUE);
		$icon = t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database', array(
			'title' => $title
		));

		$this->toolbarItemMenu[] = '<a href="#" class="toolbar-item">' . $icon . '</a>';
	}

	protected function addToolbarItemMenu() {
		$this->toolbarItemMenu[] = self::$toolbarItemMenuStart;

		$controlItems = $this->getControlMenuItems();
		$this->addToolbarItemMenuItems($controlItems);

		$this->addToolbarItemMenuDivider();

		$tableItems = $this->getTableMenuItems();
		$this->addToolbarItemMenuItems($tableItems);

		$this->toolbarItemMenu[] = self::$toolbarItemMenuEnd;
	}

	protected function getControlMenuItems() {
		$actions = array();

		$actions[] = array(
			'href' => $this->buildAjaxUrl('tx_dbmigrate::enable_logging'),
			'icon' => t3lib_iconWorks::getSpriteIcon('actions-edit-hide'),
			'title' => $GLOBALS['LANG']->sL('LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.control.enable_logging', TRUE),
			'visible-if' => 'tx_dbmigrate::is_logging_disabled',
		);

		$actions[] = array(
			'href' => $this->buildAjaxUrl('tx_dbmigrate::disable_logging'),
			'icon' => t3lib_iconWorks::getSpriteIcon('actions-edit-unhide'),
			'title' => $GLOBALS['LANG']->sL('LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.control.disable_logging', TRUE),
			'visible-if' => 'tx_dbmigrate::is_logging_enabled',
		);

		return $actions;

	}

	protected function addToolbarItemMenuItems($items) {
		foreach ($items as $_ => $item) {
			$replacePairs = array(
				'%href%' => $item['href'],
				'%icon%' => $item['icon'],
				'%title%' => $item['title'],
				'%visible-if%' => $item['visible-if'],
			);

			$this->toolbarItemMenu[] = strtr(self::$toolbarItemMenuItemTemplate, $replacePairs);
		}
	}

	protected function addToolbarItemMenuDivider() {
		$sectionSubtitle = $GLOBALS['LANG']->sL('LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.divider', TRUE);

		$divider = array();

		$divider[] = '<li class="divider">&nbsp;</li>';
		$divider[] = '<li class="header">' . $sectionSubtitle . '</li>';
		$divider[] = '<li class="divider">&nbsp;</li>';

		$this->toolbarItemMenu[] = implode(LF, $divider);
	}

	protected function getTableMenuItems() {
		$items = array();

		foreach ($this->allowedTables as $table => $icon) {
			$items[] = $this->getTableMenuItem($table, $icon);
		}

		return $items;
	}

	protected function getTableMenuItem($table, $icon) {
		$tableExistsInTCA = TRUE === isset($GLOBALS['TCA'][$table]);
		$tableItemIsForced = TRUE === $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate']['loggingTables'][$table]['force'];
		$dontUseMissingTableItem = $tableExistsInTCA || $tableItemIsForced;

		$missingTableItem = array(
			'href' => '#',
			'icon' => t3lib_iconWorks::getSpriteIcon('status-status-icon-missing'),
			'title' => sprintf($GLOBALS['LANG']->sL('LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml:toolbar.item.menu.table_missing'), $table),
			'visible-if' => '',
		);

		$item = $missingTableItem;

		if ($tableExistsInTCA) {
			$titleReference = $GLOBALS['TCA'][$table]['ctrl']['title'];
		} else if ($tableItemIsForced) {
			$titleReference = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate']['loggingTables'][$table]['title'];
		}

		if ($dontUseMissingTableItem) {
			$title = $GLOBALS['LANG']->sL($titleReference, TRUE);
			$item = $this->buildTableMenuItem($table, $icon, $title);
		}

		return $item;
	}

	protected function buildTableMenuItem($table, $icon, $title) {
		$item = array(
			'href' => $this->buildAjaxUrl('tx_dbmigrate::toggle_table', array('table' => $table)),
			'icon' => t3lib_iconWorks::getSpriteIcon($icon),
			'title' => $title ? $title : $table,
			'visible-if' => 'tx_dbmigrate::is_table_active&table=' . $table .'&icon=' . $icon,
		);

		return $item;
	}

	protected function buildAjaxUrl($ajaxId, $additionalParams = array()) {
		$additionalQueryString = '';

		foreach ($additionalParams as $paramKey => $paramValue) {
			$additionalQueryString .= '&' . $paramKey . '=' . urlencode($paramValue);
		}

		return sprintf(self::$ajaxUrlTemplate, $ajaxId, $additionalQueryString);
	}
}

if (TYPO3_MODE == 'BE') {
	/* @var $_backend TYPO3backend */
	$GLOBALS['TYPO3backend']->addToolbarItem('dbmigrate', 'Tx_Dbmigrate_Backend_Toolbar');
}
?>
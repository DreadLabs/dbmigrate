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

	protected static $TOOLBAR_ITEM_MENU_START = '<ul class="toolbar-item-menu" style="display: none;">';

	protected static $TOOLBAR_ITEM_MENU_END = '</ul>';

	protected static $TOOLBAR_ITEM_MENU_ITEM_TEMPLATE = '<li data-visible-if="%visible-if%"><a href="%href%">%icon% %title%</a></li>';

	protected $allowedTables = array(
		'backend_layout' => 'tcarecords-backend_layout-default',
		'be_groups' => 'status-user-group-backend',
		'be_users' => 'status-user-backend',
		'fe_groups' => 'status-user-group-frontend',
		'fe_users' => 'status-user-frontend',
		'pages' => 'apps-pagetree-page-default',
		'pages_language_overlay' => 'mimetypes-x-content-page-language-overlay',
#		'sys_collection',
#		'sys_collection_entries',
		'sys_domain' => 'apps-pagetree-page-domain',
		'sys_history' => 'actions-document-history-open',
		'sys_language' => 'mimetypes-x-sys_language',
#		'sys_refindex' => '',
		'sys_template' => 'mimetypes-x-content-template',
		'sys_workspace' => 'mimetypes-x-sys_workspace',
		'sys_workspace_stage' => 'mimetypes-x-sys_workspace',
		'tt_content' => 'mimetypes-x-content-text',
#		'tx_cscounterplus_info',
#		'tx_fed_domain_model_datasource' => '',
#		'tx_form4_pages_rss_feed' => '',
#		'tx_gridelements_backend_layout' => '',
#		'tx_realurl_redirects' => '',
#		'tx_realurl_uniqalias' => '',
#		'tx_rsaauth_keys' => '',
#		'tx_rtehtmlarea_acronym' => '',
		//'tx_scheduler_task' => ''
	);

	/**
	 * constructor that receives a back reference to the backend
	 *
	 * @param	TYPO3backend	TYPO3 backend object reference
	 */
	public function __construct(TYPO3backend &$backendReference = NULL) {
		$this->backendReference = $backendReference;
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
		$title = 'dbmigrate'; //$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:rm.clearCache_clearCache', TRUE);
		$icon = t3lib_iconWorks::getSpriteIcon('extensions-dbmigrate-database', array(
			'title' => $title
		));

		$this->toolbarItemMenu[] = '<a href="#" class="toolbar-item">' . $icon . '</a>';
	}

	protected function addToolbarItemMenu() {
		$this->toolbarItemMenu[] = self::$TOOLBAR_ITEM_MENU_START;

		$controlItems = $this->getControlMenuItems();
		$this->addToolbarItemMenuItems($controlItems);

		$this->addToolbarItemMenuDivider();

		$tableItems = $this->getTableMenuItems();
		$this->addToolbarItemMenuItems($tableItems);

		$this->toolbarItemMenu[] = self::$TOOLBAR_ITEM_MENU_END;
	}

	protected function getControlMenuItems() {
		$actions = array();

		$actions[] = array(
			'href' => 'ajax.php?ajaxID=tx_dbmigrate::enable_logging',
			'icon' => t3lib_iconWorks::getSpriteIcon('actions-edit-hide'),
			'title' => 'enable logging',
			'visible-if' => 'tx_dbmigrate::is_logging_disabled',
		);

		$actions[] = array(
			'href' => 'ajax.php?ajaxID=tx_dbmigrate::disable_logging',
			'icon' => t3lib_iconWorks::getSpriteIcon('actions-edit-unhide'),
			'title' => 'disable logging',
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

			$this->toolbarItemMenu[] = strtr(self::$TOOLBAR_ITEM_MENU_ITEM_TEMPLATE, $replacePairs);
		}
	}

	protected function addToolbarItemMenuDivider() {
		$sectionSubtitle = 'Select the tables to log:'; //$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:rm.clearCache_clearCache', TRUE);

		$divider = array();

		$divider[] = '<li class="divider">&nbsp;</li>';
		$divider[] = '<li class="header">' . $sectionSubtitle . '</li>';
		$divider[] = '<li class="divider">&nbsp;</li>';

		$this->toolbarItemMenu[] = implode(LF, $divider);
	}

	protected function getTableMenuItems() {
		$items = array();

		foreach ($this->allowedTables as $table => $icon) {
			$titleReference = $GLOBALS['TCA'][$table]['ctrl']['title'];
			$title = $GLOBALS['LANG']->sL($titleReference, TRUE);

			$items[] = array(
				'href' => 'ajax.php?ajaxID=tx_dbmigrate::toggle_table&table=' . $table,
				'icon' => t3lib_iconWorks::getSpriteIcon($icon),
				'title' => $title ? $title : $table,
				'visible-if' => 'tx_dbmigrate::is_table_active&table=' . $table .'&icon=' . $icon,
			);
		}

		return $items;
	}
}

if (TYPO3_MODE == 'BE') {
	/* @var $_backend TYPO3backend */
	$GLOBALS['TYPO3backend']->addToolbarItem('dbmigrate', 'Tx_Dbmigrate_Backend_Toolbar');
}
?>
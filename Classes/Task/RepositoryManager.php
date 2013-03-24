<?php
class Tx_Dbmigrate_Task_RepositoryManager implements tx_taskcenter_Task {
	protected $taskObject;

	protected $actions = array();

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $actionPath = '/RepositoryManager/Action/';

	protected static $actionItemTemplate = '<li><a href="%url%">%activeWrapStart%%actionName%%activeWrapEnd%<br /><em>%actionDescription%</em></a></li>';

	/**
	 *
	 * @var Tx_Dbmigrate_Task_RepositoryManager_Action_ActionInterface
	 */
	protected $action = NULL;

	public function __construct(SC_mod_user_task_index $taskObject) {
		$this->taskObject = $taskObject;

		$this->getActions();
	}

	protected function getActions() {
		$actions = t3lib_div::getFilesInDir(dirname(__FILE__) . self::$actionPath, 'php', FALSE, 1, '');

		foreach ($actions as $action) {
			$actionName = str_replace('.php', '', $action);
			$actionNameNormalized = strtolower($actionName);
			$isSelectedAction = $actionNameNormalized === t3lib_div::_GP('select');

			$_action = t3lib_div::makeInstance(__CLASS__ . '_Action_' . $actionName);

			if (FALSE === $_action->checkAccess()) {
				continue;
			}

			if ($isSelectedAction) {
				$this->action = $_action;
			}

			$url = 'mod.php?M=user_task&SET[function]=sys_action.' . __CLASS__ . '&select=' . $actionNameNormalized;

			$this->actions[] = array(
				'%url%' => $url,
				'%activeWrapStart%' => $isSelectedAction ? '<strong>' : '',
				'%actionName%' => $actionName,
				'%activeWrapEnd%' => $isSelectedAction ? '</strong>' : '',
				'%actionDescription%' => $this->getTranslation('task.action.' . $actionNameNormalized . '.description'),
			);
		}
	}

	public function getOverview() {
		$content = '<p>' . $this->getTranslation('task.overview') . '</p>';

		$content .= $this->renderActions();

		return $content;
	}

	protected function renderActions() {
		$list = '<ul>';

		foreach ($this->actions as $action) {
			$list .= strtr(self::$actionItemTemplate, $action);
		}

		$list .= '</ul>';

		return $list;
	}

	public function getTask() {
		$content = '<br />';

		if (NULL !== t3lib_div::_GP('select') && NULL === t3lib_div::_GP('submit')) {
			$content .= '<h2 class="uppercase">' . $this->getTranslation('task.header.configure') . ' ' . $this->action->getName() . '</h2>';
			$content .= $this->action->renderForm();
		}

		if (NULL !== t3lib_div::_GP('submit')) {
			$content .= '<h2 class="uppercase">' . $this->getTranslation('task.header.processing') . ' ' . $this->action->getName() . '...</h2>';
			$content .= $this->action->process();
		}

		return $content;
	}

	protected function getTranslation($key) {
		return $GLOBALS['LANG']->sL(self::$translationCatalogue . ':' . $key, TRUE);
	}
}
?>
<?php
namespace DreadLabs\Dbmigrate\Task;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * RepositoryManager.php
 *
 * Gateway to all task center actions for migration/change data handling.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class RepositoryManager implements \TYPO3\CMS\Taskcenter\TaskInterface {

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $actionPath = '/RepositoryManager/Action/';

	protected static $actionItemTemplate = '<li><a href="%url%">%activeWrapStart%%actionName%%activeWrapEnd%<br /><em>%actionDescription%</em></a></li>';

	protected static $overviewTemplate = '<p>%title%</p>%actions%';

	protected static $actionListTemplate = '<ul>%list%</ul>';

	protected static $taskHeaderTemplate = '<h2 class="uppercase">%title%</h2>';

	protected $taskObject;

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Configuration
	 */
	protected $configuration = NULL;

	protected $actions = array();

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Task\RepositoryManager\Action\ActionInterface
	 */
	protected $action = NULL;

	public function __construct(\TYPO3\CMS\Taskcenter\Controller\TaskModuleController $taskObject) {
		$this->taskObject = $taskObject;

		$this->taskObject->doc->getPageRenderer()->addJsFile('../typo3conf/ext/dbmigrate/Resources/Public/Javascript/tx_dbmigrate_task.js');

		$this->initialize();
	}

	public function initialize() {
		$this->initializeConfiguration();

		$this->getActions();
	}

	protected function initializeConfiguration() {
		$this->configuration = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Configuration');
	}

	protected function getActions() {
		$actions = GeneralUtility::getFilesInDir(dirname(__FILE__) . self::$actionPath, 'php', FALSE, 1, '');

		foreach ($actions as $action) {
			$actionName = str_replace('.php', '', $action);
			$actionNameCleaned = strtolower($actionName);
			$actionNameNormalized = str_replace('action', '', $actionNameCleaned);

			$isSelectedAction = $actionNameCleaned === GeneralUtility::_GP('select');

			$_action = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Action\\' . $actionName);

			if (FALSE === $_action->checkAccess()) {
				continue;
			}

			if ($isSelectedAction) {
				$this->action = $_action;
				$this->action->injectConfiguration($this->configuration);
// 				$this->action->initialize();
			}

			$url = 'mod.php?M=user_task&SET[function]=sys_action.' . __CLASS__ . '&select=' . $actionNameCleaned;

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
		$replacePairs = array(
			'%title%' => $this->getTranslation('task.overview'),
			'%actions%' => $this->renderActions(),
		);

		$content = strtr(self::$overviewTemplate, $replacePairs);

		return $content;
	}

	protected function renderActions() {
		$list = '';

		foreach ($this->actions as $action) {
			$list .= strtr(self::$actionItemTemplate, $action);
		}

		$replacePairs = array(
			'%list%' => $list,
		);

		$content = strtr(self::$actionListTemplate, $replacePairs);

		return $content;
	}

	public function getTask() {
		$content = '<br />';

		if (NULL !== GeneralUtility::_GP('select') && NULL === GeneralUtility::_GP('submit')) {
			$content .= $this->getTaskHeader('task.header.configure');
			$content .= $this->action->renderForm();
		}

		if (NULL !== GeneralUtility::_GP('submit')) {
			$content .= $this->getTaskHeader('task.header.processing');
			$content .= $this->action->process();
		}

		return $content;
	}

	protected function getTaskHeader($translationKey) {
		$replacePairs = array(
			'%title%' => $this->getTaskHeaderTitle($translationKey),
		);

		return strtr(self::$taskHeaderTemplate, $replacePairs);
	}

	protected function getTaskHeaderTitle($translationKey) {
		$replacePairs = array(
			'%action%' => $this->action->getName(),
		);

		return strtr($this->getTranslation($translationKey), $replacePairs);
	}

	protected function getTranslation($key) {
		return $GLOBALS['LANG']->sL(self::$translationCatalogue . ':' . $key, TRUE);
	}
}
?>
<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager;

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
 * AbstractAction.php
 *
 * Abstract task center action implementation which encapsulates functionalities which are common to all concrete task center actions.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
abstract class AbstractAction implements \DreadLabs\Dbmigrate\Task\RepositoryManager\ActionInterface {

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $formActionUrlTemplate = 'mod.php?M=user_task&SET[function]=sys_action.%taskClass%&select=%select%&submit=%submit%';

	protected static $optionFieldTemplate = '<label class="tx-dbmigrate-repositorymanager-actionoption"><h3 class="uppercase">%label%</h3>%field%</label><br />';

	protected $options = array();

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Configuration
	 */
	protected $configuration = NULL;

	public function injectConfiguration(\DreadLabs\Dbmigrate\Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getName() {
		$parts = explode('\\', get_class($this));

		return strtolower(array_pop($parts));
	}

	public final function renderForm() {
		$this->getOptions();

		$url = $this->getFormUrl();

		$optionsForm = '<form class="tx-dbmigrate-repositorymanager-action tx-dbmigrate-repositorymanager-action-' . strtolower($this->getName()) . '" action="' . $url . '" method="post">';

		foreach ($this->options as $option) {
			$optionsForm .=  $this->buildOptionField($option);
		}

		$optionsForm .= '<br />';

		$optionsForm .= '<input type="submit" class="tx-dbmigrate-repositorymanager-actionbutton" name="execute" value="' . $this->getTranslation('task.action.submit') . '" />';

		$optionsForm .= '</form>';

		return $optionsForm;
	}

	protected function getFormUrl() {
		$name = $this->getName();

		$replacePairs = array(
			'%taskClass%' => 'DreadLabs\Dbmigrate\Task\RepositoryManager',
			'%select%' => $name,
			'%submit%' => $name,
		);

		$url = strtr(self::$formActionUrlTemplate, $replacePairs);

		return $url;
	}

	protected function buildOptionField($option) {
		$replacePairs = array(
			'%label%' => $option['label'],
			'%field%' => $option['field'],
		);

		$template = self::$optionFieldTemplate;

		$hasOwnTemplate = isset($option['template']) && '' !== $option['template'];

		if ($hasOwnTemplate) {
			$template = $option['template'];
		}

		return strtr($template, $replacePairs);
	}

	protected function getTranslation($key) {
		return $GLOBALS['LANG']->sL(self::$translationCatalogue . ':' . $key, TRUE);
	}

	protected function raiseExceptionUnless($condition, $message) {
		if (FALSE === $condition) {
			throw new \Exception($condition, 1364332797);
		}
	}

	protected function raiseExceptionIf($condition, $message) {
		if (TRUE === $condition) {
			throw new \Exception($message, 1364333374);
		}
	}
}
?>
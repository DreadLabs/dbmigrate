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
 * AbstractAction.php
 *
 * Abstract task center action implementation which encapsulates functionalities which are common to all concrete task center actions.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/ActionInterface.php');

/**
 * Abstract task center action implementation which encapsulates functionalities which are common to all concrete task center actions.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
abstract class Tx_Dbmigrate_Task_RepositoryManager_AbstractAction implements Tx_Dbmigrate_Task_RepositoryManager_Action {

	protected static $translationCatalogue = 'LLL:EXT:dbmigrate/Resources/Private/Language/Backend.xml';

	protected static $optionFieldTemplate = '<label><h3 class="uppercase">%label%</h3>%field%</label><br />';

	protected static $formActionUrlTemplate = 'mod.php?M=user_task&SET[function]=sys_action.%taskClass%&select=%select%&submit=%submit%';

	protected $options = array();

	/**
	 * 
	 * @var Tx_Dbmigrate_Configuration
	 */
	protected $configuration = NULL;

	public function injectConfiguration(Tx_Dbmigrate_Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getName() {
		$parts = explode('_', get_class($this));

		return strtolower(array_pop($parts));
	}

	public final function renderForm() {
		$this->getOptions();

		$url = $this->getFormUrl();

		$optionsForm = '<form action="' . $url . '" method="post">';

		foreach ($this->options as $option) {
			$optionsForm .=  $this->buildOptionField($option['label'], $option['field']);
		}

		$optionsForm .= '<br />';

		$optionsForm .= '<input type="submit" name="execute" value="' . $this->getTranslation('task.action.submit') . '" />';

		$optionsForm .= '</form>';

		return $optionsForm;
	}

	protected function getFormUrl() {
		$name = $this->getName();

		$replacePairs = array(
			'%taskClass%' => 'Tx_Dbmigrate_Task_RepositoryManager',
			'%select%' => $name,
			'%submit%' => $name,
		);

		$url = strtr(self::$formActionUrlTemplate, $replacePairs);

		return $url;
	}

	protected function buildOptionField($label, $field) {
		$replacePairs = array(
			'%label%' => $label,
			'%field%' => $field,
		);
		return strtr(self::$optionFieldTemplate, $replacePairs);
	}

	protected function executeCommand($command, $errorPreface = '') {
		$lastLine = t3lib_utility_Command::exec($command, $output, $exitCode);

		if (0 !== $exitCode) {
			$this->handleErrorCommand($command, $output, $lastLine, $errorPreface);
		}
	}

	protected function handleErrorCommand($command, $output, $lastLine, $prefaceMessage = '') {
		$msg = '';

		if ('' !== $prefaceMessage) {
			$msg .= $prefaceMessage;
		}

		$msg .= '<pre>' . $command . '</pre>';
		$msg .= '<pre>' . $lastLine . '</pre>';
		$msg .= '<pre>' . implode(LF, $output) . '</pre>';

		throw new Exception($msg);
	}

	protected function getTranslation($key) {
		return $GLOBALS['LANG']->sL(self::$translationCatalogue . ':' . $key, TRUE);
	}
}
?>
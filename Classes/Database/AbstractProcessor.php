<?php
namespace DreadLabs\Dbmigrate\Database;

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
use \TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * AbstractProcessor.php
 *
 * Abstract t3lib_DB processor class implements business logic common to all concrete processor implementations.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class AbstractProcessor implements \TYPO3\CMS\Core\SingletonInterface {

	protected $isInitialized = FALSE;

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Configuration
	 */
	protected $configuration = NULL;

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Backend\User
	 */
	protected $user = NULL;

	public function initialize() {
		if (FALSE === $this->isInitialized) {
			$this->initializeConfiguration();

			$this->initializeUser();

			$this->initializeChangeRepository();

			$this->isInitialized = TRUE;
		}
	}

	protected function initializeConfiguration() {
		$this->configuration = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Configuration');
	}

	protected function initializeUser() {
		$this->user = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Backend\\User');
		$this->user->injectConfiguration($this->configuration);
	}

	protected function initializeChangeRepository() {
		$this->changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
		$this->changeRepository->injectUser($this->user);
	}

	protected function isMonitoringEnabled() {
		return $this->configuration->isMonitoringEnabled();
	}

	protected function storeChange($table, $query) {
		try {
			$this->raiseExceptionUnlessTableIsActive($table);

			$lastChangeId = $this->user->getChangeId();

			$this->changeRepository->add($query);

// 			$this->setUpdateSignal($lastChangeId);

			$this->user->clearChange();
		} catch (\Exception $e) {
		}
	}

	protected function raiseExceptionUnlessTableIsActive($table) {
		$raiseException = FALSE === $this->configuration->isTableActive($table);

		if ($raiseException) {
			$msg = sprintf('The query is not loggable for the table %s: Table is not active!', $table);
			throw new \Exception($msg, 1364320214);
		}
	}

	protected function raiseExceptionIfChangeIsBlacklisted($table, $fields) {
		$isBlacklistedChange = FALSE;

		$blacklistEntries = $this->configuration->getTableBlacklist($table);

		foreach ($blacklistEntries as $blacklistEntry) {
			if (TRUE === $isBlacklistedChange) {
				continue;
			}

			$isBlacklistedChange = $this->checkBlacklistedChange($fields, $blacklistEntry);
		}

		if ($isBlacklistedChange) {
			$msg = sprintf('The query is not loggable for the table because of blacklist settings.');
			throw new \Exception($msg, 1366662349);
		}
	}

	protected function checkBlacklistedChange($fields, $blacklistEntry) {
		$blacklistFields = explode(',', $blacklistEntry);

		$numberOfFieldsInChange = count($fields);
		$numberOfFieldsInBlacklistEntry = count($blacklistFields);

		$fieldAmountsAreEqual = $numberOfFieldsInBlacklistEntry === $numberOfFieldsInChange;

		$fieldsInChange = array_keys($fields);

		sort($fieldsInChange);
		sort($blacklistFields);

		$fieldsAreEqual = $fieldsInChange === $blacklistFields;

		return $fieldAmountsAreEqual && $fieldsAreEqual;
	}

	protected function setUpdateSignal($lastChangeId) {
		$moduleData = $GLOBALS['BE_USER']->getModuleData('TYPO3\\CMS\\Backend\\Utility\\BackendUtility::getUpdateSignal', 'ses');

		if (!isset($moduleData['tx_dbmigrate::commitWizard'])) {
			$this->configuration->setMonitoringEnabledOverride(FALSE);

			BackendUtility::setUpdateSignal('tx_dbmigrate::commitWizard', $lastChangeId);

			$this->configuration->setMonitoringEnabledOverride(NULL);
		}
	}
}
?>
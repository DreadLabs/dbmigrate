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

/**
 * TceMainTransactionHandler.php
 *
 * \TYPO3\CMS\Core\DataHandling\DataHandler transaction handler which flags change management settings for the backend user.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class TceMainTransactionHandler implements \TYPO3\CMS\Core\SingletonInterface {

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

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Domain\Repository\ChangeRepository
	 */
	protected $changeRepository = NULL;

	/**
	 *
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db = NULL;

	public function initialize() {
		$this->initializeConfiguration();

		$this->initializeUser();

		$this->initializeChangeRepository();

		$this->initializeDatabase();
	}

	public function initializeConfiguration() {
		$this->configuration = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Configuration');
	}

	public function initializeUser() {
		$this->user = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Backend\\User');
		$this->user->injectConfiguration($this->configuration);
	}

	public function initializeChangeRepository() {
		$this->changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
		$this->changeRepository->injectUser($this->user);
	}

	public function initializeDatabase() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * process_cmdmap entry point hook method
	 *
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tceMain
	 */
	public function processCmdmap_beforeStart(\TYPO3\CMS\Core\DataHandling\DataHandler $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$changeId = $this->changeRepository->findNextFreeChangeOfUser();
			$this->user->setChange($changeId);

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_cmdmap exit point hook method
	 *
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tceMain
	 */
	public function processCmdmap_afterFinish(\TYPO3\CMS\Core\DataHandling\DataHandler $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}

	/**
	 * process_datamap entry point hook method
	 *
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tceMain
	 */
	public function processDatamap_beforeStart(\TYPO3\CMS\Core\DataHandling\DataHandler $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$changeId = $this->changeRepository->findNextFreeChangeOfUser();
			$this->user->setChange($changeId);

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_datamap exit point hook method
	 *
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tceMain
	 */
	public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}
}
?>
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
 * TceMainTransactionHandler.php
 *
 * t3lib_TCEmain transaction handler which flags change management settings for the backend user.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * t3lib_TCEmain transaction handler which flags change management settings for the backend user
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Database_TceMainTransactionHandler implements t3lib_Singleton {

	/**
	 *
	 * @var Tx_Dbmigrate_Configuration
	 */
	protected $configuration = NULL;

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
	 *
	 * @var t3lib_db
	 */
	protected $db = NULL;

	public function initialize() {
		$this->initializeConfiguration();

		$this->initializeUser();

		$this->initializeChangeRepository();

		$this->initializeDatabase();
	}

	public function initializeConfiguration() {
		$this->configuration = t3lib_div::makeInstance('Tx_Dbmigrate_Configuration');
	}

	public function initializeUser() {
		$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');
		$this->user->injectConfiguration($this->configuration);
	}

	public function initializeChangeRepository() {
		$this->changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');
		$this->changeRepository->injectUser($this->user);
	}

	public function initializeDatabase() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * process_cmdmap entry point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processCmdmap_beforeStart(t3lib_TCEmain $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$userName = $this->user->getUserName();
			$changeId = $this->changeRepository->findNextFreeChangeOfUser();
			$this->user->setChange('Command', $changeId);

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_cmdmap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processCmdmap_afterFinish(t3lib_TCEmain $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL, NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}

	/**
	 * process_datamap entry point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_beforeStart(t3lib_TCEmain $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$userName = $this->user->getUserName();
			$changeId = $this->changeRepository->findNextFreeChangeOfUser();
			$this->user->setChange('Data', $changeId);

			$this->db->store_lastBuiltQuery = TRUE;
		}
	}

	/**
	 * process_datamap exit point hook method
	 *
	 * @param t3lib_TCEmain $tceMain
	 */
	public function processDatamap_afterAllOperations(t3lib_TCEmain $tceMain) {
		$this->initialize();

		if ($this->configuration->isMonitoringEnabled()) {
			$this->user->setChange(NULL, NULL);

			$this->db->store_lastBuiltQuery = FALSE;
		}
	}
}
?>
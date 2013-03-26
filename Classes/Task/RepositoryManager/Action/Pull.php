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
 * Pull.php
 *
 * Task center task action for pulling migrations/changes data into the current T3 instance.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

/**
 * Task center task action for pulling migrations/changes data into the current T3 instance.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Task_RepositoryManager_Action_Pull extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	public function checkAccess() {
		// @TODO: Tx_Dbmigrate_Backend_User instance!!!
		return $GLOBALS['BE_USER']->isAdmin();
	}

	public function getOptions() {
	}

	public function process() {
	}
}
?>
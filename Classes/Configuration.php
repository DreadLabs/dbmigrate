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
 * Configuation.php
 *
 * Provides an interface to application wide configuration concerns.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * Provides an interface to application wide configuration concerns.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Configuration implements t3lib_Singleton {

	public static $defaultTables = array(
		'backend_layout',
		'be_groups',
		'be_users',
		'fe_groups',
		'fe_users',
		'pages',
		'pages_language_overlay',
		'sys_action',
		'sys_action_asgr_mm',
		'sys_category',
		'sys_category_record_mm',
		'sys_collection',
		'sys_collection_entries',
		'sys_domain',
		'sys_file',
		'sys_filemounts',
		'sys_file_collection',
		'sys_file_reference',
		'sys_history',
		'sys_language',
		'sys_news',
		'sys_note',
		'sys_refindex',
		'sys_registry',
		'sys_template',
		'sys_workspace',
		'sys_workspace_stage',
		'tt_content',
		'tx_rsaauth_keys',
		'tx_rtehtmlarea_acronym',
		'tx_scheduler_task',
	);

	protected $configuration = array();

	public function __construct() {
		$this->configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbmigrate'];
	}

	public function isMonitoringEnabled() {
		return (bool) $this->configuration['monitoringEnabled'];
	}

	public function isTableExisting($tableName) {
		return isset($this->configuration['monitoringTables'][$tableName]);
	}

	public function isTableActive($tableName) {
		$isTableExisting = $this->isTableExisting($tableName);
		$isTableActive = $this->configuration['monitoringTables'][$tableName]['active'];

		return $isTableExisting && $isTableActive;
	}

	public function getAdditionalTables() {
		$tables = $this->configuration['monitoringTables'];

		$additionalTables = array();

		foreach ($tables as $table => $tableConfiguration) {
			if (TRUE === in_array($table, self::$defaultTables)) {
				continue;
			}

			$additionalTables[] = $table;
		}

		return $additionalTables;
	}
}
?>
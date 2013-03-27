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
 * QueryPreProcessor.php
 *
 * t3lib_DB pre processor implements business logic for different database (DML) queries.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * t3lib_DB pre processor implements business logic for different database (DML) queries.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Database_QueryPreProcessor extends Tx_Dbmigrate_Database_AbstractProcessor implements t3lib_DB_preProcessQueryHook {

	/**
	 * Pre-processor for the INSERTquery method.
	 *
	 * @param string $table Database table name
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function INSERTquery_preProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->initialize();

		$parentObject->store_lastBuiltQuery = $this->isMonitoringEnabled();
	}

	/**
	 * Pre-processor for the INSERTmultipleRows method.
	 * BEWARE: When using DBAL, this hook will not be called at all. Instead,
	 * INSERTquery_preProcessAction() will be invoked for each row.
	 *
	 * @param string $table Database table name
	 * @param array $fields Field names
	 * @param array $rows Table rows
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function INSERTmultipleRows_preProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->initialize();

		$parentObject->store_lastBuiltQuery = $this->isMonitoringEnabled();
	}

	/**
	 * Pre-processor for the UPDATEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function UPDATEquery_preProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->initialize();

		$parentObject->store_lastBuiltQuery = $this->isMonitoringEnabled();
	}

	/**
	 * Pre-processor for the DELETEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function DELETEquery_preProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		$this->initialize();

		$parentObject->store_lastBuiltQuery = $this->isMonitoringEnabled();
	}

	/**
	 * Pre-processor for the TRUNCATEquery method.
	 *
	 * @param string $table Database table name
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function TRUNCATEquery_preProcessAction(&$table, t3lib_DB $parentObject) {
		$this->initialize();

		$parentObject->store_lastBuiltQuery = $this->isMonitoringEnabled();
	}
}
?>
<?php
class Tx_Dbmigrate_Database_QueryPostProcessor extends Tx_Dbmigrate_Database_AbstractProcessor implements t3lib_DB_postProcessQueryHook {

	/**
	 * Post-processor for the exec_INSERTquery method.
	 *
	 * @param string $table Database table name
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_INSERTquery_postProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->init();

		$lastQuery = $parentObject->debug_lastBuiltQuery;

		if ($lastQuery !== '') {
			$this->logQueryForTable('InsertInto', $table, $lastQuery);
		}
	}

	/**
	 * Post-processor for the exec_INSERTmultipleRows method.
	 *
	 * @param string $table Database table name
	 * @param array $fields Field names
	 * @param array $rows Table rows
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_INSERTmultipleRows_postProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->init();

		$lastQuery = $parentObject->debug_lastBuiltQuery;

		if ($lastQuery !== '') {
			$this->logQueryForTable('InsertMultipleRowsInto', $table, $lastQuery);
		}
	}

	/**
	 * Post-processor for the exec_UPDATEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_UPDATEquery_postProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		$this->init();

		$lastQuery = $parentObject->debug_lastBuiltQuery;

		if ($lastQuery !== '') {
			$this->logQueryForTable('Update', $table, $lastQuery);
		}
	}

	/**
	 * Post-processor for the exec_DELETEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_DELETEquery_postProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		$this->init();

		$lastQuery = $parentObject->debug_lastBuiltQuery;

		if ($lastQuery !== '') {
			$this->logQueryForTable('Delete', $table, $lastQuery);
		}
	}

	/**
	 * Post-processor for the exec_TRUNCATEquery method.
	 *
	 * @param string $table Database table name
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_TRUNCATEquery_postProcessAction(&$table, t3lib_DB $parentObject) {
		$this->init();

		$lastQuery = $parentObject->debug_lastBuiltQuery;

		if ($lastQuery !== '') {
			$this->logQueryForTable('Truncate', $table, $lastQuery);
		}
	}
}
?>
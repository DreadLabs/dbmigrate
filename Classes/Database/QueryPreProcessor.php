<?php
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
		$this->init();

		$parentObject->store_lastBuiltQuery = $this->isQueryLoggingEnabled();
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
		$this->init();

		$parentObject->store_lastBuiltQuery = $this->isQueryLoggingEnabled();
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
		$this->init();

		$parentObject->store_lastBuiltQuery = $this->isQueryLoggingEnabled();
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
		$this->init();

		$parentObject->store_lastBuiltQuery = $this->isQueryLoggingEnabled();
	}

	/**
	 * Pre-processor for the TRUNCATEquery method.
	 *
	 * @param string $table Database table name
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function TRUNCATEquery_preProcessAction(&$table, t3lib_DB $parentObject) {
		$this->init();

		$parentObject->store_lastBuiltQuery = $this->isQueryLoggingEnabled();
	}
}
?>
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

/**
 * QueryPreProcessor.php
 *
 * \TYPO3\CMS\Core\Database\DatabaseConnection pre processor implements business logic for different database (DML) queries.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

class QueryPreProcessor extends \DreadLabs\Dbmigrate\Database\AbstractProcessor implements \TYPO3\CMS\Core\Database\PreProcessQueryHookInterface {

	/**
	 * Pre-processor for the INSERTquery method.
	 *
	 * @param string $table Database table name
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject
	 * @return void
	 */
	public function INSERTquery_preProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject) {
		$this->initialize();

		try {
			$this->raiseExceptionUnlessTableIsActive($table);
			$this->raiseExceptionIfChangeIsBlacklisted($table, $fieldsValues);

			if ($this->isMonitoringEnabled() && FALSE === $this->user->hasActiveChange()) {
				$changeId = $this->changeRepository->findNextFreeChangeOfUser();
				$this->user->setChange($changeId);

				$parentObject->store_lastBuiltQuery = TRUE;
			}
		} catch (\Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
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
	 * @param \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject
	 * @return void
	 */
	public function INSERTmultipleRows_preProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject) {
		$this->initialize();

		try {
			$this->raiseExceptionUnlessTableIsActive($table);
			$this->raiseExceptionIfChangeIsBlacklisted($table, $fields);

			if ($this->isMonitoringEnabled() && FALSE === $this->user->hasActiveChange()) {
				$changeId = $this->changeRepository->findNextFreeChangeOfUser();
				$this->user->setChange($changeId);

				$parentObject->store_lastBuiltQuery = TRUE;
			}
		} catch (\Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
	}

	/**
	 * Pre-processor for the UPDATEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param array $fieldsValues Field values as key => value pairs
	 * @param string/array $noQuoteFields List/array of keys NOT to quote
	 * @param \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject
	 * @return void
	 */
	public function UPDATEquery_preProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject) {
		$this->initialize();

		try {
			$this->raiseExceptionUnlessTableIsActive($table);
			$this->raiseExceptionIfChangeIsBlacklisted($table, $fieldsValues);

			if ($this->isMonitoringEnabled() && FALSE === $this->user->hasActiveChange()) {
				$changeId = $this->changeRepository->findNextFreeChangeOfUser();
				$this->user->setChange($changeId);

				$parentObject->store_lastBuiltQuery = TRUE;
			}
		} catch (\Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
	}

	/**
	 * Pre-processor for the DELETEquery method.
	 *
	 * @param string $table Database table name
	 * @param string $where WHERE clause
	 * @param \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject
	 * @return void
	 */
	public function DELETEquery_preProcessAction(&$table, &$where, \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject) {
		$this->initialize();

		try {
			$this->raiseExceptionUnlessTableIsActive($table);

			if ($this->isMonitoringEnabled() && FALSE === $this->user->hasActiveChange()) {
				$changeId = $this->changeRepository->findNextFreeChangeOfUser();
				$this->user->setChange($changeId);

				$parentObject->store_lastBuiltQuery = TRUE;
			}
		} catch (\Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
	}

	/**
	 * Pre-processor for the TRUNCATEquery method.
	 *
	 * @param string $table Database table name
	 * @param \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject
	 * @return void
	 */
	public function TRUNCATEquery_preProcessAction(&$table, \TYPO3\CMS\Core\Database\DatabaseConnection $parentObject) {
		$this->initialize();

		try {
			$this->raiseExceptionUnlessTableIsActive($table);

			if ($this->isMonitoringEnabled() && FALSE === $this->user->hasActiveChange()) {
				$changeId = $this->changeRepository->findNextFreeChangeOfUser();
				$this->user->setChange($changeId);

				$parentObject->store_lastBuiltQuery = TRUE;
			}
		} catch (\Exception $e) {
			// fail silently
			// @todo: syslog or similar
		}
	}
}
?>
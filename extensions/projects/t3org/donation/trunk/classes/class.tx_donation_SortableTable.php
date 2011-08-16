<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner <ingo@typo3.org>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_t3lib . 'class.t3lib_page.php');

/**
 * a sortable table
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_SortableTable {

	protected $prefix;

	protected $header;
	protected $fields;
	protected $table;
	protected $tableEnableFields;
	protected $sortingField;
	protected $sortingDirection;
	protected $offset;
	protected $rowLimit;
	protected $conf;

	/**
	 * constructor for class tx_donation_SortableTable
	 */
	public function __construct($prefix, $table, array $header) {
		$this->setPrefix($prefix);
		$this->setTable($table);
		$this->setHeader($header);

		$getParameters = t3lib_div::_GET($this->prefix);

		if (!is_null($getParameters)) {
			if (in_array($getParameters['order'], $this->fields)) {
				$this->sortingField = $getParameters['order'];
			}

			if (isset($getParameters['sort'])) {
				if (in_array($getParameters['sort'], array('ASC', 'DESC'))) {
					$this->sortingDirection = $getParameters['sort'];
				} else {
					$this->sortingDirection = 'ASC';
				}
			}
		}
		
		if(is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_donation_pi_donorlist.']))
			$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_donation_pi_donorlist.'];
	}

	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * sets the table to query
	 *
	 * @param	string	the table to query
	 * @throws	InvalidArgumentException	throws an InvalidArgumentException if the given table is nto found in TCA
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setTable($table) {
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($table);

		if (!isset($GLOBALS['TCA'][$table])) {
			throw new InvalidArgumentException('No such table found in TCA: ' . $table, 1227720635);
		}

		$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		$pageSelect->init(false);

		$this->tableEnableFields = $pageSelect->enableFields($table);

		$this->table = $table;
	}

	public function setOffset($offset) {
		$this->offset = t3lib_div::intval_positive($offset);
	}

	public function setRowLimit($rowLimit) {
		$this->rowLimit = t3lib_div::intval_positive($rowLimit);
	}

	public function setHeader(array $header) {
		$this->header = $header;

		foreach ($header as $headerColumn) {
			$this->fields[] = $headerColumn['field'];

			if (isset($headerColumn['sort'])) {
				$this->setSortingField($headerColumn['field']);
				$this->setSortingDirection($headerColumn['sort']);
			}
		}
	}

	public function addField($label, $field) {
		$this->header[] = array('label' => $label, 'field' => $field);
		$this->fields[] = $field;
	}

	/**
	 * sets the fields to query from the table
	 *
	 * @param	array	An array of field names
	 * @throws	InvalidArgumentException	throws and InvalidArgumentException if a field is not found in the current table
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setFields(array $fields) {
		foreach ($fields as $field) {
			if (!isset($GLOBALS['TCA'][$this->table]['columns'][$field])) {
				InvalidArgumentException('No such field found in table ' . $this->table . ': ' . $field, 1227720635);
			}
		}

		$this->fields = $fields;
	}

	public function setSortingField($sortingField) {
		if (in_array($sortingField, $this->fields)) {
			$this->sortingField = $sortingField;
		} else {
			$this->sortingField = $this->fields[0];
		}
	}

	/**
	 * sets the sorting direction - either ascending (ASC) or descending (DESC).
	 * Also checks for valid input, if invlaid, the direction is set to
	 * descending.
	 *
	 * @param	string	sorting direction, either ASC or DESC
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function setSortingDirection($sortingDirection) {
		$sortingDirection = strtoupper($sortingDirection);

		if (!in_array($sortingDirection, array('ASC', 'DESC'))) {
			$sortingDirection = 'ASC';
		}

		$this->sortingDirection = $sortingDirection;
	}

	public function getHeaderColumns() {
		$headerColumns = array();

		foreach ($this->header as $headerColumn) {
			$headerColumns[] = array(
				'field' => $headerColumn['field'],
				'title' =>$this->formatHeaderCell($headerColumn)
			);
		}

		return $headerColumns;
	}

	public function getRows() {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->table,
			'1 = 1 ' . $this->tableEnableFields,
			'',
			$this->sortingField . ' ' . $this->sortingDirection,
			$this->offset . ',' . $this->rowLimit
		);

		return $rows;
	}

	public function getTotalRowCount() {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->table,
			'1 = 1 ' . $this->tableEnableFields,
			'',
			$this->sortingField . ' ' . $this->sortingDirection
		);

		return count($rows);
	}

	protected function formatHeaderCell($cell) {
		$contentObject = t3lib_div::makeInstance('tslib_cObj');
		$sorting       = 'ASC';
		$sortingField  = $cell['field'];
		$cellContent   = $cell['label'];

		if ($cell['field'] == $this->sortingField) {
			$sortIndicator = $contentObject->IMAGE(array(
				'file' => $this->conf['iconPath'].'arrow-asc.png'
			));

			if ($this->sortingDirection == 'ASC') {
				$sorting = 'DESC';
				$sortIndicator = $contentObject->IMAGE(array(
					'file' => $this->conf['iconPath'].'arrow-desc.png'
				));
			}

			$cellContent .= ' ' . $sortIndicator;
		}

		$cellContent = $contentObject->typolink(
			$cellContent,
			array(
				'useCacheHash'     => true,
				'parameter'        => $GLOBALS['TSFE']->id,
				'additionalParams' => t3lib_div::implodeArrayForUrl(
					'',
					array(
						$this->prefix => array(
							'order' => $sortingField,
							'sort'  => $sorting
						)
					),
					'',
					true
				)
			)
		);

		return $cellContent;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_SortableTable.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_SortableTable.php']);
}

?>
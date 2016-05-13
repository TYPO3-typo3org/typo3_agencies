<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Fabien Udriot <fabien.udriot@typo3.org>
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
 * ************************************************************* */

/**
 * A repository for agencies
 */
class Tx_Typo3Agencies_Domain_Repository_AgencyRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Count all references by the specified filter
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @return array The references
	 */
	public function countAllByFilter(Tx_Typo3Agencies_Domain_Model_Filter $filter, $latlong = null, $nearbyAdditionalWhere = null) {
		$query = $this->createQuery();
		if(is_array($latlong)){
			$query->statement($this->getStatement($filter, $latlong, $nearbyAdditionalWhere));

			$result = count($query->execute()->toArray());
		} else {
			$constrains = $this->getConstraints($query, $filter);

			if (!empty($constrains)) {
				$query->matching($query->logicalAnd($constrains));
			}

			$result = $query->execute()->count();
		}



		return $result;
	}

	/**
	 * Return the contrains
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query The filter the references must apply to
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @return array The references
	 */
	protected function getConstraints(Tx_Extbase_Persistence_QueryInterface $query, Tx_Typo3Agencies_Domain_Model_Filter $filter) {
		$constrains = array();

		$constrains[] = $query->greaterThan('member', 0);

		// Special case: remove certain type of record from the result set
		$_constrains = array();
		$_constrains[] = $query->logicalNot($query->equals('first_name', ''));
		$_constrains[] = $query->logicalNot($query->equals('last_name', ''));
		$_constrains[] = $query->logicalNot($query->equals('name', ''));
		$constrains[] = $query->logicalOr($_constrains);

		// Membership case
		$members = $filter->getMembers();
		if (!empty($members)) {
			foreach ($members as $member) {
				$_constrains[] = $query->equals('member', $member);
			}
		}

		// Service case
		if ($filter->getTrainingService()) {
			$constrains[] = $query->equals('training_service', $filter->getTrainingService());
		}
		if ($filter->getHostingService()) {
			$constrains[] = $query->equals('hosting_service', $filter->getHostingService());
		}
		if ($filter->getDevelopmentService()) {
			$constrains[] = $query->equals('development_service', $filter->getDevelopmentService());
		}

		// Country case
		if ($filter->getCountry()) {
			$constrains[] = $query->equals('country', $filter->getCountry());
		}
		if ($filter->getFeUser()) {
			$constrains[] = $query->logicalOr(
				$query->equals('administrator', $filter->getFeUser()),
				$query->logicalAnd(
					$query->equals('approved', true),
					$query->logicalOr(
						$query->equals('payed_until_date', 0),
						$query->greaterThanOrEqual('payed_until_date', time())
					)
				)
			);
		} else {
			$constrains[] = $query->logicalAnd(
				$query->equals('approved', true),
				$query->logicalOr(
					$query->equals('payed_until_date', 0),
					$query->greaterThanOrEqual('payed_until_date', time())
				)
			);
		}
		return $constrains;
	}

	/**
	 * Return the sql statement - needed for distance query
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @param array $latLong Array of latitude and longitude
	 * @param string $nearbyAdditionalWhere
	 * @return array The references
	 */
	protected function getStatement(Tx_Typo3Agencies_Domain_Model_Filter $filter, $latLong = array(), $nearbyAdditionalWhere = '') {
		$where = array();
		$where[] = '(first_name <> \'\' OR last_name <> \'\' OR name <> \'\')';

		// Membership case
		$members = $filter->getMembers();
		$memberArray = array(0);
		if (!empty($members)) {
			foreach ($members as $member) {
				$memberArray[] = intval($member);
			}
		}
		if (!empty($memberArray)) {
			$where[] = 'member IN (' . implode(',', $memberArray) . ')';
		}

		// Service case
		if ($filter->getTrainingService()) {
			$where[] = 'training_service = ' . intval($filter->getTrainingService());
		}
		if ($filter->getHostingService()) {
			$where[] = 'hosting_service = ' . intval($filter->getHostingService());
		}
		if ($filter->getDevelopmentService()) {
			$where[] = 'development_service = ' . intval($filter->getDevelopmentService());
		}

		// Country case
		if ($filter->getCountry()) {
			$where[] = 'country = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($filter->getCountry(), 'tx_typo3agencies_domain_model_agency');
		}

		if (!empty($latLong) && $nearbyAdditionalWhere != ''){
			$where[] = str_replace(array('###LONGITUDE###', '###LATITUDE###'), array(floatval($latLong['long']), floatval($latLong['lat'])), $nearbyAdditionalWhere);
		}

		if($filter->getFeUser() > 0){
			$where[] = '(approved = 1 or FIND_IN_SET(administrator, '. intval($filter->getFeUser()) . '))';
		} else {
			$where[] = 'approved = 1';
		}


		$sql = 'SELECT * FROM tx_typo3agencies_domain_model_agency WHERE ' . implode(' AND ', $where ) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_agency').' ORDER BY member DESC, name ASC, last_name ASC';
		return $sql;
	}

	/**
	 * Finds all references by the specified filter
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @param Tx_Typo3Agencies_Domain_Model_Order $order The order
	 * @param int $offset
	 * @param int $rowsPerPage
	 * @param array $latLong Array of latitude and longitude
	 * @param string $nearbyAdditionalWhere
	 * @return array The references
	 */
	public function findAllByFilter(Tx_Typo3Agencies_Domain_Model_Filter $filter, Tx_Typo3Agencies_Domain_Model_Order $order = null, $offset = 0, $rowsPerPage = 0, $latLong = array(), $nearbyAdditionalWhere = '') {
		$query = $this->createQuery();
		if (!empty($latLong)) {
			$query->statement($this->getStatement($filter, $latLong, $nearbyAdditionalWhere));
		} else {
			$constraints = $this->getConstraints($query, $filter);

			if (!empty($constraints)) {
				$query->matching($query->logicalAnd($constraints));
			}

			if ($order) {
				$ordering = $order->getOrderings();
				$query->setOrderings($ordering);
			}

			if ($offset) {
				$query->setOffset((integer) $offset);
			}

			if ($rowsPerPage) {
				$query->setLimit((integer) $rowsPerPage);
			}
		}

		$result = $query->execute();
		return $result;
	}

	/**
	 * Finds all countries from the storage
	 *
	 * @return array The countries
	 */
	public function findAllCountries() {
		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		$query->statement('SELECT DISTINCT country FROM tx_typo3agencies_domain_model_agency WHERE country <> \'\' ORDER BY country ASC');
		$result = $query->execute();
		return $result;
	}

	/**
	 * @param $userId
	 * @return array
	 */
	public function findAllForUser($userId) {
		$query = $this->createQuery();
		// a user wants to see his hidden records
//		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$query->matching(
			$query->logicalAnd(
				$query->equals('administrator', $userId),
				$query->equals('deleted', '0')
			)
		);

		$result = $query->execute();

		return $result;
	}

	/**
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter
	 * @param Tx_Typo3Agencies_Domain_Model_Order $order
	 * @return array|\Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function findByNameOrCity(Tx_Typo3Agencies_Domain_Model_Filter $filter, Tx_Typo3Agencies_Domain_Model_Order $order) {
		$query = $this->createQuery();
		$searchString = '%' . mysql_real_escape_string($filter->getLocation()) . '%';

		if ($order) {
			$ordering = $order->getOrderings();
			$query->setOrderings($ordering);
		}

		$query->matching(
			$query->logicalAnd(
				array(
					$query->equals('approved', TRUE),
					$query->logicalOr(
						array(
							$query->equals('payed_until_date', 0),
							$query->greaterThanOrEqual('payed_until_date', time())
						)
					),
					$query->logicalOr(
						array(
							$query->like('name', $searchString),
							$query->like('city', $searchString)
						)
					)
				)
			)
		);

		return $query->execute();
	}


}

?>
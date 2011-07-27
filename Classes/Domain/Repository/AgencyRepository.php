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
			$constrains = $this->getConstrains($query, $filter);
	
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
	protected function getConstrains(Tx_Extbase_Persistence_QueryInterface $query, Tx_Typo3Agencies_Domain_Model_Filter $filter) {
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
			$_constrains = array();
			foreach ($members as $member) {
				$_constrains[] = $query->equals('member', $member);
			}
			if (!empty($_constrains)) {
				$constrains[] = $query->logicalOr($_constrains);
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
		return $constrains;
	}
	
	/**
	 * Return the sql statement - needed for distance query
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @param array $latlong Array of latitude and longitude
	 * @return array The references
	 */
	protected function getStatement(Tx_Typo3Agencies_Domain_Model_Filter $filter, $latlong = null, $nearbyAdditionalWhere = null) {
		$where = Array();
		
		$where[] = 'member > 0';
		
		$where[] = '(first_name <> \'\' OR last_name <> \'\' OR name <> \'\')';
		
		// Membership case
		$members = $filter->getMembers();
		if (!empty($members)) {
			$memberArray = Array();
			foreach ($members as $member) {
				$memberArray[] = 'member = '.$member;
			}
			if (!empty($memberArray)) {
				$where[] = '('.implode(' OR ', $memberArray).')';
			}
		}
		
		// Service case
		if ($filter->getTrainingService()) {
			$where[] = 'training_service = '.$filter->getTrainingService();
		}
		if ($filter->getHostingService()) {
			$where[] = 'hosting_service = '.$filter->getHostingService();
		}
		if ($filter->getDevelopmentService()) {
			$where[] = 'development_service = '.$filter->getDevelopmentService();
		}
		
		// Country case
		if ($filter->getCountry()) {
			$where[] = 'country = \''.$filter->getCountry().'\'';
		}

		if (is_array($latlong) && $nearbyAdditionalWhere != null && $nearbyAdditionalWhere != ''){
			$where[] = str_replace(Array('###LONGITUDE###','###LATITUDE###'),Array($latlong['long'],$latlong['lat']),$nearbyAdditionalWhere);
		}
		
		
		$limit = ' LIMIT ' . $offset . ', ' . $rowsPerPage;
		if($justCount || $offset == null || $rowsPerPage == null){
			$limit = '';
		}
		$sql = 'SELECT * FROM tx_typo3agencies_domain_model_agency WHERE ' . implode(' AND ',$where) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_agency').' ORDER BY member DESC, name ASC, last_name ASC '.$limit;
		return $sql;
	}

	/**
	 * Finds all references by the specified filter
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @param Tx_Typo3Agencies_Domain_Model_Order $order The order
	 * @param int $offset
	 * @param int $rowsPerPage
	 * @param array $latlong Array of latitude and longitude
	 * @return array The references
	 */
	public function findAllByFilter(Tx_Typo3Agencies_Domain_Model_Filter $filter, Tx_Typo3Agencies_Domain_Model_Order $order = null, $offset = null, $rowsPerPage = null, $latlong = null, $nearbyAdditionalWhere = null) {
		$query = $this->createQuery();
		if(is_array($latlong)){
			$query->statement($this->getStatement($filter, $latlong, $nearbyAdditionalWhere));
		} else {
			$constrains = $this->getConstrains($query, $filter);
			if (!empty($constrains)) {
				$query->matching($query->logicalAnd($constrains));
			}
			
			if ($order) {
				$orderering = $order->getOrderings();
				$query->setOrderings($orderering);
			}
	
			if ($offset) {
				$query->setOffset((integer) $offset);
			}
	
			if ($rowsPerPage) {
				$query->setLimit((integer) $rowsPerPage);
			}
		}
						
		$result = $query->execute() ;
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

}

?>
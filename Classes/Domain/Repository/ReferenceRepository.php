<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Mario Matzulla <mario@matzullas.de>
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

/**
 * A repository for references
 */
class Tx_Typo3Agencies_Domain_Repository_ReferenceRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Finds all references by the specified agency
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The company the references must refer to
	 * @param boolean $includeDeactivated
	 * @return array The references
	 */
	public function findAllByAgency(Tx_Typo3Agencies_Domain_Model_Agency $agency, $includeDeactivated = FALSE) {
		$query = $this->createQuery();
		if($includeDeactivated){
			$query->matching($query->equals('agency', $agency));
		} else {
			$query->matching($query->logicalAnd($query->equals('agency', $agency),$query->equals('deactivated',0)));
		}
		$query->setOrderings(Array('sorting'=>Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING));
		return $query->execute();
	}

	public function findValidTopReferences($uidList = ''){
		$returnArray = Array();
		if($uidList != ''){
			$query = $this->createQuery();
			$query->matching($query->logicalAnd($query->in('uid', t3lib_div::trimExplode(',',$uidList,1)),$query->equals('deactivated',0)));
			$query->setOrderings(Array('crdate'=>Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));
			$result = $query->execute();

			foreach($result as $reference){
				$returnArray[] = $reference->getUid();
			}

		}
		return $returnArray;
	}

	/**
	 * Finds records within a certain range
	 *
	 * @param int $offset
	 * @param int $rowsPerPage
	 * @param boolean $includeDeactivated
	 * @return array The references
	 */
	public function findAllByRange($offset, $rowsPerPage, $includeDeactivated = FALSE){
		$query = $this->createQuery();
		if(!$includeDeactivated){
			$query->matching($query->equals('deactivated',0));
		}

		$query->getQuerySettings()->setRespectEnableFields(TRUE);
		$query->setLimit(intval($rowsPerPage));
		$query->setOffset($offset);
		return $query->execute();
	}

	/**
	 * Finds the recently added records
	 *
	 * @param int $offset
	 * @param int $rowsPerPage
	 * @param boolean $includeDeactivated
	 * @param int $ignore
	 * @return array The references
	 */
	public function findAllByRevenue($offset, $rowsPerPage, $includeDeactivated = FALSE, $agency = NULL, $limit = 30, $ignore = 0){
		$query = $this->createQuery();
		$constrains = array();
		if(!$includeDeactivated){
			if($ignore == 0){
				$constrains[] = $query->equals('deactivated',0);
			} else {
				$constrains[] = $query->logicalAnd($query->equals('deactivated',0),$query->logicalNot($query->equals('uid',$ignore)));
			}
		} else {
			$constrains[] = $query->logicalOr(
				$query->logicalAnd(
					// Not in ignore
					$query->logicalNot($query->equals('uid',$ignore)),
					// AND deactivated = 0
					$query->equals('deactivated',0)
				),
				// OR
				$query->logicalAnd(
					// deactivated = 1
					$query->equals('deactivated',1),
					// agency = $agency
					$query->equals('agency',$agency)
				)
			);
		}
		$query->matching($query->logicalAnd($constrains));
		$query->getQuerySettings()->setRespectEnableFields(TRUE);
		$query->setLimit((int)$rowsPerPage);
		$query->setOffset($offset);
		$query->setOrderings(Array('revenue'=>Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));
		return $query->execute();
	}

	/**
	 * Counts the recently added records
	 *
	 * @param int $offset
	 * @param int $rowsPerPage
	 * @param int $limit
	 * @return int Number of recently added
	 */
	public function countRecentlyAdded($includeDeactivated = FALSE, $agency = NULL, $limit = 30){
		$query = $this->createQuery();
		$constrains = array();
		if(!$includeDeactivated){
			$constrains[] = $query->equals('deactivated',0);
		} else {
			$constrains[] = $query->logicalOr(
				$query->logicalAnd(
					// AND deactivated = 0
					$query->equals('deactivated',0)
				),
				// OR
				$query->logicalAnd(
					// deactivated = 1
					$query->equals('deactivated',1),
					// agency = $agency
					$query->equals('agency',$agency)
				)
			);
		}
		$query->matching($query->logicalAnd($constrains));
		$query->getQuerySettings()->setRespectEnableFields(TRUE);
		$query->setLimit($limit);
		return count($query->execute()->toArray());
	}

	/**
	 * Finds all references by the specified filter
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Filter $filter The filter the references must apply to
	 * @param unknown_type $offset
	 * @param unknown_type $rowsPerPage
	 * @param unknown_type $justCount
	 * @param unknown_type $includeDeactivated
	 * @return array The references
	 */
	public function findAllByFilter(Tx_Typo3Agencies_Domain_Model_Filter $filter, $offset = NULL, $rowsPerPage = NULL, $justCount = FALSE, $includeDeactivated = FALSE) {
		$query = $this->createQuery();
		$where = Array();
		if($includeDeactivated){
			$where[] = '1=1';
		} else {
			$where[] = 'tx_typo3agencies_domain_model_reference.deactivated = 0';
		}
		if($filter->getSearchTerm() != ''){
			$escapedSearchTerm = $GLOBALS['TYPO3_DB']->fullQuoteStr('%' . $filter->getSearchTerm() . '%');
			$where[] = '(tx_typo3agencies_domain_model_reference.about LIKE ' . $escapedSearchTerm . ' OR tx_typo3agencies_domain_model_reference.title LIKE ' . $escapedSearchTerm . ' OR tx_typo3agencies_domain_model_reference.description LIKE ' . $escapedSearchTerm . ' OR tx_typo3agencies_domain_model_reference.tags LIKE ' . $escapedSearchTerm . ' OR tx_typo3agencies_domain_model_reference.conclusion LIKE ' . $escapedSearchTerm . ')';
		}
		if($filter->getCategory() > 0){
			$where[] = 'tx_typo3agencies_domain_model_reference.category = ' . intval($filter->getCategory());
		}
		if($filter->getIndustry() > 0){
			$where[] = 'tx_typo3agencies_domain_model_reference.industry = ' . intval($filter->getIndustry());
		}
		if($filter->getRevenue() > 0){
			$where[] = 'tx_typo3agencies_domain_model_reference.revenue = ' . intval($filter->getRevenue());
		}
		if($filter->isListed()){
			$where[] = 'tx_typo3agencies_domain_model_reference.listed = 1';
		}

		$limit = ' LIMIT ' . intval($offset) . ', ' . intval($rowsPerPage);
		if($justCount || $offset == NULL || $rowsPerPage == NULL){
			$limit = '';
		}
		$query->statement('SELECT tx_typo3agencies_domain_model_reference.* FROM tx_typo3agencies_domain_model_reference left join tx_typo3agencies_domain_model_agency on tx_typo3agencies_domain_model_reference.agency = tx_typo3agencies_domain_model_agency.uid WHERE ' . implode(' AND ', $where) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_reference').' ORDER BY tx_typo3agencies_domain_model_agency.member DESC, tx_typo3agencies_domain_model_reference.crdate DESC'.$limit);
		$result = $query->execute();

		if($justCount){
			return count($result->toArray());
		}
		return $result;
	}

	/**
	 * @param Tx_Typo3Agencies_Domain_Model_Category $category
	 * @param Tx_Typo3Agencies_Domain_Model_Industry $industry
	 * @param Tx_Typo3Agencies_Domain_Model_Revenue $revenue
	 * @param integer $membershipStatus
	 * @param boolean $fortune500
	 * @param integer $offset
	 * @param integer $rowsPerPage
	 * @param boolean $includeDeactivated
	 *
	 * @return mixed
	 */
	public function findByFilterSelection(Tx_Typo3Agencies_Domain_Model_Category $category = NULL, Tx_Typo3Agencies_Domain_Model_Industry $industry = NULL, Tx_Typo3Agencies_Domain_Model_Revenue $revenue = NULL, $membershipStatus = -1, $fortune500 = TRUE, $offset = NULL, $rowsPerPage = NULL, $includeDeactivated = FALSE) {
		$query = $this->createQuery();

		$andConditions = array();
		if ($includeDeactivated === FALSE) {
			$andConditions[] = $query->equals('deactivated', 0);
		}

		if ($category !== NULL) {
			$andConditions[] = $query->equals('category', $category);
		}

		if ($industry !== NULL) {
			$andConditions[] = $query->equals('industry', $industry);
		}

		if ($revenue !== NULL) {
			$andConditions[] = $query->equals('revenue', $revenue);
		}

		if ($fortune500 === TRUE) {
			$andConditions[] = $query->equals('listed', TRUE);
		}

		if ($membershipStatus !== -1 && intval($membershipStatus) > 0) {
			$andConditions[] = $query->equals('agency.member', intval($membershipStatus));
		}

		$query->matching($query->logicalAnd($andConditions));

		if ($rowsPerPage !== NULL) {
			$query->setLimit(intval($rowsPerPage));
		}

		if ($offset !== NULL && intval($offset) > 0) {
			$query->setOffset(intval($offset));
		}

		$query->setOrderings(array(
			'revenue' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
		));
		return $query->execute();
	}

	/**
	 * Counts the records with certain options
	 *
	 * @param int $selectedCategory
	 * @param int $selectedIndustry
	 * @param int $selectedRevenue
	 * @param boolean $includeDeactivated
	 * @return int Number of records
	 */
	public function countByOption($selectedCategory=0, $selectedIndustry = 0, $selectedRevenue = 0, $includeDeactivated = FALSE) {
		$query = $this->createQuery();

		if($includeDeactivated){
			$where[] = '1=1';
		} else {
			$where[] = 'deactivated = 0';
		}
		if($selectedCategory > 0){
			$where[] = 'category = ' . intval($selectedCategory);
		}
		if($selectedIndustry > 0){
			$where[] = 'industry = ' . intval($selectedIndustry);
		}
		if($selectedRevenue > 0){
			$where[] = 'revenue = ' . intval($selectedRevenue);
		}

		$query->statement('SELECT * FROM tx_typo3agencies_domain_model_reference WHERE ' . implode(' AND ', $where) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_reference'));
		return count($query->execute()->toArray());
	}

	public function countByAgency($agency){
		$query = $this->createQuery();
		$query->matching($query->logicalAnd($query->equals('deactivated', 0), $query->equals('agency', $agency)));
		return count($query->execute()->toArray());
	}
}
?>
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
	public function findAllByAgency(Tx_Typo3Agencies_Domain_Model_Agency $agency, $includeDeactivated = false) {
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
	public function findAllByRange($offset, $rowsPerPage, $includeDeactivated = false){
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
	public function findRecentlyAdded($offset, $rowsPerPage, $includeDeactivated = false, $agency = null, $limit = 30, $ignore = 0){
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
		$query->setOrderings(Array('crdate'=>Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));
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
	public function countRecentlyAdded($includeDeactivated = false, $agency = null, $limit = 30){
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
		$query->setOrderings(Array('crdate'=>Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));
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
	public function findAllByFilter(Tx_Typo3Agencies_Domain_Model_Filter $filter, $offset = null, $rowsPerPage = null, $justCount = false, $includeDeactivated = false) {
		$query = $this->createQuery();
		$where = Array();
		if($includeDeactivated){
			$where[] = '1=1';
		} else {
			$where[] = 'deactivated = 0';
		}
		if($filter->getSearchTerm() != ''){
			$where[] = '(about LIKE \'%' . $filter->getSearchTerm() . '%\' OR title LIKE \'%' . $filter->getSearchTerm() . '%\' OR description LIKE \'%' . $filter->getSearchTerm() . '%\' OR tags LIKE \'%' . $filter->getSearchTerm() . '%\' OR conclusion LIKE \'%' . $filter->getSearchTerm() . '%\')';
		}
		if($filter->getCategory() > 0){
			$where[] = 'category = ' . $filter->getCategory();
		}
		if($filter->getIndustry() > 0){
			$where[] = 'industry = ' . $filter->getIndustry();
		}
		if($filter->getCompanySize() > 0){
			$where[] = 'size = ' . $filter->getCompanySize();
		}
		if($filter->isListed()){
			$where[] = 'listed = 1';
		}
		
		$limit = ' LIMIT ' . $offset . ', ' . $rowsPerPage;
		if($justCount || $offset == null || $rowsPerPage == null){
			$limit = '';
		}
		$query->statement('SELECT tx_typo3agencies_domain_model_reference.* FROM tx_typo3agencies_domain_model_reference left join tx_typo3agencies_domain_model_agency on tx_typo3agencies_domain_model_reference.agency = tx_typo3agencies_domain_model_agency.uid WHERE ' . implode(' AND ',$where) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_reference').' ORDER BY tx_typo3agencies_domain_model_agency.member DESC, tx_typo3agencies_domain_model_reference.crdate DESC'.$limit);
		$result = $query->execute();
		
		if($justCount){
			return count($result->toArray());
		}
		return $result;
	}
	
	/**
	 * Counts the records with certain options
	 * 
	 * @param int $selectedCategory
	 * @param int $selectedIndustry
	 * @param int $selectedCompanySize
	 * @param boolean $includeDeactivated
	 * @return int Number of records
	 */
	public function countByOption($selectedCategory=0, $selectedIndustry = 0, $selectedCompanySize = 0, $includeDeactivated = false) {
		$query = $this->createQuery();
		
		if($includeDeactivated){
			$where[] = '1=1';
		} else {
			$where[] = 'deactivated = 0';
		}
		if($selectedCategory > 0){
			$where[] = 'category = ' . $selectedCategory;
		}
		if($selectedIndustry > 0){
			$where[] = 'industry = ' . $selectedIndustry;
		}
		if($selectedCompanySize > 0){
			$where[] = 'size = ' . $selectedCompanySize;
		}
		$query->statement('SELECT * FROM tx_typo3agencies_domain_model_reference WHERE ' . implode(' AND ',$where) . $GLOBALS['TSFE']->sys_page->enableFields('tx_typo3agencies_domain_model_reference'));
		return count($query->execute()->toArray());
	}
	
	public function countByAgency($agency){
		$query = $this->createQuery();
		$query->matching($query->logicalAnd($query->equals('deactivated',0),$query->equals('agency',$agency)));
		return count($query->execute()->toArray());
	}
}
?>
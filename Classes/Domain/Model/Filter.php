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
 * A Filter
 *
 * @version $Id:$
 * @copyright Copyright belongs to the respective authors
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Domain_Model_Filter extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * The filter search term.
	 *
	 * @var string
	 */
	protected $searchTerm = '';

	/**
	 * A category to filter by
	 *
	 * @var integer
	 */
	protected $category = 0;
	
	/**
	 * An industry to filter by
	 *
	 * @var string
	 */
	protected $industry = 0;
	
	/**
	 * The company size to filter by
	 *
	 * @var integer
	 */
	protected $companySize = 0;
	
	/**
	 * Filter only by Fortune1000 company
	 *
	 * @var boolean
	 */
	protected $listed = false;
	
	/**
	 * Number of items found
	 * @var integer
	 */
	protected $resultCount;

	/**
	 * A membership
	 *
	 * @var string
	 */
	protected $member = '';

	/**
	 * An array of membership
	 *
	 * @var array
	 */
	protected $members = array();

	/**
	 * A trainingService to filter by
	 *
	 * @var integer
	 */
	protected $trainingService = 0;

	/**
	 * A hostingService to filter by
	 *
	 * @var integer
	 */
	protected $hostingService = 0;

	/**
	 * A developmentService to filter by
	 *
	 * @var integer
	 */
	protected $developmentService = 0;


	/**
	 * A country to filter by
	 *
	 * @var string
	 */
	protected $country = 0;

	/**
	 * Constructs a new Filter
	 *
	 */
	public function __construct() {
		$this->listed = false;
	}

	/**
	 * Sets this filter trainingService
	 *
	 * @param int $trainingService The filter trainingService
	 * @return void
	 */
	public function setTrainingService($trainingService) {
		$this->trainingService = $trainingService;
	}

	/**
	 * Returns the filter trainingService
	 *
	 * @return int The filter trainingService
	 */
	public function getTrainingService() {
		return $this->trainingService;
	}

	/**
	 * Sets this filter hostingService
	 *
	 * @param int $hostingService The filter hostingService
	 * @return void
	 */
	public function setHostingService($hostingService) {
		$this->hostingService = $hostingService;
	}

	/**
	 * Returns the filter hostingService
	 *
	 * @return int The filter hostingService
	 */
	public function getHostingService() {
		return $this->hostingService;
	}

	/**
	 * Sets this filter developmentService
	 *
	 * @param int $developmentService The filter developmentService
	 * @return void
	 */
	public function setDevelopmentService($developmentService) {
		$this->developmentService = $developmentService;
	}

	/**
	 * Returns the filter developmentService
	 *
	 * @return int The filter developmentService
	 */
	public function getDevelopmentService() {
		return $this->developmentService;
	}

	/**
	 * Sets this filter country
	 *
	 * @param string $country The filter country
	 * @return void
	 */
	public function setCountry($country) {
		$this->country = $country;
	}

	/**
	 * Returns the filter country
	 *
	 * @return string The filter country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Sets this filter category
	 *
	 * @param int $category The filter category
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * Returns the filter category
	 *
	 * @return int The filter category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets this filter industry
	 *
	 * @param int $industry The filter industry
	 * @return void
	 */
	public function setIndustry($industry) {
		$this->industry = $industry;
	}

	/**
	 * Returns the filter industry
	 *
	 * @return int The filter industry
	 */
	public function getIndustry() {
		return $this->industry;
	}
	
	/**
	 * Sets this filter company size
	 *
	 * @param int $companySize The filter company size
	 * @return void
	 */
	public function setCompanySize($companySize) {
		$this->companySize = $companySize;
	}

	/**
	 * Returns the filter company size
	 *
	 * @return int The filter company size
	 */
	public function getCompanySize() {
		return $this->companySize;
	}
	
	/**
	 * Sets this filter searchTerm
	 *
	 * @param string $searchTerm The filter searchTerm
	 * @return void
	 */
	public function setSearchTerm($searchTerm) {
		$this->searchTerm = $searchTerm;
	}

	/**
	 * Returns the filter searchTerm
	 *
	 * @return string The filter searchTerm
	 */
	public function getSearchTerm() {
		return $this->searchTerm;
	}
	
	/**
	 * Sets this filter listed
	 *
	 * @param boolean $listed The filter listed
	 * @return void
	 */
	public function setListed($listed) {
		$this->listed = $this->boolval($listed);
	}

	/**
	 * Returns the filter listed
	 *
	 * @return boolean The filter listed
	 */
	public function isListed() {
		return $this->listed;
	}
	
	/**
	 * Sets result count
	 *
	 * @param boolean $resultCount The result count
	 * @return void
	 */
	public function setResultCount($resultCount) {
		$this->resultCount = $resultCount;
	}

	/**
	 * Returns the member
	 *
	 * @return string
	 */
	public function getMember() {
		return $this->member;
	}

	/**
	 * Sets the member
	 *
	 * @param string The member
	 * @return void
	 */
	public function setMember($member) {
		$this->member = $member;
	}

	/**
	 * Returns the array of member
	 *
	 * @return array
	 */
	public function getMembers() {
		return $this->members;
	}

	/**
	 * Sets the member
	 *
	 * @param string The member
	 * @return void
	 */
	public function addMember($member) {
		$this->members[] = $member;
	}

	/**
	 * Returns the result count
	 *
	 * @return int The result count
	 */
	public function getResultCount() {
		return $this->resultCount;
	}
	
	private function boolval($in, $strict=false) {
	    $out = null;
	    $in = (is_string($in)?strtolower($in):$in);
	    // if not strict, we only have to check if something is false
	    if (in_array($in,array('false','no', 'n','0','off',false,0), true) || !$in) {
	        $out = false;
	    } else if ($strict) {
	        // if strict, check the equivalent true values
	        if (in_array($in,array('true','yes','y','1','on',true,1), true)) {
	            $out = true;
	        }
	    } else {
	        // not strict? let the regular php bool check figure it out (will
	        //     largely default to true)
	        $out = ($in?true:false);
	    }
	    return $out;
	}
	
}
?>
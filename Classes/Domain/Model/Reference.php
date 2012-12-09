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
 * A Reference
 *
 * @version $Id:$
 * @copyright Copyright belongs to the respective authors
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Domain_Model_Reference extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_CountryRepository
	 * @inject
	 */
	protected $countryRepository;

	/**
	 * Deactivate this reference
	 * @var boolean
	 */
	protected $deactivated = FALSE;

	/**
	 * The reference title.
	 *
	 * @var string
	 * @validate NotEmpty,StringLength(maximum = 256)
	 */
	protected $title = '';

	/**
	 * A short description of the reference
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $description = '';

	/**
	 * A link of the reference
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $link = '';

	/**
	 * Index of pages attribute of the reference
	 *
	 * @var integer
	 */
	protected $pages = 0;

	/**
	 * Languages of the reference
	 *
	 * @var string
	 * @validate NotEmpty,StringLength(maximum = 256)
	 */
	protected $languages = '';

	/**
	 * Category of the reference
	 *
	 * @var Tx_Typo3Agencies_Domain_Model_Category
	 */
	protected $category;

	/**
	 * Other category of the reference
	 *
	 * @var string
	 * @validate StringLength(maximum = 256)
	 */
	protected $categoryOther = '';

	/**
	 * Tags of the reference
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $tags = '';

	/**
	 * Industry of the reference
	 *
	 * @var Tx_Typo3Agencies_Domain_Model_Industry
	 */
	protected $industry;

	/**
	 * Other industry of the reference
	 *
	 * @var string
	 * @validate StringLength(maximum = 256)
	 */
	protected $industryOther = '';

	/**
	 * Screenshot of the reference
	 *
	 * @var string
	 */
	protected $screenshot = '';

	/**
	 * Screenshot gallery of the reference
	 *
	 * @var string
	 */
	protected $screenshotGallery = '';

	/**
	 * Casestudy of the reference
	 *
	 * @var string
	 */
	protected $casestudy = '';

	/**
	 * A short conclusion of the reference
	 *
	 * @var string
	 */
	protected $conclusion = '';

	/**
	 * About the company
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $about = '';

	/**
	 * Reference Agency
	 *
	 * @var Tx_Typo3Agencies_Domain_Model_Agency
	 * @lazy
	 */
	protected $agency;

	/**
	 * Agency revenue
	 *
	 * @var Tx_Typo3Agencies_Domain_Model_Revenue
	 */
	protected $revenue;

	/**
	 * Reference sorting
	 *
	 * @var integer
	 */
	protected $sorting = 0;

	/**
	 * Agency country
	 *
	 * @var string
	 */
	protected $country = '';

	/**
	 * Agency listed in fortune 1000
	 *
	 * @var boolean
	 */
	protected $listed = FALSE;


	/**
	 * Constructs a new Reference
	 *
	 */
	public function __construct() {
	}

	/**
	 * Sets this reference title
	 *
	 * @param string $title The reference title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the reference title
	 *
	 * @return string The reference title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the description for the reference
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the category for the reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Category $category
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * Returns the category
	 *
	 * @return Tx_Typo3Agencies_Domain_Model_Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets the categoryOther for the reference
	 *
	 * @param string $categoryOther
	 * @return void
	 */
	public function setCategoryOther($categoryOther) {
		$this->categoryOther = $categoryOther;
	}

	/**
	 * Returns the categoryOther
	 *
	 * @return string
	 */
	public function getCategoryOther() {
		return $this->categoryOther;
	}

	/**
	 * Sets the agency
	 *
	 * @param Tx_Typo3Agencies_Doamin_Model_Agency $agency
	 * @return void
	 */
	public function setAgency($agency) {
		$this->agency = $agency;
	}

	/**
	 * Returns the Tx_Typo3Agencies_Doamin_Model_Agency
	 *
	 * @return Tx_Typo3Agencies_Domain_Model_Agency
	 */
	public function getAgency() {
		return $this->agency;
	}

	/**
	 * Sets the industry for the reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Industry $industry
	 * @return void
	 */
	public function setIndustry($industry) {
		$this->industry = $industry;
	}

	/**
	 * Returns the Tx_Typo3Agencies_Domain_Model_Industry
	 *
	 * @return Tx_Typo3Agencies_Domain_Model_Industry
	 */
	public function getIndustry() {
		return $this->industry;
	}

	/**
	 * Sets the industryOther for the reference
	 *
	 * @param string $industryOther
	 * @return void
	 */
	public function setIndustryOther($industryOther) {
		$this->industryOther = $industryOther;
	}

	/**
	 * Returns the industryOther
	 *
	 * @return string
	 */
	public function getIndustryOther() {
		return $this->industryOther;
	}

	/**
	 * Sets the languages for the reference
	 *
	 * @param string $languages
	 * @return void
	 */
	public function setLanguages($languages) {
		$this->languages = $languages;
	}

	/**
	 * Returns the languages
	 *
	 * @return string
	 */
	public function getLanguages() {
		return $this->languages;
	}

	/**
	 * Sets the link for the reference
	 *
	 * @param string $link
	 * @return void
	 */
	public function setLink($link) {
		$this->link = $link;
	}

	/**
	 * Returns the link
	 *
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * Sets the pages for the reference
	 *
	 * @param string $pages
	 * @return void
	 */
	public function setPages($pages) {
		$this->pages = $pages;
	}

	/**
	 * Returns the pages
	 *
	 * @return string
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * Sets the screenshot for the reference
	 *
	 * @param string $screenshot
	 * @return void
	 */
	public function setScreenshot($screenshot) {
		$this->screenshot = $screenshot;
	}

	/**
	 * Returns the screenshot
	 *
	 * @return string
	 */
	public function getScreenshot() {
		return $this->screenshot;
	}

	/**
	 * Sets the screenshotGallery for the reference
	 *
	 * @param string $screenshotGallery
	 * @return void
	 */
	public function setScreenshotGallery($screenshotGallery) {
		$this->screenshotGallery = $screenshotGallery;
	}

	/**
	 * Returns the screenshotGallery
	 *
	 * @return string
	 */
	public function getScreenshotGallery() {
		return $this->screenshotGallery;
	}

	/**
	 * Sets the casestudy for the reference
	 *
	 * @param string $casestudy
	 * @return void
	 */
	public function setCasestudy($casestudy) {
		$this->casestudy = $casestudy;
	}

	/**
	 * Returns the casestudy
	 *
	 * @return string
	 */
	public function getCasestudy() {
		return $this->casestudy;
	}

	/**
	 * Sets the tags for the reference
	 *
	 * @param string $tags
	 * @return void
	 */
	public function setTags($tags) {
		$this->tags = $tags;
	}

	/**
	 * Returns the tags
	 *
	 * @return string
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * Sets the about for the conclusion
	 *
	 * @param string $conclusion
	 * @return void
	 */
	public function setConclusion($conclusion) {
		$this->conclusion = $conclusion;
	}

	/**
	 * Returns the conclusion
	 *
	 * @return string
	 */
	public function getConclusion() {
		return $this->conclusion;
	}

	/**
	 * Sets the about for the reference
	 *
	 * @param string $about
	 * @return void
	 */
	public function setAbout($about) {
		$this->about = $about;
	}

	/**
	 * Returns the about
	 *
	 * @return string
	 */
	public function getAbout() {
		return $this->about;
	}

	/**
	 * Sets the country of the company
	 *
	 * @param string $country
	 * @return void
	 */
	public function setCountry($country) {
		$this->country = $country;
	}

	/**
	 * Returns the country
	 *
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @return Tx_Typo3Agencies_Domain_Model_Country
	 */
	public function getCountryObject() {
		if ($this->countryRepository == NULL) {
			$this->countryRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_CountryRepository');
		}
		return $this->countryRepository->findOneByCnIso2($this->country);
	}

	/**
	 * Sets the listed attribute for the company
	 *
	 * @param boolean $listed
	 * @return void
	 */
	public function setListed($listed) {
		$this->listed = $listed;
	}

	/**
	 * Returns the listed attribute for the company
	 *
	 * @return boolean
	 */
	public function getListed() {
		return $this->listed;
	}

	/**
	 * Sets the revenue for the company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Revenue $revenue
	 * @return void
	 */
	public function setRevenue($revenue) {
		$this->revenue = $revenue;
	}

	/**
	 * Returns the revenue
	 *
	 * @return Tx_Typo3Agencies_Domain_Model_Revenue
	 */
	public function getRevenue() {
		return $this->revenue;
	}

	/**
	 * Sets the deactivation flag
	 * @param boolean $deactivated
	 * @return void
	 */
	public function setDeactivated($deactivated){
		$this->deactivated = $deactivated;
	}

	/**
	 * Returns the deactivation flag
	 * @return boolean The deactivation flag
	 */
	public function isDeactivated(){
		return $this->deactivated;
	}

	/**
	 * Sets the sorting index
	 * @param integer $sorting
	 */
	public function setSorting($sorting){
		$this->sorting = $sorting;
	}

	/**
	 * Returns the sorting index
	 * @return integer The sorting index
	 */
	public function getSorting(){
		return $this->sorting;
	}

	/**
	 * Returns the last updated time
	 *
	 * @return int
	 */
	public function getTstamp() {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tstamp', 'tx_typo3agencies_domain_model_reference', 'uid = ' . intval($this->getUid()));
		if(!$result){
			$row = current($result);
			return $row['tstamp'];
		}
		return FALSE;
	}
}
?>
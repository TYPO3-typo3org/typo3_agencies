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
class Tx_Typo3Agencies_Domain_Model_Agency extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Administrator
	 *
	 * @var integer
	 */
	protected  $administrator;
	
	/**
	 * Array of references
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Typo3Agencies_Domain_Model_Reference>
	 * @lazy
	 */
	protected  $references;
	
	/**
	 * Name of the agency
	 *
	 * @var string
	 * @validate NotEmpty,StringLength(maximum = 255)
	 */
	protected $name = '';
	
	/**
	 * Logo of the agency
	 *
	 * @var string
	 */
	protected $logo = '';
	
	/**
	 * A link to the agency
	 *
	 * @var string
	 * @validate NotEmpty,StringLength(maximum = 255)
	 */
	protected $link = '';
	
	/**
	 * About the agency
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $about = '';
	
	/**
	 * Member status: 0 = no member, 1 = premium, 2 = platin
	 * 
	 *  @var string
	 */
	protected $member = '';
	
	/**
	 * Approved or not?
	 * 
	 * @var boolean
	 */
	protected $approved = false;
	
	/**
	 * Amount of case studies allowed to be shown
	 * 
	 * @var integer
	 */
	protected $casestudies = 0;
	
	/**
	 * The code to sync with the association
	 * 
	 * @var string
	 */
	protected $code = '';

	/**
	 * Salutation
	 *
	 *  @var string
	 */
	protected $salutation = '';

	/**
	 * FirstName
	 *
	 *  @var string
	 */
	protected $firstName = '';

	/**
	 * LastName
	 *
	 *  @var string
	 */
	protected $lastName = '';

	/**
	 * Email
	 *
	 *  @var string
	 *  @validate EmailAddress,StringLength(maximum = 100)
	 */
	protected $email = '';

	/**
	 * Address
	 *
	 *  @var string
	 *  @validate StringLength(maximum = 255)
	 */
	protected $address = '';

	/**
	 * Zip
	 *
	 *  @var string
	 *  @validate StringLength(maximum = 50)
	 */
	protected $zip = '';

	/**
	 * City
	 *
	 *  @var string
	 *  @validate StringLength(maximum = 100)
	 */
	protected $city = '';
	
	/**
	 * Country
	 *
	 *  @var string
	 */
	protected $country = '';
	
	/**
	 * Contact
	 *
	 *  @var string
	 *  @validate NotEmpty,StringLength(maximum = 100)
	 */
	protected $contact = '';

	/**
	 * TrainingService
	 *
	 *  @var boolean
	 */
	protected $trainingService = false;

	/**
	 * HostingService
	 *
	 *  @var boolean
	 */
	protected $hostingService = false;

	/**
	 * DevelopmentService
	 *
	 *  @var boolean
	 */
	protected $developmentService = false;

	/**
	 * Latitude
	 *
	 *  @var float
	 */
	protected $latitude = 0;

	/**
	 * Longitude
	 *
	 *  @var float
	 */
	protected $longitude = 0;
	
	/**
	 * Constructs a new Agency
	 *
	 */
	public function __construct() {
		$this->references = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * Sets the administrator
	 *
	 * @param int
	 * @return void
	 */
	public function setAdministrator($administrator) {
		$this->administrator = $administrator;
	}

	/**
	 * Returns the administrator
	 *
	 * @return int
	 */
	public function getAdministrator() {
		return $this->administrator;
	}
	
	/**
	 * Sets the references
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Typo3Agencies_Domain_Model_Reference> $references
	 * @return void
	 */
	public function setReferences($references) {
		$this->references = $references;
	}

	/**
	 * Returns the references
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Typo3Agencies_Domain_Model_Reference>
	 */
	public function getReferences() {
		return $this->references;
	}
	
	/**
	 * Sets the name of the agency
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the logo of the agency
	 *
	 * @param string $logo
	 * @return void
	 */
	public function setLogo($logo) {
		$this->logo = $logo;
	}

	/**
	 * Returns the logo
	 *
	 * @return string
	 */
	public function getLogo() {
		return $this->logo;
	}

	/**
	 * Sets the about for the agency
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
	 * Sets the link for the agency
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
	 * Sets the member status
	 *
	 * @param string $member
	 * @return void
	 */
	public function setMember($member) {
		$this->member = $member;
	}

	/**
	 * Returns the member status
	 *
	 * @return string
	 */
	public function getMember() {
		return $this->member;
	}

	/**
	 * Sets the trainingService status
	 *
	 * @param boolean $trainingService
	 * @return void
	 */
	public function setTrainingService($trainingService) {
		$this->trainingService = $trainingService;
	}

	/**
	 * Returns the trainingService status
	 *
	 * @return boolean
	 */
	public function getTrainingService() {
		return (boolean) $this->trainingService;
	}
	
	/**
	 * Sets the hostingService status
	 *
	 * @param boolean $hostingService
	 * @return void
	 */
	public function setHostingService($hostingService) {
		$this->hostingService = $hostingService;
	}

	/**
	 * Returns the hostingService status
	 *
	 * @return boolean
	 */
	public function getHostingService() {
		return (boolean) $this->hostingService;
	}

	/**
	 * Sets the developmentService status
	 *
	 * @param boolean $developmentService
	 * @return void
	 */
	public function setDevelopmentService($developmentService) {
		$this->developmentService = $developmentService;
	}

	/**
	 * Returns the developmentService status
	 *
	 * @return boolean
	 */
	public function getDevelopmentService() {
		return (boolean) $this->developmentService;
	}

	/**
	 * Sets the salutation status
	 *
	 * @param string $salutation
	 * @return void
	 */
	public function setSalutation($salutation) {
		$this->salutation = $salutation;
	}

	/**
	 * Returns the salutation status
	 *
	 * @return string
	 */
	public function getSalutation() {
		return $this->salutation;
	}

	/**
	 * Sets the firstName status
	 *
	 * @param string $firstName
	 * @return void
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * Returns the firstName status
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Sets the lastName status
	 *
	 * @param string $lastName
	 * @return void
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * Returns the lastName status
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Sets the email status
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the email status
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the address status
	 *
	 * @param string $address
	 * @return void
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * Returns the address status
	 *
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * Sets the zip status
	 *
	 * @param string $zip
	 * @return void
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}

	/**
	 * Returns the zip status
	 *
	 * @return string
	 */
	public function getZip() {
		return $this->zip;
	}

	/**
	 * Sets the city status
	 *
	 * @param string $city
	 * @return void
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * Returns the city status
	 *
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the country status
	 *
	 * @param string $country
	 * @return void
	 */
	public function setCountry($country) {
		$this->country = $country;
	}

	/**
	 * Returns the country status
	 *
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}
	
	/**
	 * Sets the contact status
	 *
	 * @param string $contact
	 * @return void
	 */
	public function setContact($contact) {
		$this->contact = $contact;
	}

	/**
	 * Returns the contact status
	 *
	 * @return string
	 */
	public function getContact() {
		return $this->contact;
	}
	
	/**
	 * Set code
	 * 
	 * @param string $code
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}
	
	/**
	 * Get code
	 * 
	 * @return string code
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Sets the latitude status
	 *
	 * @param float $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	/**
	 * Returns the latitude status
	 *
	 * @return float
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * Sets the longitude status
	 *
	 * @param float $longitude
	 * @return void
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	/**
	 * Returns the longitude status
	 *
	 * @return float
	 */
	public function getLongitude() {
		return $this->longitude;
	}
	
	/**
	 * Returns true if one of the coordinates is not zero
	 * @return boolean 
	 */
	public function isGeolocation() {
		return $this->getLatitude() != 0 || $this->getLongitude() != 0;
	}

	/**
	 * Returns true if the agency has been approved
	 * @return boolean
	 */
	public function getApproved(){
		return $this->approved;
	}
	
	/**
	 * Returns the number of case studies allowed to be displayed
	 * @return integer
	 */
	public function getCasestudies(){
		return $this->casestudies;
	}
}
?>
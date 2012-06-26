<?php

/***************************************************************
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
***************************************************************/

/**
 * An Revenue
 *
 * @version $Id:$
 * @copyright Copyright belongs to the respective authors
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Domain_Model_Revenue extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * The title
	 *
	 * @var string
	 */
	protected $title = array();
	
	/**
	 * Reference sorting
	 *
	 * @var integer
	 */
	protected $sorting = 0;

	/**
	 * Constructs a new Revenue
	 *
	 */
	public function __construct() {
	}
	
	/**
	 * Sets this revenue title
	 *
	 * @param string $title The revenue title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Returns the revenue title
	 *
	 * @return string The revenue title
	 */
	public function getTitle() {
		return $this->title;
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

}
?>
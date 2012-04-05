<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Tolleiv Nietch
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
 * View helper for rendering countries
 *
 * = Examples =
 */
class Tx_Typo3Agencies_ViewHelpers_ScriptViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;
	
	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @var t3lib_PageRenderer
	 */
	protected $pageRenderer;
	
	/**
	 * @param t3lib_PageRenderer $pageRenderer
	 */
	public function injectPageRenderer(t3lib_PageRenderer $pageRenderer) {
		$this->pageRenderer = $pageRenderer;
	}	
	
	/**
	 * Returns TRUE if what we are outputting may be cached
	 *
	 * @return boolean
	 */
	protected function isCached() {
		$userObjType = $this->configurationManager->getContentObject()->getUserObjectType();
		return ($userObjType !== tslib_cObj::OBJECTTYPE_USER_INT);
	}
	
    /**
     * @param string $content
     * @param bool $inline
     * @param bool $compress
     * @param bool $forceOnTop
     * @return void
     */
	public function render($compress=TRUE, $forceOnTop=FALSE) {
        $content = $this->renderChildren();
        
        if ($this->isCached()) {
        	$this->pageRenderer->addJsFooterInlineCode(md5($content), $content, $compress, $forceOnTop);
        } else {
        	// additionalFooterData not possible in USER_INT
        	$GLOBALS['TSFE']->additionalHeaderData[md5($content)] = t3lib_div::wrapJS($content);
        }
        
	}
}
?>
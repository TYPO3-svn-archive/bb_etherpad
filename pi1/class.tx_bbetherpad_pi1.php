<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Bastian Bringenberg <spam@bastian-bringenberg.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   50: class tx_bbetherpad_pi1 extends tslib_pibase
 *   72:     function main($content, $conf)
 *  109:     function init()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath("bb_etherpad").'pi1/class.Connector.php');
require_once(t3lib_extMgm::extPath("bb_etherpad").'pi1/class.ConnectorConfig.php');


/**
 * Plugin 'Etherpad' for the 'bb_etherpad' extension.
 *
 * @author	Bastian Bringenberg <spam@bastian-bringenberg.de>
 * @package	TYPO3
 * @subpackage	tx_bbetherpad
 */
class tx_bbetherpad_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_bbetherpad_pi1';
	public $scriptRelPath = 'pi1/class.tx_bbetherpad_pi1.php';
	public $extKey        = 'bb_etherpad';
	public $pi_checkCHash = true;
	private $lConf;
	private $connectorConfig;
	private $connector;
	private $page="";
	private $link ="";
	private $readOnly=false;
	private $showSideBar=false;
	private $template;


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->init();


		if($this->link!=""){
			if($this->readOnly){
				$content = $this->connector->getPage($this->page);
				$template = $this->cObj->getSubpart($this->template, '###TEMPLATE_READONLY###');
				$markerArray=array();
				$markerArray['###CONTENT###']=$content;
				/** here could be a hook **/
				$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, array(), array());
			}else{
				$tmpLink=$this->link;
				$additional=array();
				$additional[]=$this->showSideBar?"sidebar=1":"sidebar=0";
				if(count($additional)>0) $tmpLink.="?".implode("&",$additional);
				$template = $this->cObj->getSubpart($this->template, '###TEMPLATE_IFRAME###');
				//$content = '<iframe src="'.$tmpLink.'" id="test"></iframe>';
				$markerArray['###LINK###']=$tmpLink;
				$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, array(), array());
			}
		}else{
			Throw new Exception($this->pi_getLL('error_noLink'));
			$content=$this->pi_getLL('error_noLink');
		}

		/** here could be a hook **/
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * The init method
	 *
	 * @return	Builds		default privates
	 */
	function init(){
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->connectorConfig=new ConnectorConfig();
		$this->connector=new Connector($this->connectorConfig);
		$this->link = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'link', 'vDEF');
		$index = $GLOBALS['TSFE']->sys_language_uid;
		$piFlexForm=$this->cObj->data['pi_flexform'];
		$sDef = current($piFlexForm['data']);
		$lDef = array_keys($sDef);
		foreach ( $piFlexForm['data'] as $sheet => $data ) {
		    foreach ($data[$lDef[$index]] as $key => $val ) {
		        $this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet,$lDef[$index]);
		    }
		}
		$this->readOnly=$this->lConf['readonly'];
		$this->link=$this->lConf['link'];
		$this->showSideBar=($this->lConf['showSideBar']==1);
		$tmp=str_replace("http://","",$this->link);
		$tmp=preg_split("#/#is",$tmp);
		$this->page=$tmp[1];
		$this->connectorConfig->padHost=$tmp[0];
		$this->connectorConfig->padUser=$this->lConf['proUser'];
		$this->connectorConfig->padPass=$this->lConf['proUserPw'];
		$this->connectorConfig->debug=false;
		$this->connectorConfig->cookieJar=tempnam("/tmp", "/tmp/bb_etherpad_".$GLOBALS['TSFE']->id);
		$cssFile = $GLOBALS['TSFE']->tmpl->getFileName($this->conf['cssFile']);
		$GLOBALS['TSFE']->additionalHeaderData['tx_bb_etherpad_pi1'] = '<link rel="stylesheet" type="text/css" media="all" href="'.$cssFile.'">';
		$this->template = $this->cObj->fileResource($this->conf['templateFile']);


		/** here could be a hook **/
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bb_etherpad/pi1/class.tx_bbetherpad_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bb_etherpad/pi1/class.tx_bbetherpad_pi1.php']);
}

?>
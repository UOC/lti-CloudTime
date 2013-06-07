<?php
/**
 * 
 * Blti Wrapper for UOC. http://www.imsglobal.org/lti/blti/bltiv1p0/ltiBLTIimgv1p0.html.   
 *
 * Copyright (c) 2011 Universitat Oberta de Catalunya
 * 
 * This file is part of Campus Virtual de Programari Lliure (CVPLl).  
 * CVPLl is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * General Public License for more details, currently published 
 * at http://www.gnu.org/copyleft/gpl.html or in the gpl.txt in 
 * the root folder of this distribution.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.   
 *
 *
 * Author: Antoni Bertran / UOC / abertranb@uoc.edu
 * Date: January 2011
 *
 * Project email: campusproject@uoc.edu
 *
 **/

require_once(dirname(__FILE__).'/../ims-blti/blti.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'UtilsPropertiesBLTI.php');

class bltiUocWrapper extends BLTI {
	
	var $configuration = null;
	var $configuration_file = null;

	/** 
	 * Added base64 support
	 * @param $parm
	 * @param $usesession
	 * @param $doredirect
	 */
	 function __construct($usesession=true, $doredirect=true, $overwriteconfigurationfile=null) {

	 	$default_conf_file = (dirname(__FILE__).'/../configuration/authorizedConsumersKey.cfg');
	 	if ($overwriteconfigurationfile!=null)
	 		$default_conf_file = $overwriteconfigurationfile;
	 	$this->configuration_file = $default_conf_file;
	 	$this->loadConfiguration();
	 	if ($this->isValidConsumerKey()) {
	 		parent::__construct($this->getSecret(), $usesession, $doredirect);
		  	$newinfo = $this->info;
		  	if (isset($newinfo['custom_lti_message_encoded_base64']) && $newinfo['custom_lti_message_encoded_base64']==1)
       			$newinfo = $this->decodeBase64($newinfo);

       		$this->info = $newinfo;
	 	}
	 }
	 
	 /**
	  * This function try to load configuration
	  */
	 function loadConfiguration() {
	 	
	 	if ($this->configuration == null) {
		 	//If everything ok then load configuration
			$this->configuration = new UtilsPropertiesBLTI($this->configuration_file);
	 	}
		return $this->configuration;
	 }
	 
	 /**
	  * Gets the consumer guid from post to load the configuration
	  * This is a unique identifier for the TC.  
	  * A common practice is to use the DNS of the organization or the DNS 
	  * of the TC instance.  
	  * If the organization has multiple TC instances, 
	  * then the best practice is to prefix the domain name with a locally 
	  * unique identifier for the TC instance.
	  */
	function getConsumerKeyFromPost() {
		//Never encoded base64
		$tool_consumer_instance_guid = $_POST['oauth_consumer_key'];
		return $tool_consumer_instance_guid;
	} 
	 
	/**
	 * This function uses the consumer key to get if exists and is enabled
	 */
	function isValidConsumerKey() {
		
		try {

			$key = 'consumer_key.'.$this->getConsumerKeyFromPost().'.enabled';
			return $this->configuration->getProperty($key)=='1';
			
		} catch (Exception $e) {
			return false;
		}
		
	}
	
	/**
	 * This function uses the consumer key to get the secret
	 */
	function getSecret() {
		
		try {

			$key = 'consumer_key.'.$this->getConsumerKeyFromPost().'.secret';
			return $this->configuration->getProperty($key);
			
		} catch (Exception $e) {
			return false;
		}
		
	}
	/**
	 * This function gets the User key format of UOC
	 * @see IMSBasicLTI/ims-blti/BLTI#getUserKey()
	 */
    function getUserKey() {
       $lis_person_sourcedid = $this->info['lis_person_sourcedid'];
       $oauth = $this->info['oauth_consumer_key'];
       $pos = strpos($lis_person_sourcedid,$oauth.":");
       if ($pos !== false && strlen($lis_person_sourcedid)>0)
         return $lis_person_sourcedid;
         $id = $this->info['user_id'];
        if ( strlen($id) > 0 && strlen($oauth) > 0 ) return $oauth . ':' . $id;
        return false;
    }
 
    /**
     * Data submitter are in base64 then we have to decode
     * @author Antoni Bertran (antoni@tresipunt.com)
     * @param $info array
     */
    function decodeBase64($info) {
      $keysNoEncode = array("lti_version", "lti_message_type", "tool_consumer_instance_description", "tool_consumer_instance_guid", "oauth_consumer_key", "custom_lti_message_encoded_base64", "oauth_nonce", "oauth_version", "oauth_callback", "oauth_timestamp", "basiclti_submit", "oauth_signature_method");
      foreach ($info as $key => $item){
        if (!in_array($key, $keysNoEncode))
          $info[$key] = base64_decode($item);
      }
      return $info;
    }
    

    /**
     * to get firstName of user
     * @see BLTI::getUserShortName()
     */
    function getUserFirstName() {
        $givenname = $this->info['lis_person_name_given'];
        $familyname = $this->info['lis_person_name_family'];
        $fullname = $this->info['lis_person_name_full'];
        if ( strlen($givenname) > 0 ) return $givenname;
        if ( strlen($familyname) > 0 ) return $familyname;
        return $this->getUserName();
    }

    /**
     * to get firstName of user
     * @see BLTI::getUserShortName()
     */
    function getUserLastName() {
        $familyname = $this->info['lis_person_name_family'];
        $fullname = $this->info['lis_person_name_full'];
        if ( strlen($familyname) > 0 ) return $familyname;
        return $this->getUserName();
    }
}
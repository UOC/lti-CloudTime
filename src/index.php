<?php
/**
 * 
 * CloudTime UOC. http://www.campusproject.org/lti
 *
 * Copyright (c) 2013 Universitat Oberta de Catalunya
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
 * Date: January 2013
 *
 * Project email: campusproject@uoc.edu
 *
 **/
if (!class_exists("bltiUocWrapper")) {
	require_once dirname(__FILE__).'/lib/IMSBasicLTI/uoc-blti/bltiUocWrapper.php';
	require_once dirname(__FILE__).'/lib/IMSBasicLTI/ims-blti/blti_util.php';
	require_once dirname(__FILE__).'/lib/IMSBasicLTI/utils/UtilsPropertiesBLTI.php';
}
//Incluim la BD
require_once('constants.php');
require_once('gestorBD.php');

require_once('utils.php');
require_once('lang.php');
/**
 * 
 * Checks if is a LTI call, and then do the SSO and create and join roles if it is necessary
 */
function lti_init() {

	if ( ! is_basic_lti_request() ) { 
		$good_message_type = $_REQUEST["lti_message_type"] == "basic-lti-launch-request";
		$good_lti_version = $_REQUEST["lti_version"] == "LTI-1p0";
		$resource_link_id = $_REQUEST["resource_link_id"];
		if ($good_message_type && $good_lti_version && !isset($resource_link_id) ) {
			$launch_presentation_return_url = $_REQUEST["launch_presentation_return_url"];
			if (isset($launch_presentation_return_url)) {
				header('Location: '.$launch_presentation_return_url);
				exit();
			} else {
				show_error("Is not a valid LTI request");
			}
		} else {
			show_error("Is not a valid LTI request");
		}
		return false; 
	}

//	if (session_status() == PHP_SESSION_NONE) { PHP 5.4
	if(!isset($_SESSION)) { 
		session_start();
	}
    // See if we get a context, do not set session, do not redirect
    $context = new bltiUocWrapper(false, false);
    if ( ! $context->valid ) {
    	show_error("Signature is not a valid");
        exit;
    }
    
    try {
    	$gestorBD = new GestorBD();
	    //Check if user exists
	    // Set up the user...
		if (!is_lti_error_data($context)) {
		    $username = lti_get_username($context);
		    $name = $context->getUserName();
		    $name = isset($name) && $name ? $name : $username;
	    	$email = $context->getUserEmail();
	    	$admin = strpos(strtolower($context->info['roles']), ADMINISTRATOR_ROLE)!==false;
		    $is_instructor = $context->isInstructor();
		    $is_learner = strpos(strtolower($context->info['roles']), LEARNER_ROLE)!==false;
		    if ($admin || $is_instructor || $is_learner) {
			    $course = false;
			    $user_id = false;
			    $image = $context->getUserImage();
			    $user = $gestorBD->get_user_by_username($username);
			    if (!$user) {
			    	
			    	$user_id = $gestorBD->register_user($username, $name, $email, $image);
					if ($user_id) {
						$user = $gestorBD->get_user_by_username($username);
					} else {
						show_error(Language::get("register_user_error"));
						$user=false;
					}
					
			    } else {
					 
			    	if (!$gestorBD->update_user($username, $name, $email, $image))
			    	{
			    		show_error(Language::get("updated_user_error"));
			    		$user = false;
			    	}
			    	 
			    }
			    if ($user) {
				    $user_id = $user['id'];
			    
				    //Check if course exists
					$course_name = lti_get_course_name($context);
					$course_key = $context->getResourceKey();
					//$course_key = $context->getCourseKey();
					
			
					$course = $gestorBD->get_course_by_courseKey($course_key);
					$region = lti_get_aws_region($context);
					$aws_configuration = isset($context->info[FIELD_OTHER_CONF])?$context->info[FIELD_OTHER_CONF]:DEFAULT_AWS_ACCOUNT;
					if (!$course) {
						if (!$gestorBD->register_course($course_key, $course_name, $region, $aws_configuration)) {
							show_error(Language::get('lti:errorregistercourse'));
						}
						$course = $gestorBD->get_course_by_courseKey($course_key);
					}
					else {
						if (!$gestorBD->update_course($course_key, $course_name, $region, $aws_configuration)) {
							show_error(Language::get('lti:errorupdatingcourse'));
						}
					}
					if ($course) {
						$course_id= $course['id'];
						if (!$gestorBD->join_course($course_id, $user_id, $is_instructor)) {
							show_error(Language::get('lti:errorjoincourse'));
						}
					}
			    }
			    if ($course && $user) {
			    	//Fem el login i guardem dades a sessio
			    	$_SESSION[USER_ID] = $user['id'];
			    	$_SESSION[USERNAME] = $user['userKey'];
			    	$_SESSION[FULLNAME] = $user['fullname'];
			    	$_SESSION[EMAIL] = $user['email'];
			    	$_SESSION[COURSE_ID] = $course['id'];
			    	$_SESSION[CUSTOM_AWS_REGION] = $course['amazon_region'];
			    	$_SESSION[IS_INSTRUCTOR] = $is_instructor;
			    	$_SESSION[CONTEXT_ID] = lti_get_context_id($context);
			    	$_SESSION[INSTANCE_ID] = lti_get_instance_id($context);
			    	$_SESSION[SESSION_ID_FIELD] = lti_get_session_id($context);
			    	$_SESSION[LANG] = lti_get_lang($context);
			    	//abertranb 20131003 allow multiple sites
			    	$_SESSION[FIELD_OTHER_CONF] = $aws_configuration;
			    	//****** END
			    	$url = 'getIP.php';
			    	if ($is_instructor) {
			    		$url = 'index_instructor.php';
			    	}
			    	header('Location: '.$url);
			    }
		    } else {
		    	show_error(Language::get('lti:errornotauthorized'));
		    }
		}
    } catch (RegistrationException $r) {
			show_error($r->getMessage());
	}
	catch (Exception $r) {
		show_error($r->getMessage());
	}

}

/**
 * 
 * Check if there is any error
 * @param unknown_type $context
 * @return boolean
 */
function  is_lti_error_data($context){
	$error = false;
	if (!$context->getResourceKey() || strlen($context->getResourceKey())==0) {
	//if (!isset($context->info['context_id']) || strlen($context->info['context_id'])==0) {
		$error = true;
		show_error(Language::get("lti:resource_id_required"));
	/*} elseif (!isset($context->info['roles']) || strlen($context->info['roles'])==0) {
		$error = true;
		show_error("lti:role_necessary");
	}  elseif (!$context->getUserEmail() || strlen($context->getUserEmail())==0) {
		$error = true;
		show_error("lti:emailnotprovided");*/
	}
	return $error;
}

/**
*
* Get the courseName
*/
function lti_get_course_name($context) {
	$resourceTitle = $context->getResourceTitle();
	if (!$resourceTitle) {
		$resourceTitle = $context->getResourceKey();
	}
	$courseName = $context->getCourseTitle();
	$courseName .= strlen($courseName)==0?'':' - ';
	$courseName .= $resourceTitle
	;
/*	$concat_resource_link_id = 0;
	try {
		//we have a configuration file on indicates which fields are mapping
		require_once dirname(__FILE__).'/IMSBasicLTI/utils/UtilsPropertiesBLTI.php';
		$conf_file = (dirname(__FILE__).'/configuration/mappingFields.cfg');
		$prop = new UtilsPropertiesBLTI($conf_file);
		$concat_resource_link_id = $prop->getProperty('concat_resource_link_id');
		if ($concat_resource_link_id == '1') {
    			$courseName .= ' '.$context->info['resource_link_id'];
		}
	} catch (Exception $e) {
		//Problem in configuration
	   	$concat_resource_link_id = 0;
	}
	*/ 
	return $courseName;
	 
}

/**
 * 
 * Gets the course id of name
 * @param unknown_type $course_name
 */
function lti_get_course_id_by_name($course_name) {
	$course_name = sanitise_string($course_name);
	//TODO Elgg hasn't API to get the group id with name
	$group = get_data_row("SELECT g.*, e.owner_guid, e.container_guid from {$CONFIG->dbprefix}groups_entity g
	inner join {$CONFIG->dbprefix}entities e on e.guid=g.guid where name='".$group_name."'");
	
	return $group;
}


function lti_map_as_admin($prop) {
	try {$mapping_as_admin = $prop->getProperty('mapping_as_admin');}
 	catch (Exception $e) {
		//Problem in configuration 
		$mapping_as_admin = 0;
	}
 return $mapping_as_admin;
}
/**
 * 
 * Sanitazes a string
 * @param unknown_type $str
 */
function sanitise_string($str) {
	return str_replace('-','_',str_replace(':','_',$str));
}

function lti_get_username($context) {
	$username = $context->getUserKey();
	if (isset($context->info[USERNAME])) {
		$username = $context->info[USERNAME];
	}
    $username = sanitise_string($username);  
	return $username;
}

function lti_get_context_id($context) {
	return $context->info[CONTEXT_ID];
}

function lti_get_instance_id($context) {
	return $context->info[INSTANCE_ID];
}

function lti_get_session_id($context) {
	return $context->info[SESSION_ID_FIELD];
}

function lti_get_lang($context) {
	$lang = 'en-US';
	if (isset($context->info[LAUNCH_PRESENTATION_LOCALE])) {
		$custom_lang_id = $context->info[LAUNCH_PRESENTATION_LOCALE];
	}
	$custom_lang_id = ''; 
	if (isset($context->info[CUSTOM_LANG])) {
		$custom_lang_id = $context->info[CUSTOM_LANG];
		switch ($custom_lang_id)
		{
			case "a":
				$lang="ca-ES";
				break;
			case "b":
				$lang="es-ES";
				break;
			case "d":
				$lang="fr-FR";
				break;
			default:
				$lang="en-US";
		}
	}
	
	if (strlen($lang)<4){
		switch ($lang)
		{
			case "en":
				$lang="en_US";
				break;
			case "es":
				$lang="es_ES";
				break;
			case "ca":
				$lang="ca_ES";
				break;
			case "fr":
				$lang="fr_FR";
				break;
			default:
				$lang="en_US";
		}
	}
	return $lang;	
}

/**
 * Gets the region of this course
 * @param unknown $context
 * @return NULL
 */
function lti_get_aws_region($context) {
	$region = null;
	if (isset($context->info[CUSTOM_AWS_REGION]))
		$region = $context->info[CUSTOM_AWS_REGION];
	return $region;
}

lti_init();
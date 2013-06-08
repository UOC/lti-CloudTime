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
/**
*
* Gets if file exists in include path or directely
* @param unknown_type $file
*/
function filexists_aws($file)
{
	$ps = explode(":", ini_get('include_path'));
	if ($ps) {
		foreach($ps as $path)
		{
			if(file_exists($path.'/'.$file)) return true;
		}
	}
	if(file_exists($file)) return true;
	return false;
}

function getDeviceMapping($item) {
	$p = $item->deviceName;
	if (isset($item->ebs)) {
		$p .= '<br>&nbsp;VolumeId = '.$item->ebs->volumeId;
		$p .= '<br>&nbsp;status = '.$item->ebs->status;
		//		$p .= ' attachTime = '.$item->ebs->attachTime;
		//		$p .= ' deleteOnTermination = '.$item->ebs->deleteOnTermination;
	}
	return $p;
}

function in_array_value($needle, $haystack) {
	if(in_array($needle, $haystack)) {
        return true;
    }
    foreach($haystack as $element) {
        if(is_array($element) && in_array_value($needle, $element)) {
            return true;
    	}
    }
    return false;
}
function printEc2Error($response) {
	$str = '';
	if (isset($response->body)) {
		foreach ($response->body->Errors as $error) {
			$str .=  "<p>".$error->Error->Code.". ".$error->Error->Message."</p>";
		}
	} else {
		$str = $response;
	}
	return $str;
}

function tornaData($launchTime) {
	$launchTime = strtotime($launchTime);
	$d = date("d/m/Y h:m:s",$launchTime);
	return $d;
}

function quantTempsEncesa($launchTime) {
	$launchTime = strtotime($launchTime);
	$fa_quant_q_esta_encesa = round((time() - $launchTime)/3600,2);
	return $fa_quant_q_esta_encesa;
}
function paintDeviceMapping($item){
	
	return getDeviceMapping($item);
}
function sanitizeFilename($f) {
 // a combination of various methods
 // we don't want to convert html entities, or do any url encoding
 // we want to retain the "essence" of the original file name, if possible
 // char replace table found at:
 // http://www.php.net/manual/en/function.strtr.php#98669
 $replace_chars = array(
     'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
     'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
     'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
     'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
     'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
     'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
     'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
 );
 $f = strtr($f, $replace_chars);
 // convert & to "and", @ to "at", and # to "number"
 $f = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $f);
 $f = preg_replace('/[^(\x20-\x7F)]*/','', $f); // removes any special chars we missed
 $f = str_replace(' ', '-', $f); // convert space to hyphen 
 $f = str_replace('\'', '', $f); // removes apostrophes
 $f = preg_replace('/[^\w\-\.]+/', '', $f); // remove non-word chars (leaving hyphens and periods)
 $f = preg_replace('/[\-]+/', '-', $f); // converts groups of hyphens into one
 return strtolower($f);
}
/**
 * 
 * Shows the message
 * @param unknown_type $msg
 */
function show_error($msg) {
	$course_title = $title = 'Error';
	require_once('header.php');
	require_once('end_header_navbar.php');
	echo '<div class="alert alert-error">'.$msg.'</div>';
	require_once('footer.php');
	exit();
}

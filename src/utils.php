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
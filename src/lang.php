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
require_once 'constants.php';
if (!$_SESSION)
	session_start();
$lang = $_SESSION[LANG];
if (!$lang) {
	$lang = 'es-ES';
	$_SESSION[LANG] = $lang;
}
//$lang = 'en-US';
require_once dirname(__FILE__).'/lang/'.$lang.'.php';

class Language {

	/**
	 * 
	 * Get the translation
	 * @param string $string
	 */
	public static function get($string) {
		global $language;		
		if (isset($language[$string])) {
			$string = $language[$string];
		}	
		return $string;
	}
	
	/**
	 *
	 * Get the translation and do sprintf
	 * @param string $string
	 * @param string $subs
	 */
	public static function getTag($string, $subs) {
		global $language;
		if (isset($language[$string])) {
			$string = $language[$string];
		}
		return sprintf($string, $subs);
	}
	
	/**
	 *
	 * Get the translation and do sprintf
	 * @param string $string
	 * @param string $subs1
	 * @param string $subs2
	 */
	public static function getTagDouble($string, $subs1, $subs2) {
		global $language;
		if (isset($language[$string])) {
			$string = $language[$string];
		}
		return sprintf($string, $subs1, $subs2);
	}

}
?>
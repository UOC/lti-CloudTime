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
require_once 'utils.php';
require_once('gestorBD.php');
require_once('lang.php');

$gestorBD = new GestorBD();

$course_id = $_SESSION[COURSE_ID];
$user_id = $_SESSION[USER_ID];
$username = $_SESSION[USERNAME];
$is_instructor = $_SESSION[IS_INSTRUCTOR]==1;
$gestorBD = new GestorBD();
$course_name = '';
$course_title = false;
$obj_course = $gestorBD->get_course_by_id($course_id);
if ($obj_course!=false) {
	$course_name = $obj_course['courseKey'];
}
if (!$course_id || !$user_id || !$username || $course_title) {
	show_error(Language::get("no estas autoritzat"));
}
if (defined('PEM_PROTECTED_FOLDER')) {
	$file = PEM_PROTECTED_FOLDER.DIRECTORY_SEPARATOR.sanitizeFilename($course_name).'.pem';

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
	
} 
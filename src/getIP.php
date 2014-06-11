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


//	error_reporting(-1);

	// Set HTML headers
	header("Content-type: text/html; charset=utf-8");

	// Include the SDK
	require_once './sdk.class.php';
	require_once 'constants.php';
	require_once 'gestorBD.php';
	require_once('lang.php');
	require_once('utils.php');
	
	
$course_id = $_SESSION[COURSE_ID];
$user_id = $_SESSION[USER_ID];
$username = $_SESSION[USERNAME];
$is_instructor = $_SESSION[IS_INSTRUCTOR]==1;
$gestorBD = new GestorBD();
if (!$course_id || !$user_id || !$username) {
	show_error(Language::get("no estas autoritzat"));
}
if ($is_instructor) {
	$InstanceId = $_REQUEST[INSTANCEC2ID];
	if ($InstanceId) {
		$_SESSION[INSTANCEC2ID] = $InstanceId;
		$user = $gestorBD->get_user_by_username($username);		
	} else {
		$InstanceId = $_SESSION[INSTANCEC2ID];
	}
} 
else { 
	$InstanceId = $gestorBD->getInstanciaPerUsuariCurs($user_id, $course_id);
}
$course_title = Language::get('Ec2CourseInterface');
$obj_course = $gestorBD->get_course_by_id($course_id);
$has_key_stored = 0;
$course_name = '';
$custom_instructions = '';
$custom_aws_username = '';

if ($obj_course!=false) {
	$course_title = $obj_course['title'];
	$course_name = $obj_course['courseKey'];
	$has_key_stored = $obj_course['has_key_stored']==1;
	$custom_instructions = $obj_course['instructions'];
	$custom_aws_username = $obj_course['aws_username_student'];	
}
if (!$custom_aws_username || strlen($custom_aws_username)==0) {
	$custom_aws_username = DEFAULT_USERNAME_AWS;
}
$operation 	= isset($_POST['operation'])?$_POST['operation']:'';
$name		= isset($_POST['name'])?$_POST['name']:'';
$task	 	= isset($_POST['task'])?$_POST['task']:'';
$extra	 	= isset($_POST['extra'])?$_POST['extra']:'';
$action     = isset($_POST['action'])?$_POST['action']:'';

$ec2 = new AmazonEC2(array('key' => AWS_KEY, 'secret' => AWS_SECRET_KEY));
if ($_SESSION[CUSTOM_AWS_REGION] && strlen($_SESSION[CUSTOM_AWS_REGION]))
	$ec2->set_region($_SESSION[CUSTOM_AWS_REGION]);

if ($InstanceId && $task==CHANGESTATE) {
	if ($extra==STATERUNNING){
		$ec2->stop_instances($InstanceId);
	} else {
		$ec2->start_instances($InstanceId);
	}
}

$title = Language::get('Ec2 instance');
include ('includes/header.php');
?>
<script type="text/javascript">
function canviaEstat(estat_actual, id) {
	if ("<?php echo Language::get('Segur que vol canviar lestat a la instancia')?> "+id+"?") {
		document.f.id.value = id;
		document.f.extra.value = estat_actual;
		document.f.task.value = '<?php echo CHANGESTATE;?>';
		document.f.submit();
	}
}
function refresca() {
	document.location.reload();
}
</script>
<?php	
$show_breadbrumbs = true;
include_once('end_header_navbar.php');
if (!$InstanceId) {
	echo(Language::get("no tens instancies assignades"));
} else {?>

		<form action="" method="post" name="f">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="extra" value="" />
		<?php 
			// Instantiate the AmazonEC2 class			
			include_once('includes/myinstancesStudent.php');
		?>
		</form>
<?php } 
include('includes/footer.php');
$gestorBD->desconectar();
?>
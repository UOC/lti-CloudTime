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
	
	
$course_id = $_SESSION[COURSE_ID];
$user_id = $_SESSION[USER_ID];
$username = $_SESSION[USERNAME];
$is_instructor = $_SESSION[IS_INSTRUCTOR]==1;
$gestorBD = new GestorBD();
if (!$course_id || !$user_id || !$username) {
	die(Language::get("no estas autoritzat"));
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
if ($obj_course!=false) {
	$course_title = $obj_course['title'];
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

?>
<html>
<head><title><?php echo Language::get('Ec2 instance')?></title>
<link rel="shortcut icon" href="images/favicon.ico">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/index.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
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
</head>
<body class="body-with-breadcrumbs">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse">
						  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
					  </a>
					  <a class="brand" href="#"> <img src="images/logo.png"><?php echo $course_title?></a>
					    <?php include('logos.php');?>

					<!--ul class="nav">
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
					</ul-->
				</div><!--/.nav-collapse -->
			</div>
		</div>
<?php 
	if ($is_instructor) {
?>
	<ul class="breadcrumb">
	  <li>
	    <a href="index_instructor.php"><?php echo Language::get('home') ?></a> <span class="divider">></span>
	  </li>
	  <li class="active"><?php echo $InstanceId?></li>
	</ul>
<?php } ?>
</div>
<div class="container">
<?php	if (!$InstanceId) {
	echo(Language::get("no tens instancies assignades"));
} else {?>

		<form action="" method="post" name="f">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="extra" value="" />
		<?php 
			// Instantiate the AmazonEC2 class			
			include_once('myinstancesStudent.php');
		?>
		</form>
<?php } ?>		
	      <hr>

	      <footer>
	              <?php include('logos_footer.php');?></a>
	      </footer>
</div>

    </div> 
  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="http://code.jquery.com/jquery.min.js"></script>
  <script src="js/bootstrap/bootstrap.min.js"></script>
    

</body>
</html>
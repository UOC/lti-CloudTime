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

	// Enable full-blown error reporting. http://twitter.com/rasmus/status/7448448829
	//error_reporting(-1);

	// Set HTML headers
	header("Content-type: text/html; charset=utf-8");

	// Include the SDK
	require_once './sdk.class.php';
	require_once 'constants.php';
	require_once 'utils.php';
	//Incluim la BD
	require_once('gestorBD.php');
	require_once('lang.php');

	$gestorBD = new GestorBD();
	

$operation 	= isset($_POST['operation'])?$_POST['operation']:MYINSTANCES;
$id			= isset($_POST['id'])?$_POST['id']:'';
$instances	= isset($_POST['instances'])?$_POST['instances']:'';
$task	 	= isset($_POST['task'])?$_POST['task']:'';
$extra	 	= isset($_POST['extra'])?$_POST['extra']:'';
$action     = isset($_POST['action'])?$_POST['action']:'';
$busca     	= isset($_POST['boto'])?$_POST['boto']!='':'';
if ($_POST['boto'] == STARTSELECT || $_POST['boto'] == STOPSELECT ) {
	$action = $_POST['boto'];
}
$delete = isset($_POST['delete'])?$_POST['delete']:false;
$delete_image = isset($_POST['delete_image'])?$_POST['delete_image']:false;

$active_tab_1 = true;
$launch_as_image = false;

$launch_as_new_image = isset($_POST['launch_as_new_image'])?strlen($_POST['launch_as_new_image'])>0:false;
$launch_as_image_source_image = isset($_POST['launch_as_image_source_image'])?strlen($_POST['launch_as_image_source_image'])>0:false;
$launch_as_image = (isset($_POST['launch_as_image']) && ($launch_as_new_image || $launch_as_image_source_image))?$_POST['launch_as_image']:false;
$create_instanceId = isset($_POST['create_instanceId'])?$_POST['create_instanceId']:false;
$my_amis = array();

session_start();
if (!$_SESSION[IS_INSTRUCTOR]==1) {
	die("no estas autoritzat");
}
$container = DOMAIN_PREFIX.$_SESSION[CONTEXT_ID]; //343407
$s = $_SESSION[SESSION_ID_FIELD];
$instanceId = $_SESSION[INSTANCE_ID];
$course_id = $_SESSION[COURSE_ID];

$course_name = '';
$course_title = Language::get('Ec2CourseInterface');
$obj_course = $gestorBD->get_course_by_id($course_id);
if ($obj_course!=false) {
	$course_name = $obj_course['courseKey'];
	$course_title = $obj_course['title'];
}
//var_dump($_POST);

$ec2 = new AmazonEC2(array('key' => AWS_KEY, 'secret' => AWS_SECRET_KEY));
if ($_SESSION[CUSTOM_AWS_REGION] && strlen($_SESSION[CUSTOM_AWS_REGION]))
$ec2->set_region($_SESSION[CUSTOM_AWS_REGION]);
$msg_ok = false; 
$msg_error = false; 
if (is_array($instances) && ($action==SALVA_DADES) ) {
	foreach ($instances as $value) {
		$user_instance = $_REQUEST[NAME_OPTION.$value];
		if ((is_array($user_instance)) && count($user_instance)>0) { //added support for multi user
			foreach ($user_instance as $user_intance_item) {
				if ($user_intance_item!='0') {
					$gestorBD->assignaUsuari($value, $user_intance_item, $course_id, $_SESSION[USERNAME]);
				}
			}
		}
	}
}
elseif ($action==STARTSELECT || $action==STOPSELECT || $task==CHANGESTATE) {
	if (is_array($id)) {
		foreach ($id as $value) {
			if ($action==STOPSELECT){
				$ec2->stop_instances($value);
			} else {
				$ec2->start_instances($value);
			}
		}
		$msg_ok = Language::get($action==STOPSELECT?'Instancies parades correctament':'Instancies iniciades correctament');
		
	}
	elseif ($id && $task==CHANGESTATE) {
		if ($extra==STATERUNNING){
			$ec2->stop_instances($id);
		} else {
			$ec2->start_instances($id);
		}
		$msg_ok = Language::getTag(($extra==STATERUNNING?'Instancia parada correctament':'Instancia iniciada correctament'), $id);
	} 

}
elseif ($action==ASSIGN_USERS) {
	$instanceId = isset($_POST['instanceId'])?$_POST['instanceId']:false;

	if ($instanceId) {
		$user_instance = $_REQUEST[NAME_OPTION.$instanceId];
		$gestorBD->desassignaUsuaris($instanceId, $course_id);
		if ((is_array($user_instance)) && count($user_instance)>0) { //added support for multi user
			foreach ($user_instance as $user_intance_item) {
				if ($user_intance_item!='0') {
					$gestorBD->assignaUsuari($instanceId, $user_intance_item, $course_id, $_SESSION[USERNAME]);
				}
			}
			$msg_ok = Language::getTag('Usuaris des-assignats correctament', $instanceId);
		}
	}
}	
elseif ($action==DELETE_INSTANCE) {

	$student_selected = $gestorBD->getUserPerInstancia($delete, $course_id);
	if (!$student_selected) {
		//Mirem d'eliminar-la cridant tant al ec2 com a la bd
		$response = $ec2->terminate_instances($delete);
		if ($response->isOK()) {
			//Eliminem de l a BD
			if ($gestorBD->eliminaInstancia($delete)) {
				 $msg_ok = Language::getTag('Maquina eliminada correctament', $delete);
			}
			else {
				$msg_error = Language::getTag('Error eliminant maquina de la bd', $delete); 
			}
		} else {
			$msg_error = Language::getTag('Error eliminant maquina amazon', $delete).printEc2Error($response);
		}
	} else {
		$msg_error = Language::get('No es pot eliminar');
	}
}
elseif ($action==DELETE_IMAGE) {
	$active_tab_1 = false;
	//Mirem d'eliminar-la cridant tant al ec2 com a la bd
	$response = $ec2->deregister_image($delete_image);
	if ($response->isOK()) {
		//Eliminem de l a BD
		if ($gestorBD->eliminaImatge($delete_image)) {
			$msg_ok = Language::getTag('Imatge eliminada correctament', $delete_image);
		}
		else {
			$msg_error = Language::getTag('Error eliminant imatge de la bd', $delete_image); 
		}
	} else {
		$msg_error = Language::getTag('Error eliminant imatge amazon', $delete_image).printEc2Error($response);
	}
}
elseif ($action==CREATE_IMAGE_FROM_INSTANCE) {
	$name = str_replace(':','',$course_name).'_'.$create_instanceId.'_'.time();
	$response=$ec2->create_image($create_instanceId, $name);
	if ($response->isOK() && isset($response->body))
	{
		$imageId = $response->body->imageId;
		if ($gestorBD->afegeixAmi($imageId, '', $name, 
                                            $name, 0, '', '',
                                            '', $_SESSION[CUSTOM_AWS_REGION])) {
			if ($gestorBD->afegeixAmiCourse($imageId, $course_id)) {
				$msg_ok = Language::getTag('CreatedImageSuccessfully',$imageId); 
			}
			else {
				$msg_error = Language::getTag('AssociateAMIError',$imageId); 
			}

	
		}
		else {
			$msg_error = Language::getTag('AssociateAMIError',$imageId); 
		}
	}
	else {
		$msg_error = Language::getTag('ErrorCreatingImage',$create_instanceId); 
	}
}
elseif (isset($_POST[FIELD_AMI_BY_ID]) && ($action==ACTION_AMI_MNGT) && !$launch_as_image) {
	$active_tab_1 = false;
	if (strlen($_POST[FIELD_AMI_BY_ID])>0) {
		$options = array();
		$options['ImageId'] = $_POST[FIELD_AMI_BY_ID];
		$response = $ec2->describe_images($options);
		if (isset($response->body->imagesSet ) && isset($response->body->imagesSet->item )) { 
			$item = $response->body->imagesSet->item;
			$imageId = (string) $item->imageId;
				
			if ($imageId==$_POST[FIELD_AMI_BY_ID]) {
					// Stringify the value
				$name = (string) $item->name;
				$description = (string) $item->description;
				$imageState = (string) $item->imageState;
				$imageOwnerId = (string) $item->imageOwnerId;
				$isPublic = ((string) $item->isPublic)=='true';
				$architecture = (string) $item->architecture;
				$imageType = (string) $item->imageType;
				$kernelId = (string) $item->kernelId;
				
				if ($gestorBD->afegeixAmi($imageId, $imageState, $name, 
		                                                $description, $isPublic, $architecture, $imageType,
		                                                $kernelId, $_SESSION[CUSTOM_AWS_REGION])) {
						if ($gestorBD->afegeixAmiCourse($imageId, $course_id)) {
							$msg_ok = Language::getTag('AssociateAMIOK',$imageId); 
						} else {
							$msg_error = Language::getTag('AssociateAMIError',$imageId); 
						}

				} else {
					$msg_error = Language::getTag('AssociateAMIError',$imageId); 
				}
			} else{
				$msg_error = Language::getTag('ErrorAssociatingAMIDoesBotExistImage',$imageId); 
			}
		} else {
			$msg_error = Language::getTag('ErrorAssociatingAMIDoesBotExistImage',$imageId); 
		}
	} else {
		//search button
	}
}
elseif ($action==LAUNCH_INSTANCES) {
		$active_tab_1 = $action!=LAUNCH_INSTANCES;
		$imageId = isset($_POST['imageId'])?$_POST['imageId']:false;
		$instance_type = isset($_POST['instance_type'])?$_POST['instance_type']:false;
		$number_of_instances = isset($_POST['number_of_instances'])?intval($_POST['number_of_instances']):-1;

		if ($imageId && $instance_type) {
			if ($number_of_instances>0) {
				
				$response = $ec2->run_instances($imageId, $number_of_instances, $number_of_instances,
				array(
						'KeyName' => $course_name,
						'InstanceType' => $instance_type,
				));
			// Temporarily cache the InstanceID
			if ($response->isOK() && isset($response->body->instancesSet->item->instanceId))
			{
				$result = new stdClass();
				$result->instance_id = false;
				$result->public_ip = false;
				$result->associated = false;
			
				$result->instances = $response->body->instancesSet;
				if ($result->instances->item) {
					foreach ($result->instances->item as $item) {
					$current_instanceid = $item->instanceId;
					//2. Assoociate tag
					$response = $ec2->create_tags($current_instanceid, array(
							array('Key' => 'Name', 'Value' => Language::get('Maquina').' '.$current_instanceid),
					));
					
						if ($response->isOK()) {
							$msg_ok = Language::getTag('Maquina creada correctament', $current_instanceid); 
						}
						 else {
						 	$msg_error =  Language::get('Error creant maquines resposta sense instancia'). printEc2Error($response);
						}
					}
				
				} else {
					$msg_error = Language::get('Error obtenint resposta de crear maquines'). printEc2Error($response);
				}
			} else {
				$msg_error =  Language::get('Error creant maquines'). printEc2Error($response);
			}
		} else {
			$msg_error = Language::get('selecciona alguna instancia');
		}
	}
} 
?>
<html>
<head>
<meta charset="utf-8">
<title><?php echo Language::get('Ec2CourseInterface').' '.$course_title?></title>
<link rel="shortcut icon" href="favicon.ico">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link rel="shortcut icon" href="images/favicon.ico">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/bootstrap-editable.css" rel="stylesheet">
<link href="css/index.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
<script src="js/bootstrap-editable.min.js"></script>
<script type="text/javascript">
function canviaEstat(estat_actual, id) {
	if (confirm("<?php echo Language::get('Segur que vol canviar lestat a la instancia')?> "+id+"?")) {
		document.f.id.value = id;
		document.f.extra.value = estat_actual;
		document.f.task.value = '<?php echo CHANGESTATE;?>';
		document.f.submit();
	}
}
function aplicarCheckedTot(checked){ 
	   for (i=0;i<document.f.elements.length;i++) { 
	      if(document.f.elements[i].type == "checkbox")	
	         document.f.elements[i].checked=checked
	   } 
	} 
function deleteInstance(form) {
	var id = form.delete.value;	
	if (confirm("<?php echo Language::get('Segur que vol eliminar la instancia')?> "+id+"?")) {
		form.action.value="<?php echo DELETE_INSTANCE;?>";
		form.submit();
	}
}
function deleteImage(form) {
	var id = form.delete_image.value;	
	if (confirm("<?php echo Language::get('Segur que vol eliminar la imatge')?> "+id+"?")) {
		form.action.value="<?php echo DELETE_IMAGE;?>";
		form.submit();
	}
}
function createImageFromInstance(form) {
	var id = form.imageId.value;	
	//if (confirm("<?php echo Language::get('Segur que vol eliminar la instancia')?> "+id+"?")) {
		form.action.value="<?php echo CREATE_IMAGE_FROM_INSTANCE;?>";
		form.submit();
	//}
}
function createInstanceFromImage(form) {
	var id = form.imageId.value;	
	//if (confirm("<?php echo Language::get('Segur que vol eliminar la instancia')?> "+id+"?")) {
		form.action.value="<?php echo LAUNCH_INSTANCES;?>";
		form.submit();
	//}
}

function assignStudents(form, assign) {
	var id = form.instanceId.value;	
	var result = [];
	var select = document.getElementById("<?php echo UN_NAME_OPTION;?>"+id);
	var select_dest = document.getElementById("<?php echo NAME_OPTION;?>"+id);
	if (!assign) {
		select_dest = document.getElementById("<?php echo UN_NAME_OPTION;?>"+id);
		select = document.getElementById("<?php echo NAME_OPTION;?>"+id);
	}
	for (i=select.options.length-1; i>=0; i--) {
		if (select.options[i].selected) {
			select_dest.options[select_dest.length] = new Option(select.options[i].text, select.options[i].value);
			select_dest.options[select_dest.length-1].selected = true;
			select.options[i] = null;
		}
	}
	
	if (assign) {
		select = select_dest;	
	}
	for (i=select.options.length-1; i>=0; i--) {
			select.options[i].selected = true;
		}
	form.submit();
}

</script>
</head>
 <body>
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

	</div><!-- /navbar -->
		
		<div class="container">
		    		<div class="row">
		    			<?php if ($msg_ok) {
		    				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_ok.'</div>';
		    			}if ($msg_error) {
		    				echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_error.'</div>';
		    			} ?>
							<div class="tabbable span12">
								<ul class="nav nav-tabs">
									<li <?php echo $active_tab_1 ?'class="active"':''?>><a href="#tabs1-pane1" data-toggle="tab"><?php echo Language::get('Ec2CourseInterfaceInstances')?></a></li>
									<li <?php echo !$active_tab_1 ?'class="active"':''?>><a href="#tabs1-pane2" data-toggle="tab"><?php echo Language::get('Ec2CourseInterfaceAmis')?></a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane <?php echo $active_tab_1 ?'active':''?>" id="tabs1-pane1">
										<form action="" class="formulari" method="post" name="f">
											<button type="submit" name="boto" value="<?php echo Language::get('reload')?>" class="boto">
											    <i class="icon-refresh"></i> <?php echo Language::get('reload')?>
											</button>
											<button type="submit" name="boto" value="<?php echo STOPSELECT?>"  class="boto">
											    <i class="icon-stop"></i> <?php echo Language::get('stop_selected')?>
											</button>
											<button type="submit" name="boto" value="<?php echo STARTSELECT?>" class="boto">
											    <i class="icon-play"></i> <?php echo Language::get('start_selected')?>
											</button>
									<!--input type="submit" class="boto" name="boto" value="<?php echo Language::get('busca')?>">
									<input type="submit" class="boto" name="action" value="<?php echo STOPSELECT?>">
									<input type="submit" class="boto" name="action" value="<?php echo STARTSELECT?>">
									<input type="submit" class="boto" name="action" value="<?php echo SALVA_DADES?>"-->
		
									    <!-- Example row of columns -->
									<input type="hidden" name="task" value="" />
									<input type="hidden" name="id" value="" />
									<input type="hidden" name="extra" value="" />	
									<br /><br />

										<?php include_once('myinstances.php');?>
					
									</form>					
									</div>
									<div class="tab-pane <?php echo !$active_tab_1 ?'active':''?>" id="tabs1-pane2">
										 <form action="" class="formulari" method="post" name="f_image">
											<button type="submit" name="boto" value="<?php echo Language::get('reload')?>" class="boto">
											    <i class="icon-refresh"></i> <?php echo Language::get('reload')?>
											</button>
											<button href="#modalassociateinstance"  class="boto" data-toggle="modal">
											    <i class="icon-plus"></i> <?php echo Language::get('add_ami_by_id')?>
											</button>
											<div id="modalassociateinstance" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											  <div class="modal-header">
											    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											    <h3 id="myModalLabelmodal"><?php echo Language::get('add_ami_by_id')?></h3>
											  </div>
											  <div class="modal-body">
												<p><?php echo Language::get('Ami Id Explination')?></p>
												<p><span class="label"><?php echo Language::get('Ami Id')?></span> <input type="text" name="<?php echo FIELD_AMI_BY_ID?>" id="<?php echo FIELD_AMI_BY_ID?>"></p>
												
											  </div>
											  <div class="modal-footer">
											    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
											    <button class="btn btn-primary"><?php echo Language::get('Save changes')?></button>
											  </div>
											</div>
											<input type="hidden" name="action" value="<?php echo ACTION_AMI_MNGT ?>" />
											<input type="hidden" name="id" value="" />
											<input type="hidden" name="extra" value="" />	
										</form>	
											<?php include_once('myamis.php');?>
										
									</div>
								</div><!-- /.tab-content -->
							</div><!-- /.tabbable -->
						</div><!-- /.row -->

      <hr>

      <footer>
         <?php include('logos_footer.php');?>
      </footer>


    </div> 
  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="http://code.jquery.com/jquery.min.js"></script>
  <script src="js/bootstrap/bootstrap.min.js"></script>
    
<script>
	$('#myTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});
</script>
</body>
</html>
<?php 
$gestorBD->desconectar();
?>
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
	require_once 'constants.php';
	require_once './sdk.class.php';
	require_once 'utils.php';
	require_once('gestorBD.php');
	require_once('lang.php');

	$gestorBD = new GestorBD();
$course_instances = array();
		
$operation 	= isset($_POST['operation'])?$_POST['operation']:MYINSTANCES;
$id			= isset($_POST['id'])?$_POST['id']:'';
$instances	= isset($_POST['instances'])?$_POST['instances']:'';
$task	 	= isset($_POST['task'])?$_POST['task']:'';
$extra	 	= isset($_POST['extra'])?$_POST['extra']:'';
$action     = isset($_POST['action'])?$_POST['action']:'';
$busca     	= isset($_POST['boto'])?$_POST['boto']!='':'';
$custom_instructions     	= isset($_POST[CUSTOM_INSTRUCTIONS])?$_POST[CUSTOM_INSTRUCTIONS]:false;
$custom_aws_username     	= isset($_POST[CUSTOM_AWS_USERNAME])?$_POST[CUSTOM_AWS_USERNAME]:DEFAULT_USERNAME_AWS;

if (isset($_POST['boto']) && ($_POST['boto'] == STARTSELECT || $_POST['boto'] == STOPSELECT || $_POST['boto'] == RELOAD || $_POST['boto'] == RELOAD_USERS || $_POST['boto'] == AUTO_ASSIGN_USERS)) {
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

if (!isset($_SESSION[IS_INSTRUCTOR]) || !$_SESSION[IS_INSTRUCTOR]==1) {
	show_error(Language::get('no estas autoritzat'));
}
$container = DOMAIN_PREFIX.$_SESSION[CONTEXT_ID]; //343407
$s = $_SESSION[SESSION_ID_FIELD];
$instanceId = $_SESSION[INSTANCE_ID];
$course_id = $_SESSION[COURSE_ID];

if ($custom_instructions && strlen($custom_instructions)>0){
	$gestorBD->setInstructionsAndUsernameStudent($course_id, $custom_instructions, $custom_aws_username);	
}


$course_name = '';
$course_title = Language::get('Ec2CourseInterface');
$obj_course = $gestorBD->get_course_by_id($course_id);
$has_key_stored = 0;
if ($obj_course!=false) {
	$course_name = $obj_course['courseKey'];
	$course_title = $obj_course['title'];
	$has_key_stored = $obj_course['has_key_stored']==1;
	$custom_instructions = $obj_course['instructions'];
	$custom_aws_username = $obj_course['aws_username_student'];
}
if (!$custom_aws_username || strlen($custom_aws_username)==0) {
	$custom_aws_username = DEFAULT_USERNAME_AWS;
}
if (!$custom_instructions || strlen($custom_instructions)==0) {
	$custom_instructions = Language::get('instruccions1').'</br>'.
	Language::get('instruccions2').'</br>';
	$extra_ssh = '';
	$username_ssh = '%USERNAME%';
	if ($has_key_stored) {
			$custom_instructions .= Language::getTag('instruccions3_ec2-user', '<a href="get_file.php" target="_blank">'.Language::get('aqui').'</a>');
			$extra_ssh = ' -i path/to/'.sanitizeFilename($course_name).'.pem';
		} else {
		  $custom_instructions .= Language::get('instruccions3');
		}
		$custom_instructions .='</br>
		<br><pre>ssh '.$username_ssh.'@%IP%'. $extra_ssh.'</pre><br>';
		$custom_instructions .= Language::get('instruccions4').' (http://www.putty.org/)';
}

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
				if ($elasticIP = $gestorBD->getAssociatedIp($value)) {
					sleep(3);
					$response = $ec2->associate_address($value, $elasticIP);
					if (!$response->isOK()) {
						$msg_error = Language::getTag('Error associating elastic ip', $value).printEc2Error($response); 
					}
			
				}
			}
		}
		$msg_ok = Language::get($action==STOPSELECT?'Instancies parades correctament':'Instancies iniciades correctament');
		
	}
	elseif ($id && $task==CHANGESTATE) {
		if ($extra==STATERUNNING){
			$ec2->stop_instances($id);
		} else {
			$ec2->start_instances($id);
			if ($elasticIP = $gestorBD->getAssociatedIp($id)) {
					sleep(3);
					$response = $ec2->associate_address($id, $elasticIP);
					if (!$response->isOK()) {
						sleep(3); //wait 3 seconds more
						$response = $ec2->associate_address($id, $elasticIP);
						if (!$response->isOK()) {
							$msg_error = Language::getTag('Error associating elastic ip', $id).printEc2Error($response); 
						}
					}	
				}
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
elseif ($action==ASSIGN_ELASTIC_IP) {
	$assign_elastic_ip_instance = isset($_POST['assignIpInstance'])?$_POST['assignIpInstance']:false;

	//Get the new elastic IP
	$response = $ec2->allocate_address();
		
	if ($response->isOK()&& isset($response->body))
	{
		$elasticIP = $response->body->publicIp;
		//Associate to instance
		$response = $ec2->associate_address($assign_elastic_ip_instance, $elasticIP);
		if ($response->isOK()) {
			if ($gestorBD->associateIp($assign_elastic_ip_instance, $elasticIP)) {
				 $msg_ok = Language::getTagDouble('Associated sucessfully to instance', $assign_elastic_ip_instance);
			}
			else {
				$response = $ec2->release_address(array('PublicIp'=>$elasticIP));
				$msg_error = Language::getTagDouble('Error associating instance to ip in db', $assign_elastic_ip_instance, $elasticIP); 
			}	
		}
		else {
			$response = $ec2->release_address(array('PublicIp'=>$elasticIP));
			$msg_error = Language::getTag('Error associating elastic ip', $assign_elastic_ip_instance).printEc2Error($response); 
		}
	} else {
		$msg_error = Language::getTag('Error allocating elastic ip', $assign_elastic_ip_instance).printEc2Error($response);
	}
}
elseif ($action==RELEASE_ELASTIC_IP) {
	$assign_elastic_ip_instance = isset($_POST['assignIpInstance'])?$_POST['assignIpInstance']:false;
   	if ($elasticIP = $gestorBD->getAssociatedIp($assign_elastic_ip_instance)) {
		//Get the new elastic IP
		$response = $ec2->release_address(array('PublicIp'=>$elasticIP));
			
		if ($response->isOK())
		{
			if ($gestorBD->releaseIP($assign_elastic_ip_instance)) {
				 $msg_ok = Language::getTagDouble('Released sucessfully to instance', $assign_elastic_ip_instance);
			}
			else {
				$msg_error = Language::getTagDouble('Error releasing ip in db', $assign_elastic_ip_instance, $elasticIP); 
			}	
		
		} else {
			$msg_error = Language::getTag('Error releasing elastic ip', $assign_elastic_ip_instance).printEc2Error($response);
		}
	} else {
		$msg_error = Language::getTag('Error releasing elastic ip not found', $assign_elastic_ip_instance).printEc2Error($response);
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
	$name =isset($_POST['new_image_name'])?$_POST['new_image_name']:$course_name.'_'.$create_instanceId.'_'.time();
	$name = str_replace(':','',$name);
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
		$msg_error = Language::getTag('ErrorCreatingImageFromInstance',$create_instanceId).printEc2Error($response);
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
				$response = $ec2->describe_key_pairs(
									array(
										'KeyName' => $course_name
									));
				$is_ok = false;
				if (!$response->isOK()) {

					if ($response->body->Errors && $response->body->Errors[0]->Error->Code=='InvalidKeyPair.NotFound') {
						//try to create it
						if (defined('PEM_PROTECTED_FOLDER') && is_writable(PEM_PROTECTED_FOLDER)) {
							$response = $ec2->create_key_pair($course_name);
							$file_name = PEM_PROTECTED_FOLDER.DIRECTORY_SEPARATOR.sanitizeFilename($course_name).'.pem';
					
							if ($response->body && $response->body->keyMaterial) {
								(string) $private_key = $response->body->keyMaterial;
							    if (!$handle = fopen($file_name, 'a')) {
							    	$response = Language::get('Error file can not open');
							    }

							    // Write content
							    if (fwrite($handle, $private_key) === FALSE) {
							        $response = Language::get('Error file can not write');
							    } else {
							    	$is_ok = true;
							    }
							    
							    fclose($handle);
							    if ($gestorBD->setHasKeyStored($course_id, 1)) {
							    	$obj_course['has_key_stored'] = 1;
							    }

							}
						} else {
							$response = Language::get('Error folder no exist or no writable');
						}
					} 
				
				} else {
					$is_ok = true;
				}
				if ($is_ok) {
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
									if ((int)$obj_course['has_key_stored']==1) {
										$msg_ok .= '<br>'.Language::getTag('Pots descarregar key pair', '<a href="get_file.php" target="_blank">'.Language::get('aqui').'</a>'); 
									}
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
						$msg_error =  Language::get('Error creant maquines'). printEc2Error($response);
					} 
		} else {
			$msg_error = Language::get('selecciona alguna instancia');
		}
	}
} 
$title = Language::get('Ec2CourseInterface').' '.$course_title;
include('header.php');
?>
<SCRIPT TYPE="text/javascript">
function canviaEstat(estat_actual, id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol canviar lestat a la instancia')?> "+id+"?", function(result) {
        if (result) {
			document.f.id.value = id;
			document.f.extra.value = estat_actual;
			document.f.task.value = '<?php echo CHANGESTATE;?>';
			document.f.submit();
		}
	});
}
function aplicarCheckedTot(checked){ 
	   for (i=0;i<document.f.elements.length;i++) { 
	      if(document.f.elements[i].type == "checkbox")	
	         document.f.elements[i].checked=checked
	   } 
	} 
function deleteInstance(form) {
	var id = form.delete.value;	
	bootbox.confirm("<?php echo Language::get('Segur que vol eliminar la instancia')?> "+id+"?", function(result) {
        if (result) {
			form.action.value="<?php echo DELETE_INSTANCE;?>";
			form.submit();
		}
	});
}
function assignIPJS(id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol assingar Elastic IP per')?> "+id+"?", function(result) {
        if (result) {
			var form =  document.getElementById("form"+id);
			form.action.value = "<?php echo ASSIGN_ELASTIC_IP;?>";
			form.submit();
		}
	});
}
function releaseIPJS(id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol alliberar Elastic IP per')?> "+id+"?", function(result) {
        if (result) {
			var form =  document.getElementById("form"+id);
			form.action.value = "<?php echo RELEASE_ELASTIC_IP;?>";
			form.submit();
		}
	});
}

function showInfo(name_info) {
	var msg = $('#'+name_info).html();	
	bootbox.alert(msg);
}
function deleteImage(form) {
	var id = form.delete_image.value;	
	bootbox.confirm("Are you sure you want to delete the image "+id+"?", function(result) {
        if (result) {	
			form.action.value="delete_image";
			form.submit();
		}
	});
}
function createImageFromInstance(form) {
	var id = form.imageId.value;	
	bootbox.prompt("<?php echo Language::get('Image name');?>", function(result) {
		if (result === null) {
			bootbox.alert("<?php echo Language::get('Indicate a name') ?>");
		} else {
			form.new_image_name.value = result;
			form.action.value="<?php echo CREATE_IMAGE_FROM_INSTANCE;?>";
			form.submit();
		}
	}); 
}
function createInstanceFromImage(form) {
	var id = form.imageId.value;	
	form.action.value="<?php echo LAUNCH_INSTANCES;?>";
	form.submit();
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
<?php include('end_header_navbar.php');?>		
		<div class="container">
		    		<div class="row">
		    			<?php if ($msg_ok) {
		    				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_ok.'</div>';
		    			}if ($msg_error) {
		    				echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_error.'</div>';
		    			} 
		    			if ((int)$obj_course['has_key_stored']==1) {
							echo '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'.Language::getTag('Pots descarregar key pair', '<a href="get_file.php" target="_blank">'.Language::get('aqui').'</a>').'</div>'; 
						}?>
							<div class="tabbable span12">
								<ul class="nav nav-tabs">
									<li <?php echo $active_tab_1 ?'class="active"':''?>><a href="#tabs1-pane1" data-toggle="tab"><?php echo Language::get('Ec2CourseInterfaceInstances')?></a></li>
									<li <?php echo !$active_tab_1 ?'class="active"':''?>><a href="#tabs1-pane2" data-toggle="tab"><?php echo Language::get('Ec2CourseInterfaceAmis')?></a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane <?php echo $active_tab_1 ?'active':''?>" id="tabs1-pane1">
										<form action="" class="formulari" method="post" name="f">
											<button type="submit" name="boto" value="<?php echo RELOAD?>" class="boto">
											    <i class="icon-refresh"></i> <?php echo Language::get('reload')?>
											</button>
											<?php 
											//Only shows if there is an integration with OKI
												$required_class = 'org/osid/shared/SharedException.php';
												if (filexists_aws ($required_class)) { ?>
											<button type="submit" name="boto" value="<?php echo RELOAD_USERS?>" class="boto">
											    <i class="icon-refresh"></i> <?php echo Language::get('reload_users')?>
											</button>
											<?php } ?>
											<button type="submit" name="boto" value="<?php echo AUTO_ASSIGN_USERS?>" class="boto">
											    <i class="icon-hand-right"></i> <?php echo Language::get('auto_assign_users')?>
											</button>											
											<button type="submit" name="boto" value="<?php echo STOPSELECT?>"  class="boto">
											    <i class="icon-stop"></i> <?php echo Language::get('stop_selected')?>
											</button>
											<button type="submit" name="boto" value="<?php echo STARTSELECT?>" class="boto">
											    <i class="icon-play"></i> <?php echo Language::get('start_selected')?>
											</button>
											<button href="#modalinstructions"  class="boto" data-toggle="modal">
											    <i class="icon-help"></i> <?php echo Language::get('change_instructions')?>
											</button>
											<div id="modalinstructions" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											  <div class="modal-header">
											    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											    <h3 id="myModalLabelmodal"><?php echo Language::get('change_instructions')?></h3>
											  </div>
											  <div class="modal-body">
												<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo Language::get('ReplacementsInstructions');?></div> 
												<p><textarea name="<?php echo CUSTOM_INSTRUCTIONS?>" class="textarea" placeholder="" style="width: 450px; height: 200px"><?php echo $custom_instructions; ?></textarea></p>
												<p><?php echo Language::get('Username2ConnectInstance')?><input type="text" name="<?php echo CUSTOM_AWS_USERNAME?>" value="<?php echo $custom_aws_username?>" />
											  </div>
											  <div class="modal-footer">
											    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
											    <button class="btn btn-primary"><?php echo Language::get('Save changes')?></button>
											  </div>
											</div>

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
											<button type="submit" name="boto" value="<?php echo RELOAD?>" class="boto">
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
												<p><?php echo Language::getTag('Public Amis', '<a href="http://aws.amazon.com/amazon-linux-ami" target="_blank">http://aws.amazon.com/amazon-linux-ami</a>');?></p>
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
										<br />
											<?php include_once('myamis.php');?>
										
									</div>
								</div><!-- /.tab-content -->
							</div><!-- /.tabbable -->
						</div><!-- /.row -->


<?php
$show_tabs=true;
include('footer.php');
?>
<script TYPE="text/javascript">

$(function(){
  
   //defaults
   $.fn.editable.defaults.url = 'change_name.php'; 

<?php 
foreach ($course_instances as $instance_id){ 
	echo '$("#instance_name_'.$instance_id.'").editable();
		 $("#instance_pencil_'.$instance_id.'").click(function(e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $("#instance_name_'.$instance_id.'").editable("toggle");
   		}); ';
}
/*foreach ($current_amis as $item)
{
	// Stringify the value
	$imageId =  $item['imageId'];
	echo '$("#ami_name_'.$imageId.'").editable();
		 $("#ami_pencil_'.$imageId.'").click(function(e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $("#ami_name_'.$imageId.'").editable("toggle");
   		}); ';
}*/
 ?>
});
</script>
<?php
$gestorBD->desconectar();
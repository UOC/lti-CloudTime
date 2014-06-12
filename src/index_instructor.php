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

include('includes/instructor_action.php');
include('includes/header.php');
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
function reAssignIPJS(id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol reassingar Elastic IP per')?> "+id+"?", function(result) {
        if (result) {
			var form =  document.getElementById("form"+id);
			form.action.value = "<?php echo REASSIGN_ELASTIC_IP;?>";
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
function notStopInstanceJS(id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol permetre que no apagui')?> "+id+"?", function(result) {
        if (result) {
			var form =  document.getElementById("form"+id);
			form.action.value = "<?php echo NOT_STOP_INSTANCE;?>";
			form.submit();
		}
	});
}
function allowAutoStopInstanceJS(id) {
	bootbox.confirm("<?php echo Language::get('Segur que vol permetre que apagui')?> "+id+"?", function(result) {
        if (result) {
			var form =  document.getElementById("form"+id);
			form.action.value = "<?php echo AUTO_STOP_INSTANCE;?>";
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
<?php include('includes/end_header_navbar.php');?>		
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

										<?php include_once('includes/myinstances.php');?>
					
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
											<?php include_once('includes/myamis.php');?>
										
									</div>
								</div><!-- /.tab-content -->
							</div><!-- /.tabbable -->
						</div><!-- /.row -->


<?php
$show_tabs=true;
include('includes/footer.php');
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
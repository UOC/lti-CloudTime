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
require_once dirname(__FILE__).'/../utils.php';

$required_class = 'org/osid/shared/SharedException.php';
$exists= filexists_aws ($required_class);
$students = $gestorBD->getUsersCourse($course_id, true, true);
$force_reload = isset($_POST['boto'])?$_POST['boto'] == RELOAD_USERS:false;
if (($force_reload || !$students || count($students)==0) && $exists && !empty($s)) {

	require_once $required_class;
	require_once "org/campusproject/components/AuthenticationComponent.php";
	require_once "org/campusproject/components/IdComponent.php";
	require_once "org/campusproject/components/AgentComponent.php";
	require_once "org/campusproject/utils/OsidContextWrapper.php";
	
	try {
		$osidContext = new OsidContextWrapper();
		$osidContext->assignContext('s', $s);
		$osidContext->assignContext('authorization_key', AUTHORIZATION_KEY);
		$osidContext->assignContext('container', $container);
		$osidContext->assignContext('instanceId', $instanceId);
		
		
		$authN =new AuthenticationComponent($osidContext);
		if (!$authN->isUserAuthenticated()) {
			echo Language::get('InvalidSession');
			exit(); 
		}
		$agent = new AgentComponent($osidContext);
		$students = array();
		$group = $agent->getInstanceContainerGroup();
		for($roles = $group->getMembers(false); $roles->hasNextAgent();) {
			$sa = $roles->nextAgent();
			if (AgentComponent::isStudent($sa)) {
				for($members = $sa->getMembers(false); $members->hasNextAgent();) {
					$member = $members->nextAgent();
				
					$current_fullname = '';
					$current_surname = '';
					$current_email = '';
					$current_image = '';
					$properties = $member->getPropertiesByType($agent->getUserPropertiesType());
					$keys_iterator = $properties->getKeys();
					while ($keys_iterator->hasNextObject()) {
						$key = $keys_iterator->nextObject();
						$value = $properties->getProperty($key);
						//$value = mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
						if ($key==FULLNAMEOKI) {
							$current_fullname = $value;
						} elseif ($key==FIRSTNAMEOKI) {
							$current_name = $value;
						} elseif ($key==SURNAMEOKI) {
							$current_surname = $value;
						} elseif ($key==EMAILOKI) {
							$current_email = $value;
						} elseif ($key==IMAGEOKI) {
							$current_image = $value;
						}
					}
					$gestorBD->afegeixEstudiant($course_id, $member->getDisplayName(), $current_fullname, $current_email, $current_image);
				}
			}
		}
		$students = $gestorBD->getUsersCourse($course_id, true, true);
	} catch (Exception $e) {
		error_log("Getting user list ".$e->getMessage()."\n ".serialize($e));
	}
} /*else {
	$students = $gestorBD->getUsersCourse($course_id, true, true);
}*/


//$gestorBD->enableDebug();
$response = $ec2->describe_instances();

	if (isset($response->body->reservationSet->item->instancesSet->item)) { ?>
	
	<table>
		<thead> 
			<tr class="odd">
			    <th scope="col"></th>
				<th scope="col"><input type="checkbox" onclick="Javascript:aplicarCheckedTot(this.checked)" /></th>
				<th scope="col"><?php echo Language::get('instanceId')?></th>
				<th scope="col"><?php echo Language::get('Nom')?></th>
				<th scope="col"><?php echo Language::get('imageId')?></th>
				<th scope="col"><?php echo Language::get('instanceState')?></th>
				<th scope="col"><?php echo Language::get('ipAddress')?></th>
				<!--th scope="col"><?php echo Language::get('ip amazon')?></th>
				< th scope="col"><?php echo Language::get('privateDnsName')?></th>-->
				<th scope="col"><?php echo Language::get('date launched')?></th>
				<th scope="col"><?php echo Language::get('launchTime')?></th>
				<th scope="col"><?php echo Language::get('instanceType')?></th>
				<th scope="col"><?php echo Language::get('Usuari assignat')?></th>
				<th scope="col"><?php echo Language::get('Info')?></th>
				<!-- th scope="col"><?php echo Language::get('kernelId')?></th>
				<th scope="col"><?php echo Language::get('Arquitectura')?></th> 
				<th scope="col"><?php echo Language::get('Crear maquines com')?></th>
				<th scope="col"><?php echo Language::get('Eliminar')?></th>-->
				<th scope="col"><?php echo Language::get('Actions')?></th>
			</tr>
		</thead>
	
		<?php 
		$i=0;
		$unassigned_students_data = $gestorBD->get_users_unassigned_users_by_course($course_id, true);
		$unassigned_users = false;
		if ($action == AUTO_ASSIGN_USERS) {
			$unassigned_users = $gestorBD->get_users_unassigned_users_by_course($course_id, false);
		}
		// Loop through the response...
		foreach ($response->body->reservationSet->item as $obj_item)
		{ 
			$item_temp = $obj_item->instancesSet->item ;
			foreach ($item_temp as $item) {
					
				if ($item->keyName == $course_name && $item->instanceState->name!=STATETERMINATED) { //Nomes mostrem les del grup de multimedia
					$current_ip = $item->ipAddress.'';
					$instanceId = $item->instanceId.'';
					$course_instances[] = $instanceId;	
					//abertranb 20130102 - Added the launch like this
					$imageId	= $item->imageId;
					// ******* END
					/*if ($item->instanceState->name==STATERUNNING && $gestorBD->existeixInstancia($instanceId)) {
						$ip_anterior = $gestorBD->getAssociatedIp($instanceId);
						if ($ip_anterior!=$current_ip) {
							//He de cridar a la API per canviarla
							$current_ip = $ip_anterior;
							$result = $ec2->associate_address($instanceId, $current_ip);
						}
					}*/
					$gestorBD->afegeixInstancia($instanceId, $item->imageId, $item->tagSet->item[0]->value, $item->keyName, $item->instanceState->name,
					$item->ipAddress, $item->dnsName, $item->privateDnsName, $item->launchTime, $item->instanceType, $item->kernel,
					$item->architecture, $item->monitoring->state, getDeviceMapping($item->blockDeviceMapping->item), $_SESSION[CUSTOM_AWS_REGION]);
					$students_selected = $gestorBD->getUserPerInstancia($instanceId, $course_id, true);
					if ($action == AUTO_ASSIGN_USERS && 
						$unassigned_users && 
						count($unassigned_users)>0 &&
						(!$students_selected || count($students_selected)==0)) {
							$current_user_to_assign = array_shift($unassigned_users);
							$gestorBD->assignaUsuari($instanceId, '', $course_id, $_SESSION[USERNAME], $current_user_to_assign['id_user']);
							$students_selected = $gestorBD->getUserPerInstancia($instanceId, $course_id, true);
					}
					if (!in_array_value($item->imageId.'', $my_amis)) {
						$my_amis[] = $item->imageId.'';
					}
				?>
				<tr<?php echo $i%2==1?' class="odd"':''?>>
					<td><?php echo $i+1?> </td>
					<td><input type="checkbox" name="id[]" value="<?php echo $instanceId ?>" /></td>
					<td><?php echo $instanceId?></td>
					<td><span id="<?php echo 'instance_name_'.$instanceId?>" data-type="text" data-toggle="manual" data-pk="<?php echo $instanceId?>" data-placeholder="Required" data-original-title="<?php echo Language::get('Change name');?>" ><?php echo $item->tagSet->item[0]->value?></span><a href="#" id="<?php echo 'instance_pencil_'.$instanceId?>"><i class="icon-pencil"></i></a></td>
					<td><?php echo $item->imageId?></td>
					<td><a href="#" class="<?php if ($item->instanceState->name==STATERUNNING){?>green<?php }else{?>red<?php }?>" title="<?php if ($item->instanceState->name==STATERUNNING){ echo Language::get('stop'); }else{ echo Language::get('start'); }?> <?php echo $instanceId?>" onclick="Javascript:canviaEstat('<?php echo $item->instanceState->name?>', '<?php echo $instanceId ?>')"><?php echo $item->instanceState->name;?></a> </td>
					<td><?php echo $current_ip?></td>
					<!--td><?php echo $item->dnsName?></td>
					< td><?php echo $item->privateDnsName?></td-->
					<td><?php echo tornaData($item->launchTime)?></td>
					<td><?php echo quantTempsEncesa($item->launchTime)?> <?php echo Language::get('hores')?></td>
					<td><?php echo $item->instanceType?></td>
					<td><?php 
						$user_pos = 0;
						$user_str = '';
						if ($students_selected && is_array($students_selected) && count($students_selected)>0) {
							foreach ($students_selected as $student_key => $student) {
								if ($user_pos>0) {
									$user_str .= ', ';
								}
								$user_str.= $student['fullname'];
								$user_pos++;
							}
						}
						echo $user_str;
						?>
					</td>
					<!-- td><?php echo $item->kernelId?></td>
					<td><?php echo $item->architecture?></td> -->
					<td><a class="boto" href="getIP.php?<?php echo INSTANCEC2ID.'='.$instanceId ?>" ><i class="icon-cloud"></i> <?php echo Language::get('AccessInfo')?></a></td>
					<!--td><form action="getIP.php"><button class="boto" type="submit" ><i class="icon-cloud"></i> <?php echo Language::get('AccessInfo')?></button><input type="hidden" name="<?php echo INSTANCEC2ID; ?> value="<?php echo $instanceId; ?>" /></form></td-->
					<td><button href="#modal<?php echo $instanceId ?>" role="button" class="boto" data-toggle="modal"><i class="icon-wrench"></i> <?php echo Language::get('Actions')?></button>
<div id="modal<?php echo $instanceId ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabelmodal"><?php echo Language::get('instanceId')?> <?php echo $instanceId ?></h3>
  </div>
  <div class="modal-body">
  	<div class="row-fluid">
	  	<div class="span6">
			<p><span class="label"><?php echo Language::get('Nom')?>:</span> <?php echo $item->tagSet->item[0]->value?></p>
			<p><span class="label"><?php echo Language::get('imageId')?>:</span> <?php echo $item->imageId?></p>
			<p><span class="label"><?php echo Language::get('instanceState')?>:</span> <a href="#" class="<?php if ($item->instanceState->name==STATERUNNING){?>green<?php }else{?>red<?php }?>" title="<?php if ($item->instanceState->name==STATERUNNING){ echo Language::get('stop'); }else{ echo Language::get('start'); }?> <?php echo $instanceId?>" onclick="Javascript:canviaEstat('<?php echo $item->instanceState->name?>', '<?php echo $instanceId ?>')"><?php echo $item->instanceState->name;?></a></p>
			<p><span class="label"><?php echo Language::get('ip amazon')?>:</span> <?php echo $item->dnsName?></p>
			<p><span class="label"><?php echo Language::get('privateDnsName')?>:</span> <?php echo $item->privateDnsName?></p>
		</div>	
		<div class="span6">
			<p><span class="label"><?php echo Language::get('date launched')?>:</span> <?php echo tornaData($item->launchTime)?></p>
			<p><span class="label"><?php echo Language::get('launchTime')?>:</span> <?php echo quantTempsEncesa($item->launchTime)?> <?php echo Language::get('hores')?></p>
			<p><span class="label"><?php echo Language::get('instanceType')?>:</span> <?php echo $item->instanceType?></p>
			<p><span class="label"><?php echo Language::get('kernelId')?>:</span> <?php echo $item->kernelId?></p>
			<p><span class="label"><?php echo Language::get('Arquitectura')?>:</span> <?php echo $item->architecture?></p>
		</div>
	</div>	
	<div class="row-fluid">
		<p><span class="label"><?php echo Language::get('ipAddress')?>:</span> <?php echo $current_ip?> 
		<?php 
			$associatedIP = $gestorBD->getAssociatedIp($instanceId);
			if (!$associatedIP) {?>
			<input type="button" class="small btn btn-primary"  name="assignIP" onclick="Javascript:assignIPJS('<?php echo $instanceId ?>')" value="<?php echo Language::get('assignIP')?>" />&nbsp;<a href="Javascript:showInfo('<?php echo 'assignIP'.$instanceId?>');"><i class="icon-info"></i></a>
			<div id="<?php echo 'assignIP'.$instanceId?>" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::getTagDouble('InfoElasticIP', '<a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html" target="_blank">', '</a>')?><br><i><?php echo Language::get('assignIPShouldBeRunning');?></i></div>
			<?php } else { ?>
			<input type="button" class="small btn btn-primary"  name="releaseIP" onclick="Javascript:releaseIPJS('<?php echo $instanceId ?>')" value="<?php echo Language::get('releaseIP')?>" />&nbsp;<a href="Javascript:showInfo('<?php echo 'releaseIP'.$instanceId?>');"><i class="icon-info"></i></a>
			<div id="<?php echo 'releaseIP'.$instanceId?>" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::getTagDouble('InfoElasticReleaseIP', '<a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html" target="_blank">', '</a>')?></div>				
			<?php 
			if ($current_ip!=$associatedIP && ($item->instanceState->name==STATEPENDING || $item->instanceState->name==STATERUNNING)) {?>
			<input type="button" class="small btn btn-warning"  name="reAssignIP" onclick="Javascript:reAssignIPJS('<?php echo $instanceId ?>')" value="<?php echo Language::get('reAssignIP')?>" />&nbsp;<a href="Javascript:showInfo('<?php echo 'reAssignIP'.$instanceId?>');"><i class="icon-info"></i></a>
			<div id="<?php echo 'reAssignIP'.$instanceId?>" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::getTagDouble('InfoElasticIP', '<a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html" target="_blank">', '</a>')?><br><i><?php echo Language::get('reAssignIPShouldBeRunning');?></i></div>
			<span><?php echo Language::getTagDouble('reAssignIPExplicacio', $current_ip, $associatedIP)?></span>
			<?php
			}
		} ?>
		</p>
	</div>
	<div class="row-fluid">
		<p><span class="label"><?php echo Language::get('InstanceNotStop')?>:</span> 
		<?php 
			$not_stop_auto = $gestorBD->getInstanceNotStopAutomatically($instanceId);
			if ($not_stop_auto) {?>
			<input type="button" class="small btn btn-danger"  name="auto_stop" onclick="Javascript:allowAutoStopInstanceJS('<?php echo $instanceId ?>')" value="<?php echo Language::get('allow_auto_stop')?>" />
			<?php } else { ?>
			<input type="button" class="small btn btn-danger"  name="not_stop" onclick="Javascript:notStopInstanceJS('<?php echo $instanceId ?>')" value="<?php echo Language::get('not_auto_stop')?>" />			
		<?php } ?>
		</p>
	</div>
	<div class="row-fluid">
		<form method="POST">
			<table>
				<tr>
				 <td>
			  		<p><span class="label"><?php echo Language::get('Usuari assignat')?>:</p>
					<p><select name="<?php echo UN_NAME_OPTION.$instanceId; ?>[]" id="<?php echo UN_NAME_OPTION.$instanceId; ?>" size="3" multiple="multiple" class="boto">
						<?php if (isset($unassigned_students_data) && is_array($unassigned_students_data) ) {
								foreach ($unassigned_students_data as $student) {
									if (!isset($students_selected[$student['userKey']])){?>
									<option value="<?php echo $student['userKey'];?>"><?php echo $student['fullname']?></option>
									<?php }
								}
						}?>
						</select>
					</p>
				 </td>
				 <td align="center" valign="middle">
						<button role="button" class="boto" onclick="javascript:assignStudents(this.form,true);" alt="<?php echo Language::get('assignar')?>"><i class="icon-arrow-right"></i></button>
						<br>
						<button role="button" class="boto" onclick="javascript:assignStudents(this.form,false);" alt="<?php echo Language::get('desassignar')?>"><i class="icon-arrow-left"></i></button>
				 </td>	
				 <td>
					<p><span class="label"><?php echo Language::get('Usuari assignat')?>:</p>
					<p><select name="<?php echo NAME_OPTION.$instanceId; ?>[]" id="<?php echo NAME_OPTION.$instanceId; ?>" size="3" multiple="multiple" class="boto">
						<?php if (isset($students_selected) && is_array($students_selected) ) {
								foreach ($students_selected as $student_key => $student) {?>
									<option value="<?php echo $student_key;?>"><?php echo $student['fullname']?></option>
						<?php 	}
							}?>
						</select>
					</p>
				 </td>
				</tr> 	
			</table>	
			<input type="hidden" name="instanceId" value="<?php echo $instanceId?>" />
			<input type="hidden" name="action" value="<?php echo ASSIGN_USERS;?>" />
		</form>
	</div>
  </div>
  <div class="modal-footer">
    <form method="POST" onsubmit="return false;"  id="form<?php echo $instanceId ?>" name="form<?php echo $instanceId ?>"><button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
    <button class="btn btn-primary"  name="deleteaction" onclick="Javascript:deleteInstance(this.form)"><?php echo Language::get('Elimina maquina')?></button>
    <button class="btn btn-primary"  name="launch_as_image_source_image" onclick="Javascript:createImageFromInstance(this.form)"><?php echo Language::get('Create image')?></button>
    <input type="hidden" name="delete" value="<?php echo $instanceId?>" />
	<input type="hidden" name="create_instanceId" value="<?php echo $instanceId; ?>" />
	<input type="hidden" name="assignIpInstance" value="<?php echo $instanceId?>" />
	<input type="hidden" name="imageId" value="<?php echo $imageId?>" />
	<input type="hidden" name="action" value="" id="formAction<?php echo $instanceId ?>" />
	<input type="hidden" name="accio" value="" />
	<input type="hidden" name="new_image_name" id="new_image_name" value="" />
	</form>
  </div>
</div>
					</td>
				</tr>
				<?php
				$i++;
				} 	
			}
		}
		
		?>

	
	</table>
	<?php 
	}
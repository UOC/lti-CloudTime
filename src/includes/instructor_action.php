<?php

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
$ec2->disable_ssl_verification();
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
						$msg_error .= '<input type="button" class="small btn btn-warning"  name="reAssignIP" onclick="Javascript:reAssignIPJS(\''. $value .'\')" value="'.Language::get('reAssignIP').'" />&nbsp;<a href="Javascript:showInfo(\'reAssignIP_general'.$value.'\');"><i class="icon-info"></i></a>
			<div id="reAssignIP_general'.$value.'" class="hide"><i class="icon-info"></i>&nbsp;'.Language::getTagDouble('InfoElasticIP', '<a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html" target="_blank">', '</a>').'<br><i>'.Language::get('reAssignIPShouldBeRunning').'</i></div>
			<span>'.Language::getTagDouble('reAssignIPExplicacio', '', $elasticIP).'</span>';
			
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
							$msg_error .= '<input type="button" class="small btn btn-warning"  name="reAssignIP" onclick="Javascript:reAssignIPJS(\''. $id .'\')" value="'.Language::get('reAssignIP').'" />&nbsp;<a href="Javascript:showInfo(\'reAssignIP_general'.$id.'\');"><i class="icon-info"></i></a>
			<div id="reAssignIP_general'.$id.'" class="hide"><i class="icon-info"></i>&nbsp;'.Language::getTagDouble('InfoElasticIP', '<a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html" target="_blank">', '</a>').'<br><i>'.Language::get('reAssignIPShouldBeRunning').'</i></div>
			<span>'.Language::getTagDouble('reAssignIPExplicacio', '', $elasticIP).'</span>';

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
				 $msg_ok = Language::getTagDouble('IP Associated sucessfully to instance', $elasticIP, $assign_elastic_ip_instance);
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
elseif ($action==REASSIGN_ELASTIC_IP) {
	$assign_elastic_ip_instance = isset($_POST['assignIpInstance'])?$_POST['assignIpInstance']:false;

	$elasticIP = $gestorBD->getAssociatedIp($assign_elastic_ip_instance);
	//Associate to instance
	$response = $ec2->associate_address($assign_elastic_ip_instance, $elasticIP);
	if ($response->isOK()) {
		if ($gestorBD->associateIp($assign_elastic_ip_instance, $elasticIP)) {
			 $msg_ok = Language::getTagDouble('IP Associated sucessfully to instance', $elasticIP, $assign_elastic_ip_instance);
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
elseif ($action==NOT_STOP_INSTANCE || $action==AUTO_STOP_INSTANCE) {
	$assign_elastic_ip_instance = isset($_POST['assignIpInstance'])?$_POST['assignIpInstance']:false;
	$allow_not_stop = $action==NOT_STOP_INSTANCE;
	//Get the new elastic IP
	if ($gestorBD->allowInstanceAutoStop($assign_elastic_ip_instance, $allow_not_stop)) {
		 $msg_ok = Language::getTag($allow_not_stop?'Behauviour of instance is not stop automatically':'Behauviour of instance is stop automatically');
	}
	else {
		$msg_error = Language::getTag('Error associating instance to ip in db', $assign_elastic_ip_instance); 
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
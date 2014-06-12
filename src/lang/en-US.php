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
$language['no estas autoritzat'] = 'Not authorized';
$language['no tens instancies assignades'] = 'you don\'t have assigned machines';
$language['Ec2 instance'] = 'Ec2 instance';
$language['Segur que vol canviar lestat a la instancia'] = 'Are you sure you want to change the state';
$language['back'] = 'back';
$language['noinstancesassignated'] = 'Can\'t get any assigned instance. Contact with teacher';
$language['myinstancesmanagement'] = 'My Machines';
$language['instruccions_caps'] = 'Below are instructions for turning on and stopping';
$language['instruccions1'] = 'The first thing you need is start the machine.';
$language['instruccions2'] = 'Once you started the machine, you could find the IP (if not already available refresh this page).';
$language['instruccions3'] = 'Then you can log in via SSH username indicating the <b>user</b> and the password you have been given the teacher. To connect from linux/mac open a terminal and type:';
$language['instruccions3_ec2-user'] = 'Then you can log in via SSH username indicating the <b>ec2-user</b> and key pair that can download from %s.<br>You have to give 400 permission to file. To connect from linux/mac open a terminal and type:';
$language['instruccions4'] = 'If you have windows then you have to use a tool such Putty';
$language['dadesactualsinstancia'] = 'The identifier of the machine is <b>%s </b> current state is:';
$language['start'] = 'Start Machine';
$language['stop'] = 'Stop Machine';
$language['amb_ip'] = 'ip';
$language['no_ip'] = 'Not yet assigned IP refresh the page by clicking on';
$language['primer_encen_instance'] = 'First turn on the machine';
$language['refresh'] = 'refresh';
$language['apaga_maquina'] = 'When finished using the machine has to stop it by clicking on';
$language['Ec2CourseInterface'] = 'Ec2 Course Interface';
$language['busca'] = 'search';
$language['instanceId'] = 'Instance Id';
$language['Usuari assignat'] = 'Assigned User/s';
$language['Info'] = 'Access Information';
$language['AccessInfo'] = 'Access';
$language['Nom'] = 'Name';
$language['imageId'] = 'Image Id';
$language['instanceState'] = 'instanceState';
$language['ipAddress'] = 'Ip Address';
$language['ip amazon'] = 'Ip Amazon';
$language['privateDnsName'] = 'Private Dns Name';
$language['date launched'] = 'Date Launched';
$language['launchTime'] = 'Launch Time';
$language['instanceType'] = 'Instance Type';
$language['kernelId'] = 'kernel Id';
$language['Arquitectura'] = 'Architecture';
$language['Selecciona quantes maquines'] = 'Select number of machines to launch';
$language['Crear maquines com'] = 'Launch machines';
$language['selecciona alguna instancia'] = 'You have to select the number of instances to launch';
$language['Error creant maquines'] = 'Error 101 creating machines';
$language['Error obtenint resposta de crear maquines'] = 'Error 102 getting response of machine';
$language['Error creant maquines resposta sense instancia']	= 'Error 103 getting response of machine creation';
$language['Maquina'] = 'Machine';
$language['Maquina creada correctament'] = 'Machine %s successfully created';
$language['No es pot eliminar'] = 'Impossible delete because is assigned to student';
$language['Elimina maquina'] = 'Delete machine';
$language['Eliminar'] = 'Delete';
$language['Segur que vol eliminar la instancia'] = 'Are you sure you want to delete the machine';
$language['Maquina eliminada correctament'] = 'Machine %s deleted successfully';
$language['Error eliminant maquina de la bd'] = 'Error 201 deleting machine from DB %s';
$language['Error eliminant maquina amazon'] = 'Error 202 deleting machine %s';
$language['Create'] = 'Create';
$language['Create image'] = 'Create image';
$language['Delete image'] = 'Delete image';
$language['InvalidSession'] = 'Invalid session, restart application';
$language['Ec2CourseInterfaceAmis'] = 'Images';
$language['Ec2CourseInterfaceInstances'] = 'Instances';
$language['is_public_ami_true'] = 'True';
$language['is_public_ami_false'] = 'False';
$language['imageState'] = 'Status';
$language['imageType'] = 'Image Type';
$language['Elimina image'] = 'Delete image';
$language['Detalls'] = 'Details';
$language['segons'] = 'seconds';
$language['Close'] = 'Close';
$language['search'] = 'Search';
$language['reload'] = 'Reload';
$language['save'] = 'Save';
$language['stop_selected'] = 'Stop Selected';
$language['start_selected'] = 'Start Selected';
$language['Actions'] = 'Actions';
$language['Save changes'] = 'Save changes';
$language['Ami Id'] = 'Ami Id';
$language['add_ami_by_id'] = 'add image by id';
$language['Ami Id Explination'] = 'You must specify the ID of the AMI, like "ami-XXXXXXX." You will find in the AWS console';
$language['AssociateAMIOK'] = 'Ami %s associated successfully to course';
$language['AssociateAMIError'] = 'Error associating Ami %s to course';
$language['ErrorAssociatingAMIDoesBotExistImage'] = 'Ami %s does not exist';
$language['ErrorCreatingImageFromInstance'] = 'Error creating image from instance %s';
$language['CreatedImageSuccessfully'] = 'Image %s created successfully from instance.';
$language['Launch from image'] = 'Launch Instances';
$language['Segur que vol eliminar la imatge'] = 'Are you sure you want to delete the image';
$language['Imatge eliminada correctament'] = 'Image %s deleted successfully';
$language['Error eliminant imatge de la bd'] = 'Error 301 deleting image from DB %s';
$language['Error eliminant imatge amazon'] = 'Error 302 deleting image %s';
$language['lti:errornotauthorized'] = 'Error is not authorized to this course, you have to be an Admin, Teacher or Student';
$language['lti:resource_id_required'] = 'Error missing parameters, resource_id is required';
$language['register_user_error'] = 'Error registering user into system';
$language['updated_user_error'] = 'Error updating user into system';
$language['lti:errorjoincourse'] = 'Error joining user into course';
$language['lti:errorregistercourse'] = 'Error registering course into system';
$language['lti:errorupdatingcourse'] = 'Error updating course into system';
$language['desassignar'] ='Unassign';
$language['assignar'] ='Assign';
$language['Developed By UOC'] = 'Developed by Universitat Oberta de Catalunya';
$language['Usuaris des-assignats correctament'] = 'Un/assigned users/s successfully to instance %s';
$language['hores'] ='Hours';
$language['Instancies parades correctament'] = 'Instances stopped successfully';
$language['Instancies iniciades correctament'] = 'Instances started successfully';
$language['Instancia parada correctament'] = 'Instance %s stopped successfully';
$language['Instancia iniciada correctament'] = 'Instance %s started successfully';
$language['Error folder no exist or no writable'] = 'Error PEM folder doesn\'t exist or is not writable';
$language['Error file can not write'] = 'Error PEM file can not write';
$language['Error file can not open'] = 'Error PEM file can not open';
$language['Public Amis'] = 'Search public ESB-Backed 32 or 64 in the web page %s, you have to select by current region';
$language['Pots descarregar key pair'] = 'You can download the key pair from %s.';
$language['aqui'] = 'here';
$language['Missing parameters'] = 'Missing parameters';
$language['Change name'] = 'Change name';
$language['reload_users'] = 'Reload users';
$language['auto_assign_users'] = 'Autoassign users';
$language['change_instructions'] = 'Change instructions';
$language['Username2ConnectInstance'] = 'Username to Connect Instance';
$language['ReplacementsInstructions'] = 'You can use the variables: %USERNAME% => Username to log in, %IP% => Assigned IP';
$language['InfoElasticIP'] = 'Amazon\'s elastic IP address feature is similar to static IP address in traditional data centers, with one key difference. A user can programmatically map an elastic IP address to any virtual machine instance without a network administrator\'s help and without having to wait for DNS to propagate the new binding. More info %shere%s';
$language['assignIP'] = 'Assign Elastic IP';
$language['assignIPShouldBeRunning'] = 'To assign an elastic IP the instance should be running';
$language['Segur que vol assingar Elastic IP per'] = 'Are you sure want to assign an Elastic IP to the instance';
$language['Error allocating elastic ip'] = 'Error allocating elastic ip to instance %s';
$language['Error associating elastic ip'] = 'Error associating elastic IP to instance %s';
$language['IP Associated sucessfully to instance'] = 'IP %s associated sucessfully to instance %s';
$language['Error associating instance to ip in db'] = 'Error associating instance %s to ip %s in db';
$language['disassociateIP'] = 'Disassociate IP';
$language['releaseIP'] = 'Release IP';
$language['InfoElasticReleaseIP'] = 'If you release the IP you can get a new one but not the same IP. Amazon\'s elastic IP address feature is similar to static IP address in traditional data centers, with one key difference. A user can programmatically map an elastic IP address to any virtual machine instance without a network administrator\'s help and without having to wait for DNS to propagate the new binding. More info %shere%s';
$language['Segur que vol alliberar Elastic IP per'] = 'Are you sure you want to release an Elastic IP to the instance';
$language['Released sucessfully to instance'] = 'IP released sucessfully to instance %s';
$language['reAssignIP'] = 'Reassign Elastic IP';
$language['reAssignIPExplicacio'] = 'Current IP is %s  and should be %s. The problem is produced because the machine is not in running state when tried to assign the elastic ip';
$language['Segur que vol reassingar Elastic IP per'] = 'Are you sure want to reassign Elastic IP to the instance';
$language['administrator'] = 'Administrator';
$language['site'] = 'Return to Site';
$language['aws_canonical_name'] = 'AWS canonical name';
$language['aws_key'] = 'AWS key';
$language['Add'] = 'Add';
$language['Edit'] = 'Edit';
$language['Delete'] = 'Delete';
$language['aws_secret'] = 'AWS secret';
$language['aws_account_id'] = 'AWS account id';
$language['aws_canonical_id'] = 'AWS canonical id';
$language['aws_mfa_serial'] = 'AWS mfa serial';
$language['aws_cloudfront_keypair_id'] = 'AWS CloudFront key pair id';
$language['aws_cloudfront_private_key_pem'] = 'AWS CloudFront private key pem';

$language['aws_key_desc'] = 'Amazon Web Services Key. Found in the AWS Security Credentials. You can also pass this value as the first parameter to a service constructor.';
$language['aws_secret_desc'] = 'Amazon Web Services Secret Key. Found in the AWS Security Credentials. You can also pass this value as the second parameter to a service constructor.';
$language['aws_account_id_desc'] = 'Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials';
$language['aws_canonical_id_desc'] = 'Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials.';
$language['aws_canonical_name_desc'] = 'Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials (i.e. "Welcome, AWS_CANONICAL_NAME").';
$language['aws_mfa_serial_desc'] = '12-digit serial number taken from the Gemalto device used for Multi-Factor Authentication. Ignore this if you\'re not using MFA.';
$language['aws_cloudfront_keypair_id_desc'] = 'Amazon CloudFront key-pair to use for signing private URLs. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_keypair_id()';
$language['aws_cloudfront_private_key_pem_desc'] = 'The contents of the *.pem private key that matches with the CloudFront key-pair ID. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_private_key().';
$language['aws_enable_extensions_desc'] = ' Set the value to true to enable autoloading for classes not prefixed with "Amazon" or "CF". If enabled, load sdk.class.php last to avoid clobbering any other autoloaders.';

$language['ConfigurationAWSSavedOk'] = 'AWS configuration %s successfully salved';
$language['ErrorSavingAWSConfiguration'] = 'Error saving AWS configuration %s';
$language['ConfigurationAWSDeletedOk'] = 'AWS configuration %s successfully deleted';
$language['ErrorDeletingAWSConfiguration'] = 'Error deleting AWS configuration %s';
$language['InstanceNotStop'] = 'Behaivour instances auto stop';
$language['not_auto_stop'] = 'No auto stop';
$language['allow_auto_stop'] = 'Allow auto stop';
$language['Segur que vol permetre que no apagui'] = 'Are you sure do you want to allow not stop the instance';
$language['Segur que vol permetre que apagui'] = 'Are you sure do you want to allow stop the instance';
$language['Behauviour of instance is stop automatically'] = 'Behauviour of instance  %s is stop automatically';
$language['Behauviour of instance is not stop automatically'] = 'Behauviour of instance %s is not stop automatically';
$language['List instances'] = 'List instances';
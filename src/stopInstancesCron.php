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
	// Include the SDK
	require_once './sdk.class.php';
	require_once 'constants.php';
	require_once('lib/phpMailer/class.phpmailer.php');
	require_once('gestorBD.php');
	require_once('lang.php');
	
	$gestorBD = new GestorBD();
	$titulo = 'Intancias paradas';	
	$ec2 = new AmazonEC2(array('key' => AWS_KEY, 'secret' => AWS_SECRET_KEY));
	
//abertranb add field current_aws_configuration
	$last_aws_configuration = array('type' => DEFAULT_AWS_ACCOUNT, 
		'key' => AWS_KEY, 'secret' => AWS_SECRET_KEY);
	$current_aws_configuration = $last_aws_configuration;
	$last_keyName = '';
//END
	$instancies = $gestorBD->retornaTotesLesInstancies();
	
	foreach ($instancies as $instance) {

		$instanceId	= $instance['instanceId'];
		$region	= $instance['amazon_region'];

//abertranb add field current_aws_configuration
		$keyName = $instance['keyName'];
		if ($last_keyName!=$keyName) {
			$current_aws_configuration = $gestorBD->getInstanceAWSConfiguration($keyName);
			if ($current_aws_configuration['type']!=$last_aws_configuration['type']) {
				//new ec2
				$ec2 = new AmazonEC2(array('key' => $current_aws_configuration['key'],
					 'secret' => $current_aws_configuration['secret']));
				$last_aws_configuration = $current_aws_configuration;
			}
			$last_keyName = $keyName;
		}
//END
		if ($region==null || !$region) { //Marquem la per defecte
			$region = AmazonEC2::REGION_US_E1;
		}
		$ec2->set_region($region);
		
		if ($instanceId) {
		
			$array = array('InstanceId'=>$instanceId);
			$response = $ec2->describe_instances($array);
			
			$body = '';
			if (isset($response->body->reservationSet->item->instancesSet->item)) { 
		
				$body = '<html>
			<head><title>Ec2 Interface</title>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<style type="text/css"> 
			<!--
			@import url("'.url().'/css/styles.css");
			-->
			</style> 
			<h2>Se han parado las siguientes m&aacute;quinas</h2>
				<table>
					<thead> 
						<tr class="odd">
							<th scope="col">instanceId</th>
							<th scope="col">ipAddress</th>
							<th scope="col">date launched</th>
							<th scope="col">launch time</th>
						</tr>
					</thead>';
				$i=0;
				// Loop through the response...
				if ($response->body->reservationSet->item)
				{ 
					$obj_item = $response->body->reservationSet->item;
					$item = $obj_item->instancesSet->item ;
					$current_ip = $item->ipAddress.'';
					if ($item->instanceState->name==STATERUNNING) {
						$quant_temps = quantTempsEncesa($item->launchTime);
						if ($quant_temps>HOURS_AUTOSTOP) {
							 
							$body .= '<tr '.($i%2==1?' class="odd"':'').' >
								<td>'.$instanceId.'</td>
								<td>'.$current_ip.'</td>
								<td>'.tornaData($item->launchTime).'</td>
								<td>'.$quant_temps.' horas</td>
							</tr>';
							$ec2->stop_instances($instanceId);
							$i++;
						}
					}
				} //Fi de if
				
			$body .= '</table>';
			$body .= '<br> <p><b>Puede volver a arrancar la máquina desde el aula</b></p>';
			if ($i>0) {
				$mail             = new PHPMailer(); // defaults to using php "mail()"
				$teachers = $gestorBD->getAssignedInstructorForInstance($instanceId);
				$students = $gestorBD->getAssignedUserForInstance($instanceId);
				
				$mail->IsSendmail(); // telling the class to use SendMail transport
				
				//$mail->AddReplyTo("name@yourdomain.com","First Last");
				
				//$mail->SetFrom('name@yourdomain.com', 'First Last');
				
				//$mail->AddReplyTo("name@yourdomain.com","First Last");

				if ( $students ) {
					foreach ($students as $student) {
						$mail->AddAddress($student['email'], $student['fullname']);
					}
				}	
				
				if ( $teachers ) {
					foreach ($teachers as $teacher) {

						$mail->AddBCC($teacher['email'], $teacher['fullname']);
					}
				}
								
				$mail->Subject    = $titulo;
				
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
				
				$mail->MsgHTML($body);
				
				if(!$mail->Send()) {
				  echo "Mailer Error: " . $mail->ErrorInfo;
				}
			}
		}
	}
  }//For ppal	
	
	//print_r($response);
	function url(){
		$protocol = isset($_SERVER['HTTPS']) ? "https" : "http";
		return $protocol . "://" . $_SERVER['HTTP_HOST'] ;
	}
	

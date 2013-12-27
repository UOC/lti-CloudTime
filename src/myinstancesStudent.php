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
$array = null;
$array = array('InstanceId'=>$InstanceId);
$response = $ec2->describe_instances($array);

$i=0;
	if (isset($response->body->reservationSet->item->instancesSet->item)) { 
	
		// Loop through the response...
		$current_ip = '';
		$instanceId = '';
		$keyName 	= '';
		$esta_encesa = false;
		$esta_pending = false;
		$esta_stoping = false;
		foreach ($response->body->reservationSet->item as $obj_item)
		{ 
			$item = $obj_item->instancesSet->item ;
				
				$current_ip = $item->ipAddress.'';
				$instanceId = $item->instanceId.'';
				$keyName 	= $item->keyName.'';
				$esta_encesa = STATERUNNING==$item->instanceState->name;
				$esta_pending = STATEPENDING==$item->instanceState->name;
				$esta_stoping = STATESTOPING==$item->instanceState->name;;
				
			?>
			<?php
				$i++;
		}
		?>
	<?php
	}
	if ($i==0) {
		?><br><br>
		<div class="alert alert-error"><?php echo Language::get('noinstancesassignated')?></div>
	<?php } else {
		
	if (!$custom_instructions || strlen($custom_instructions)==0) {		
		$username_ssh = $custom_aws_username;
		$extra_instructions = '';
		$extra_ssh = '';
		if ($has_key_stored) {
			$extra_instructions .= Language::getTag('instruccions3_ec2-user', '<a href="get_file.php" target="_blank">'.Language::get('aqui').'</a>');
			$extra_ssh .= ' -i path/to/'.sanitizeFilename($course_name).'.pem';
		} else {
		  	$extra_instructions .= Language::get('instruccions3');
		}
	 	$custom_instructions = Language::get('myinstancesmanagement').
			'<h4>'.Language::get('instruccions_caps').':</h4>'.
			Language::get('instruccions1').'</br>'.
			Language::get('instruccions2').'</br>'.
			$extra_instructions.'</br><br>'.
			'<pre>ssh %USERNAME%@%IP%'.
			$extra_ssh.'</pre><br>'.
			Language::get('instruccions4').' (http://www.putty.org/)';
	
	}
	$ip_replace = (isset($current_ip) && strlen($current_ip)>0)? $current_ip:'XXX.XXX.X.XXX';
	$custom_instructions = str_replace('%IP%', $ip_replace, $custom_instructions);
	$custom_instructions = str_replace('%USERNAME%', $custom_aws_username, $custom_instructions);
	if ($is_instructor){
		//to avoid breadcrumb
		echo '<br>';
	}
	echo $custom_instructions;?>
			<div><?php echo sprintf(Language::get('dadesactualsinstancia'),$instanceId) ?>
				<a href="#" class="<?php if ($item->instanceState->name==STATERUNNING){?>green<?php }else{?>red<?php }?>" title="<?php if ($item->instanceState->name==STATERUNNING){ echo Language::get('stop'); }else{ echo Language::get('start'); }?> <?php echo $instanceId?>" onclick="Javascript:canviaEstat('<?php echo $item->instanceState->name?>', '<?php echo $instanceId ?>')"><?php echo $item->instanceState->name;?></a><br> <?php echo (isset($current_ip) && strlen($current_ip)>0)? Language::get('amb_ip').' <b>'.$current_ip.'</b>':($esta_pending ? ' '.Language::get('no_ip').' </b>':Language::get('primer_encen_instance')) ?></div>
				<div class="alert alert-info"><?php echo Language::get('apaga_maquina')?> <a href="#" onclick="Javascript:canviaEstat('<?php echo STATERUNNING?>', '<?php echo $instanceId ?>')"><?php echo Language::get('stop')?></a></div>
<?php 
	if ($is_instructor) {
?>
<a class="boto" href="index_instructor.php"><i class="icon-arrow-left"></i> <?php echo Language::get('back'); ?></a>
				<?php } ?>
		<?php 
	} ?>
				<button class="boto" onclick="Javascript:refresca();"><i class="icon-refresh"></i> <?php echo Language::get('refresh'); ?></button>
				<button onclick="Javascript:canviaEstat('<?php echo $item->instanceState->name?>', '<?php echo $instanceId ?>');" value="start_selected" class="boto">
											    <i class="icon-<?php echo $esta_encesa?'stop':'play'?>"></i> <?php echo Language::get($esta_encesa?'stop':'start')?></button>				
	<div class="clear"></div>
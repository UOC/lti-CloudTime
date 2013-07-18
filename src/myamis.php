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

$options = array('Owner' => 'self');
//$options = array('ExecutableBy' => 'self');
$current_amis = $gestorBD->getAmisByCourseId($course_id);
foreach ($current_amis as $key => $item) {
	if (!in_array_value($item['imageId'], $my_amis)){
		$my_amis[] = $item['imageId'];
	}
}
$options['ImageId'] = $my_amis;
$response = $ec2->describe_images($options);
$course_id = $_SESSION[COURSE_ID];
/*if (count($my_amis)==0 && isset($response->body->imagesSet ) && count($response->body->imagesSet->item)==0) {
	$response = $ec2->describe_images(
		array(
		    "Filters" => array(
		        array("Name" => "name", "Values" => array(""))
		    	)
			)
		);

	echo "<pre>";print_r($response); echo "</pre>";
}*/
if (isset($response->body->imagesSet )) { 
	foreach ($response->body->imagesSet->item as $item)
	{
			// Stringify the value
		$imageId = (string) $item->imageId;
		$name = (string) $item->name;
		$description = (string) $item->description;
		$imageLocation = (string) $item->imageLocation;
		$imageState = (string) $item->imageState;
		$imageOwnerId = (string) isset($item->imageOwnerId)?$item->imageOwnerId:'';
		$isPublic = ((string) $item->isPublic)=='true';
		$architecture = (string) $item->architecture;
		$imageType = (string) $item->imageType;
		$kernelId = (string) $item->kernelId;
		$rootDeviceType = (string) $item->rootDeviceType;
		$rootDeviceName = (string) $item->rootDeviceName;
		$blockDeviceMapping = $item->blockDeviceMapping; //obj
		$virtualizationType = (string) $item->virtualizationType;
		$hypervisor = (string) $item->hypervisor;

		if ($gestorBD->afegeixAmi($imageId, $imageState, $name, 
                                                $description, $isPublic, $architecture, $imageType,
                                                $kernelId, $_SESSION[CUSTOM_AWS_REGION])) {
			$gestorBD->afegeixAmiCourse($imageId, $course_id); 
		}
        
	}
}
$current_amis = $gestorBD->getAmisByCourseId($course_id);

//$response = $ec2->describe_images();
//echo "<pre>";
//print_r($response->body->imagesSet);
//echo "</pre>";

	// Look through the body, grab ALL <imageId> nodes, stringify the values, and filter them with the
	// PCRE regular expression.
	//$my_amis = $response->body->imageId()->map_string('/aki/i'/*'/aki/i'*/);

	// Display
	if (isset($current_amis ) && count($current_amis)>0) { ?>
	
	<table>
		<thead> 
			<tr class="odd">
			    <th scope="col"><?php echo Language::get('imageId')?></th>
				<th scope="col"><?php echo Language::get('Nom')?></th>
				<th scope="col"><?php echo Language::get('imageState')?></th>
				<th scope="col"><?php echo Language::get('isPublic')?></th>
				<th scope="col"><?php echo Language::get('imageType')?></th>
				<th scope="col"><?php echo Language::get('Actions')?></th>
			</tr>
		</thead>
	
		<?php 
		$i=0;

		// Loop through the response...
		foreach ($current_amis as $item)
		{
			// Stringify the value
			$imageId =  $item['imageId'];
		
			$name =  $item['name'];
			$description =  $item['description'];
			//$imageLocation = (string) $item['imageLocation'];
			$imageState = $item['imageState'];
			$imageOwnerId = isset($item['imageOwnerId'])?$item['imageOwnerId']:'';
			$isPublic = $item['isPublic'];
			$architecture = $item['architecture'];
			$imageType = $item['imageType'];
			$kernelId = $item['kernelId'];
			//$rootDeviceType = (string) $item->rootDeviceType;
			//$rootDeviceName = (string) $item->rootDeviceName;
			//$blockDeviceMapping = $item->blockDeviceMapping; //obj
			//$virtualizationType = (string) $item->virtualizationType;
			//$hypervisor = (string) $item->hypervisor;
			?>
			<tr<?php echo $i%2==1?' class="odd"':''?>>
				<td><?php echo $imageId?></td>
				<td><?php echo $name?></td>
				<td><?php echo $imageState?></td>
				<td><?php echo Language::get($isPublic?'is_public_ami_true':'is_public_ami_false')?></td>
				<td><?php echo $imageType?></td>
				<td>
					<button href="#modal_image<?php echo $imageId ?>" role="button" class="boto" data-toggle="modal"><i class="icon-wrench"></i> <?php echo Language::get('Actions')?></button>
					<div id="modal_image<?php echo $imageId ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
					  <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					    <h3 id="myModalLabelmodal"><?php echo Language::get('imageId')?> <?php echo $imageId ?></h3>
					  </div>
					  <div class="modal-body">
					  	<div class="row-fluid">
						  	<div class="span6">
								<p><span class="label"><?php echo Language::get('Nom')?>:</span> <?php echo $name?></p>
								<p><span class="label"><?php echo Language::get('imageId')?>:</span> <?php echo $imageId?></p>
								<p><span class="label"><?php echo Language::get('imageState')?>:</span> <?php echo $imageState;?></a></p>
							</div>	
							<div class="span6">
								<p><span class="label"><?php echo Language::get('isPublic')?>:</span> <?php echo Language::get($isPublic?'is_public_ami_true':'is_public_ami_false')?></p>
								<p><span class="label"><?php echo Language::get('imageType')?>:</span> <?php echo $imageType?></p>
								<p><span class="label"><?php echo Language::get('Architecture')?>:</span> <?php echo $architecture?></p>
							</div>
						</div>	
						<div class="row-fluid">
							<form method="POST">

							<p><span class="label"><?php echo Language::get('Crear maquines com')?></p>
							<p><select name="number_of_instances" class="boto"><option value="-1"><?php echo Language::get('Selecciona quantes maquines')?></option><?php for ($num_instances=1; $num_instances <= 10; $num_instances++){?><option value="<?php echo $num_instances?>"><?php echo $num_instances?></option><?php }?></select>
								<input type="hidden" name="launch_as_image" value="1" />
							</p>
							<p><select name="instance_type" class="boto">
								<option value="t1.micro">Micro</option>
								<option value="m1.small">Small</option>
								<option value="m1.medium">Medium</option>
							</select>
							</p>
						</div>
					  </div>
					  <div class="modal-footer">
					    <form method="POST" onsubmit="return false;"><button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
					    <button class="btn btn-primary"  name="deleteaction" onclick="Javascript:deleteImage(this.form)"><?php echo Language::get('Elimina image')?></button>
					    <button class="btn btn-primary"  name="launch_as_new_image" onclick="Javascript:createInstanceFromImage(this.form)"><?php echo Language::get('Launch from image')?></button>
					    <input type="hidden" name="imageId" value="<?php echo $imageId?>" />
						<input type="hidden" name="action" value="" />
						<input type="hidden" name="delete_image" value="<?php echo $imageId?>" />
						</form>
					  </div>
					</div>
					</td>
			</tr>
			<?php
			$i++;
		}
		?>
	</table>	
		<?php 
	} else { ?>
	<div class="info"><?php echo Language::get('No AMIs found'); ?></div>
	<?php } ?>

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
// Set HTML headers
header("Content-type: text/html; charset=utf-8");

// Include the SDK
require_once 'constants.php';
require_once 'sdk.class.php';
require_once 'utils.php';
require_once('gestorBD.php');
require_once('lang.php');

$gestorBD = new GestorBD();
$course_instances = array();
		
$action 	= isset($_POST['action'])?$_POST['action']:'';
$id			= isset($_POST['id'])?$_POST['id']:'';
$msg_ok = false; 
$msg_error = false; 
	
if ($action == SALVA_DADES) {
	$aws_canonical_name = isset($_POST['aws_canonical_name'])?$_POST['aws_canonical_name']:'';
	$aws_key = isset($_POST['aws_key'])?$_POST['aws_key']:'';
	$aws_secret = isset($_POST['aws_secret'])?$_POST['aws_secret']:'';
	$aws_account_id = isset($_POST['aws_account_id'])?$_POST['aws_account_id']:'';
	$aws_canonical_id = isset($_POST['aws_canonical_id'])?$_POST['aws_canonical_id']:'';
	$aws_mfa_serial = isset($_POST['aws_mfa_serial'])?$_POST['aws_mfa_serial']:'';
	$aws_cloudfront_keypair_id = isset($_POST['aws_cloudfront_keypair_id'])?$_POST['aws_cloudfront_keypair_id']:'';
	$aws_cloudfront_private_key_pem = isset($_POST['aws_cloudfront_private_key_pem'])?$_POST['aws_cloudfront_private_key_pem']:'';
	if ($gestorBD->save_aws_configuration($id, $aws_canonical_name, $aws_key, $aws_secret, $aws_account_id, $aws_canonical_id, $aws_mfa_serial, $aws_cloudfront_keypair_id, $aws_cloudfront_private_key_pem)) {
		$msg_ok = Language::getTag('ConfigurationAWSSavedOk', $aws_canonical_name);
	} else {
		$msg_error = Language::getTag('ErrorSavingAWSConfiguration', $aws_canonical_name);
	}
} elseif ($action == DELETE_AWS_CONFIGURATION) {
	$aws_canonical_name = isset($_POST['aws_canonical_name'])?$_POST['aws_canonical_name']:'';
	if ($gestorBD->delete_aws_configuration($id)) {
		$msg_ok = Language::getTag('ConfigurationAWSDeletedOk', $aws_canonical_name);
	} else {
		$msg_error = Language::getTag('ErrorDeletingAWSConfiguration', $aws_canonical_name);
	}
}
if (!isset($_SESSION[IS_ADMINISTRATOR]) || !$_SESSION[IS_ADMINISTRATOR]==1) {
	show_error(Language::get('no estas autoritzat'));
}

$title = Language::get('administrator');
$course_title = $title;
$course_id = $_SESSION[COURSE_ID];
$obj_course = $gestorBD->get_course_by_id($course_id);

$show_deleted = false;
$aws_configurations = $gestorBD->get_aws_configuration($show_deleted);

include('includes/header.php');
?>
<?php include('includes/end_header_navbar.php');?>		
	<br>
	<br>
	<div class="container">
		    		<div class="row">
		    			<?php if ($msg_ok) {
		    				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_ok.'</div>';
		    			}if ($msg_error) {
		    				echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$msg_error.'</div>';
		    			} 
		    			?>
		<div class="table-responsive span12">
			<table  class="table table-striped table-bordered table-condensed">
				<tr>
					<th  align="center"><?php echo Language::get('aws_canonical_name')?></th>
					<th align="center"><?php echo Language::get('aws_key')?></th>
					<th align="center"><?php echo Language::get('Edit')?></th>
					<th align="center"><?php echo Language::get('Delete')?></th>
				</tr>
			<?php	
			foreach ($aws_configurations as $key => $value) { ?>
						<tr>
							<td><?php echo $value['aws_canonical_name']?></td>
							<td><?php echo $value['aws_key']; ?></td>
							<td>
								<a href="get_aws_configuration_by_id.php?id=<?php echo $value['id']; ?>" data-toggle="modal" class="btn btn-info"><?php echo Language::get('Edit')?></a>
							</td>	
							<td>
								<form method="POST">
									<button class="btn btn-danger"  name="delete"  onclick="Javascript:deleteAWSConfiguration(this.form);return false;"><?php echo Language::get('Delete')?></button>
					    
									<input type="hidden" name="action" value="<?php echo DELETE_AWS_CONFIGURATION?>" />
					    			<input type="hidden" name="id" value="<?php echo $value['id']?>" />
					    			<input type="hidden" name="aws_canonical_name" value="<?php echo $value['aws_canonical_name']?>" />
								</form>
		  					</td>
						</tr>	         	
			         	<?php
						}
						?>
						<tr>
							<td colspan="3" align="center">
								<a href="get_aws_configuration_by_id.php" data-toggle="modal" class="btn btn-info"><?php echo Language::get('Add')?></a>
							</td>
						</tr>

					</table>
							</div><!-- /.table-responsive -->
						</div><!-- /.row -->
<div id="aws_canonical_name" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_canonical_name_desc');?></div>
<div id="aws_key" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_key_desc');?></div>
<div id="aws_secret" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_secret_desc');?></div>
<div id="aws_account_id" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_account_id_desc');?></div>
<div id="aws_canonical_id" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_canonical_id_desc');?></div> 
<div id="aws_cloudfront_keypair_id" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_cloudfront_keypair_id_desc');?></div>
<div id="aws_cloudfront_private_key_pem" class="hide"><i class="icon-info"></i>&nbsp;<?php echo Language::get('aws_cloudfront_private_key_pem_desc');?></div> 
<?php

$show_tabs=false;
include('includes/footer.php');
?>
<script TYPE="text/javascript">

function showInfo(name_info) {
	var msg = $('#'+name_info).html();	
	bootbox.alert(msg);
}
function deleteAWSConfiguration(form) {
	var aws_canonical_name = form.aws_canonical_name.value;	
	bootbox.confirm("Are you sure you want to delete "+aws_canonical_name+"?", function(result) {
        if (result) {	
			form.action.value="<?php echo DELETE_AWS_CONFIGURATION?>";
			form.submit();
		}
	});
}

$(document).ready(function() {
	// Support for AJAX loaded modal window.
	// Focuses on first input textbox after it loads the window.
	$('[data-toggle="modal"]').click(function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		if (url.indexOf('#') == 0) {
			$(url).modal('open');
		} else {
			$.get(url, function(data) {
			$('<div class="modal hide fade">' + data + '</div>').modal();
		}).success(function() { $('input:text:visible:first').focus(); });
	}
	});
});
</script>
<?php
$gestorBD->desconectar();
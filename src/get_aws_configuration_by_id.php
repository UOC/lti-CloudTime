<?php 
require_once 'constants.php';
require_once 'sdk.class.php';
require_once 'utils.php';
require_once('gestorBD.php');
require_once('lang.php');


$id = isset($_REQUEST['id'])?$_REQUEST['id']:-1;
if (!isset($_SESSION[IS_ADMINISTRATOR]) || !$_SESSION[IS_ADMINISTRATOR]==1) {
	show_error(Language::get('no estas autoritzat'));
}

$gestorBD = new GestorBD();

if ($id>0) {
	$value = $gestorBD->get_aws_configuration_by_id($id);
} 

?>
<form method="POST">
  	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">×</button>
	    <h3 id="myModalLabelmodal"><?php echo Language::get('Edit')?></h3>
	</div>
	<div class="modal-body">
		<p><label  for="aws_canonical_name"><?php echo  Language::get('aws_canonical_name')?>:
			<a href="Javascript:showInfo('aws_canonical_name')"><i class="icon-info"></i></a></label>
			 <input type="text" name="aws_canonical_name" value="<?php echo $id>0?$value['aws_canonical_name']:'';?>"></p>
		<p><label  for="aws_key"><?php echo  Language::get('aws_key')?>:
		<a href="Javascript:showInfo('aws_key')"><i class="icon-info"></i></a>
		</label> <input type="text" name="aws_key" value="<?php echo $id>0?$value['aws_key']:'';?>"></p>
		<p><label  for="aws_secret"><?php echo  Language::get('aws_secret')?>:
		<a href="Javascript:showInfo('aws_secret')"><i class="icon-info"></i></a>
			</label> <input type="text" name="aws_secret" value="<?php echo $id>0?$value['aws_secret']:'';?>"></p>
		<p><label  for="aws_account_id"><?php echo  Language::get('aws_account_id')?>:
		<a href="Javascript:showInfo('aws_account_id')"><i class="icon-info"></i></a>
			</label> <input type="text" name="aws_account_id" value="<?php echo $id>0?$value['aws_account_id']:'';?>"></p>
		<p><label  for="aws_canonical_id"><?php echo  Language::get('aws_canonical_id')?>:
		<a href="Javascript:showInfo('aws_canonical_id')"><i class="icon-info"></i></a>
			</label> <input type="text" name="aws_canonical_id" value="<?php echo $id>0?$value['aws_canonical_id']:'';?>"></p>
		<p><label  for="aws_mfa_serial"><?php echo  Language::get('aws_mfa_serial')?>:
		<a href="Javascript:showInfo('aws_mfa_serial')"><i class="icon-info"></i></a>
			</label> <input type="text" name="aws_mfa_serial" value="<?php echo $id>0?$value['aws_mfa_serial']:'';?>"></p>
		<p><label  for="aws_cloudfront_keypair_id"><?php echo  Language::get('aws_cloudfront_keypair_id')?>:
		<a href="Javascript:showInfo('aws_cloudfront_keypair_id')"><i class="icon-info"></i></a>
			</label> <input type="text" name="aws_cloudfront_keypair_id" value="<?php echo $id>0?$value['aws_cloudfront_keypair_id']:'';?>"></p>
		<p><label  for="aws_cloudfront_private_key_pem"><?php echo  Language::get('aws_cloudfront_private_key_pem')?>:
			<a href="Javascript:showInfo('aws_cloudfront_private_key_pem')"><i class="icon-info"></i></a>
			</label> <textarea name="aws_cloudfront_private_key_pem"><?php echo $id>0?$value['aws_cloudfront_private_key_pem']:'';?></textarea></p>

	</div> <!-- /modal-body -->
	<div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
	    <button class="btn btn-primary"  name="save" value="1"><?php echo Language::get('Save')?></button>
	    <input type="hidden" name="id" value="<?php echo $id?>" />
		<input type="hidden" name="action" value="<?php echo SALVA_DADES?>" />
	</div>
</form>
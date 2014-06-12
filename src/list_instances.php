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

$instances = $gestorBD->retornaTotesLesInstancies();

?>
	<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabelmodal"><?php echo Language::get('List intances')?></h3>
</div>
<div class="modal-body">
	<div class="table-responsive span12">
		<table  id="tableImages" class="table table-striped table-bordered table-condensed">
			<tr>
				<th><?php echo Language::get('instanceId')?></th>
				<th><?php echo Language::get('imageId')?></th>
				<th><?php echo Language::get('name')?></th>
				<th><?php echo Language::get('keyName')?></th>
				<th><?php echo Language::get('instanceState')?></th>
				<th><?php echo Language::get('ipAddress')?></th>
				<!--th><?php echo Language::get('ip_amazon')?></th>
				<th><?php echo Language::get('privateDnsName')?></th-->
				<th><?php echo Language::get('launchTime')?></th>
				<th><?php echo Language::get('instanceType')?></th>
				<!--th><?php echo Language::get('kernelId')?></th>
				<th><?php echo Language::get('architecture')?></th>
				<th><?php echo Language::get('monitoring')?></th>
				<th><?php echo Language::get('blockDeviceMapping')?></th>
				<th><?php echo Language::get('created')?></th-->
				<th><?php echo Language::get('amazon_region')?></th>
				<th><?php echo Language::get('has_elastic_ip')?></th>
				<th><?php echo Language::get('elasticIPAddress')?></th>
				<th><?php echo Language::get('not_stop')?></th>
			</tr>
		<?php	
		foreach ($instances as $key => $value) { ?>
			<tr>
				<td><?php echo $value['instanceId']?></td>
				<td><?php echo $value['imageId']; ?></td>
				<td><?php echo $value['name']; ?></td>
				<td><?php echo $value['keyName']; ?></td>
				<td><?php echo $value['instanceState']; ?></td>
				<td><?php echo $value['ipAddress']; ?></td>
				<!--td><?php echo $value['ip_amazon']; ?></td>
				<td><?php echo $value['privateDnsName']; ?></td-->
				<td><?php echo $value['launchTime']; ?></td>
				<td><?php echo $value['instanceType']; ?></td>
				<!--td><?php echo $value['kernelId']; ?></td>
				<td><?php echo $value['architecture']; ?></td>
				<td><?php echo $value['monitoring']; ?></td>
				<td><?php echo $value['blockDeviceMapping']; ?></td>
				<td><?php echo $value['created']; ?></td-->
				<td><?php echo $value['amazon_region']; ?></td>
				<td><?php echo $value['has_elastic_ip']; ?></td>
				<td><?php echo $value['elasticIPAddress']; ?></td>
				<td><?php echo $value['not_stop_boolean']; ?></td>
			</tr>	         	
         <?php 	} ?>
				</table>
	</div><!-- /.table-responsive -->
</div> <!-- /modal-body -->
<div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Language::get('Close')?></button>
    <input type="hidden" name="id" value="<?php echo $id?>" />
</div>
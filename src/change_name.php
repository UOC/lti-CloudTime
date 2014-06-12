<?php 

require_once './sdk.class.php';
require_once 'constants.php';
require_once 'utils.php';
require_once('gestorBD.php');
require_once('lang.php');

if (!$_SESSION[IS_INSTRUCTOR]==1) {
	header('HTTP/1.0 404 '.Language::get('no estas autoritzat'));
} else {

	$instance_id = $_POST['pk']; //the instance id
	$value = $_POST['value'];
	$option_name = $_POST['name'];
	if (strlen($instance_id)>0 && strlen($value)>0) {
		$ec2 = new AmazonEC2(array('key' => AWS_KEY, 'secret' => AWS_SECRET_KEY));
		$ec2->disable_ssl_verification();
		if ($_SESSION[CUSTOM_AWS_REGION] && strlen($_SESSION[CUSTOM_AWS_REGION]))
		$ec2->set_region($_SESSION[CUSTOM_AWS_REGION]);
		if (strpos($option_name, 'instance_name_')!==FALSE){
			$response = $ec2->create_tags($instance_id, array(
								array('Key' => 'Name', 'Value' => $value),
						));	
		} 
//Can not change the name, only can edit the description
		/*elseif (strpos($option_name, 'ami_name_')!==FALSE){
			$response = $ec2->create_tags($instance_id, array(
								array('Key' => 'Name', 'Value' => $value),
						));	
		}*/
		 header('HTTP/1.0 200');
	} else {
		 header('HTTP/1.0 404 '.Language::get('Missing parameters'));
	}
}
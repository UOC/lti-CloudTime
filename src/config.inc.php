<?php if (!class_exists('CFRuntime')) die('No direct access allowed.');
/**
 * File: Configuration
 * 	Stores your AWS account information. Add your account information, and then rename this file to 'config.inc.php'.
 *
 * Version:
 * 	2010.09.30
 *
 * License and Copyright:
 * 	See the included NOTICE.md file for more information.
 *
 * See Also:
 * 	[PHP Developer Center](http://aws.amazon.com/php/)
 * 	[AWS Security Credentials](http://aws.amazon.com/security-credentials)
 */

//	if (session_status() == PHP_SESSION_NONE) { PHP 5.4
if(!isset($_SESSION)) { 
	session_start();
}
require_once('constants.php');
require_once 'utils.php';
require_once('gestorBD.php');
/**
 * 
 * Define la bd
 * @var unknown_type
 */
define('BD_HOST', 'localhost');
define('BD_NAME', 'YOUR DATABASE NAME');
define('BD_USERNAME', 'YOUR DB USER');
define('BD_PASSWORD', 'YOUR DB PASSWORD');


//Default configuration
/**
 * 	Amazon Web Services Key. Found in the AWS Security Credentials. You can also pass this value as the first parameter to a service constructor.
 */
$current_AWS_KEY = 'YOUR PRIVATE AWS KEY';

/**
 * 	Amazon Web Services Secret Key. Found in the AWS Security Credentials. You can also pass this value as the second parameter to a service constructor.
 */
$current_AWS_SECRET_KEY = 'YOUR AWS SECRET KEY';

/**
 * 	Folder to store file pems and distribute to students, the server should be able to write it but it will not have http acces
 */
$current_PEM_PROTECTED_FOLDER = '/var/www/gestioaws_files';

/**
 * 	Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials.
 */
$current_AWS_ACCOUNT_ID = 'YOUR ACCOUNT ID';

/**
 * 	Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials.
 */
$current_AWS_CANONICAL_ID = '';

/**
 * 	Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials (i.e. "Welcome, AWS_CANONICAL_NAME").
 */
$current_AWS_CANONICAL_NAME = '';

/**
 * 	12-digit serial number taken from the Gemalto device used for Multi-Factor Authentication. Ignore this if you're not using MFA.
 */
$current_AWS_MFA_SERIAL =  '';

/**
 * 	Amazon CloudFront key-pair to use for signing private URLs. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_keypair_id().
 */
$current_AWS_CLOUDFRONT_KEYPAIR_ID = '';

/**
 * 	The contents of the *.pem private key that matches with the CloudFront key-pair ID. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_private_key().
 */
$current_AWS_CLOUDFRONT_PRIVATE_KEY_PEM = '';

/**
 * Constant: AWS_ENABLE_EXTENSIONS
 * 	Set the value to true to enable autoloading for classes not prefixed with "Amazon" or "CF". If enabled, load sdk.class.php last to avoid clobbering any other autoloaders.
 */
$current_AWS_ENABLE_EXTENSIONS = 'false';

if (isset($_SESSION[FIELD_OTHER_CONF]) && $_SESSION[FIELD_OTHER_CONF]) {
	$gestorBDTemp = new GestorBD();
	$configurationAWS = $gestorBDTemp->get_aws_configuration_by_aws_canonical_name($_SESSION[FIELD_OTHER_CONF]);
	if ($configurationAWS) {
		$current_AWS_KEY = $configurationAWS['aws_key'];

		$current_AWS_SECRET_KEY = $configurationAWS['aws_secret'];

		$current_AWS_ACCOUNT_ID = $configurationAWS['aws_account_id'];

		$current_AWS_CANONICAL_ID = $configurationAWS['aws_canonical_id'];

		$current_AWS_CANONICAL_NAME = $configurationAWS['aws_canonical_name'];

		$current_AWS_MFA_SERIAL =  $configurationAWS['aws_mfa_serial'];

		$current_AWS_CLOUDFRONT_KEYPAIR_ID = $configurationAWS['aws_cloudfront_keypair_id'];

		$current_AWS_CLOUDFRONT_PRIVATE_KEY_PEM = $configurationAWS['aws_cloudfront_keypair_id'];
	}
	$gestorBDTemp->desconectar();
}
/**
 * Constant: AWS_KEY
 * 	Amazon Web Services Key. Found in the AWS Security Credentials. You can also pass this value as the first parameter to a service constructor.
 */
define('AWS_KEY', $current_AWS_KEY);

/**
 * Constant: AWS_SECRET_KEY
 * 	Amazon Web Services Secret Key. Found in the AWS Security Credentials. You can also pass this value as the second parameter to a service constructor.
 */
define('AWS_SECRET_KEY', $current_AWS_SECRET_KEY);

/**
 * Constant: FILE_FOLDER
 * 	Folder to store file pems and distribute to students, the server should be able to write it but it will not have http acces
 */
define('PEM_PROTECTED_FOLDER', $current_PEM_PROTECTED_FOLDER);

/**
 * Constant: AWS_ACCOUNT_ID
 * 	Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials.
 */
define('AWS_ACCOUNT_ID', $current_AWS_ACCOUNT_ID);

/**
 * Constant: AWS_CANONICAL_ID
 * 	Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials.
 */
define('AWS_CANONICAL_ID', $current_AWS_CANONICAL_ID);

/**
 * Constant: AWS_CANONICAL_NAME
 * 	Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials (i.e. "Welcome, AWS_CANONICAL_NAME").
 */
define('AWS_CANONICAL_NAME', $current_AWS_CANONICAL_NAME);

/**
 * Constant: AWS_MFA_SERIAL
 * 	12-digit serial number taken from the Gemalto device used for Multi-Factor Authentication. Ignore this if you're not using MFA.
 */
define('AWS_MFA_SERIAL', $current_AWS_MFA_SERIAL);

/**
 * Constant: AWS_CLOUDFRONT_KEYPAIR_ID
 * 	Amazon CloudFront key-pair to use for signing private URLs. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_keypair_id().
 */
define('AWS_CLOUDFRONT_KEYPAIR_ID', $current_AWS_CLOUDFRONT_KEYPAIR_ID);

/**
 * Constant: AWS_PRIVATE_KEY_PEM
 * 	The contents of the *.pem private key that matches with the CloudFront key-pair ID. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_private_key().
 */
define('AWS_CLOUDFRONT_PRIVATE_KEY_PEM', $current_AWS_CLOUDFRONT_PRIVATE_KEY_PEM);

/**
 * Constant: AWS_ENABLE_EXTENSIONS
 * 	Set the value to true to enable autoloading for classes not prefixed with "Amazon" or "CF". If enabled, load sdk.class.php last to avoid clobbering any other autoloaders.
 */
define('AWS_ENABLE_EXTENSIONS', $current_AWS_ENABLE_EXTENSIONS);

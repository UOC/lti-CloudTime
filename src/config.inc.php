<?php if (!class_exists('CFRuntime')) die('No direct access allowed.');
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
/**
 * 
 * Define la bd
 * @var unknown_type
 */
define('BD_HOST', '');
define('BD_NAME', '');
define('BD_USERNAME', '');
define('BD_PASSWORD', '');
/**
 * Constant: FILE_FOLDER
 * 	Folder to store file pems and distribute to students, the server should be able to write it but it will not have http acces
 */
define('PEM_PROTECTED_FOLDER', '/Users/antonibertranbellido/uoc/Learning Apps/ec2');

/**
 * Constant: AWS_KEY
 * 	Amazon Web Services Key. Found in the AWS Security Credentials. You can also pass this value as the first parameter to a service constructor.
 */
define('AWS_KEY', '');

/**
 * Constant: AWS_SECRET_KEY
 * 	Amazon Web Services Secret Key. Found in the AWS Security Credentials. You can also pass this value as the second parameter to a service constructor.
 */
define('AWS_SECRET_KEY', '');

/**
 * Constant: AWS_ACCOUNT_ID
 * 	Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials.
 */
define('AWS_ACCOUNT_ID', '');

/**
 * Constant: AWS_CANONICAL_ID
 * 	Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials.
 */
define('AWS_CANONICAL_ID', '');

/**
 * Constant: AWS_CANONICAL_NAME
 * 	Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials (i.e. "Welcome, AWS_CANONICAL_NAME").
 */
define('AWS_CANONICAL_NAME', '');

/**
 * Constant: AWS_MFA_SERIAL
 * 	12-digit serial number taken from the Gemalto device used for Multi-Factor Authentication. Ignore this if you're not using MFA.
 */
define('AWS_MFA_SERIAL', '');

/**
 * Constant: AWS_CLOUDFRONT_KEYPAIR_ID
 * 	Amazon CloudFront key-pair to use for signing private URLs. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_keypair_id().
 */
define('AWS_CLOUDFRONT_KEYPAIR_ID', '');

/**
 * Constant: AWS_PRIVATE_KEY_PEM
 * 	The contents of the *.pem private key that matches with the CloudFront key-pair ID. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_private_key().
 */
define('AWS_CLOUDFRONT_PRIVATE_KEY_PEM', '');

/**
 * Constant: AWS_ENABLE_EXTENSIONS
 * 	Set the value to true to enable autoloading for classes not prefixed with "Amazon" or "CF". If enabled, load sdk.class.php last to avoid clobbering any other autoloaders.
 */
define('AWS_ENABLE_EXTENSIONS', 'false');

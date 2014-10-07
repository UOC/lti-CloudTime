
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courseKey` varchar(70) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `has_key_stored` decimal(1,0) DEFAULT 0,
  `amazon_region` varchar(80) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `aws_username_student` varchar(100) default null,
  `aws_configuration` varchar(40) DEFAULT 'default_aws_account',
  PRIMARY KEY (`id`),
  KEY `courseKey` (`courseKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ec2_ami` (
  `imageId` varchar(50) COLLATE utf8_bin NOT NULL,
  `imageState` varchar(50) COLLATE utf8_bin NOT NULL,
  `name` varchar(120) COLLATE utf8_bin NOT NULL,
  `description` varchar(200) COLLATE utf8_bin NOT NULL,
  `isPublic` tinyint(4) NOT NULL,
  `architecture` varchar(50) COLLATE utf8_bin NOT NULL,
  `imageType` varchar(50) COLLATE utf8_bin NOT NULL,
  `kernelId` varchar(50) COLLATE utf8_bin NOT NULL,
  `amazon_region` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`imageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `ec2_ami_course` (
  `imageId` varchar(50) COLLATE utf8_bin NOT NULL,
  `courseId` int(11) NOT NULL,
  PRIMARY KEY (`imageId`,`courseId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `ec2_instance` (
  `instanceId` varchar(50) NOT NULL DEFAULT '',
  `imageId` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `keyName` varchar(150) DEFAULT NULL,
  `instanceState` varchar(50) DEFAULT NULL,
  `ipAddress` varchar(20) DEFAULT NULL,
  `ip_amazon` varchar(50) DEFAULT NULL,
  `privateDnsName` varchar(50) DEFAULT NULL,
  `launchTime` datetime DEFAULT NULL,
  `instanceType` varchar(50) DEFAULT NULL,
  `kernelId` varchar(50) DEFAULT NULL,
  `architecture` varchar(50) DEFAULT NULL,
  `monitoring` varchar(50) DEFAULT NULL,
  `blockDeviceMapping` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `amazon_region` varchar(80) DEFAULT NULL,
  `has_elastic_ip` bit DEFAULT 0,
  `elasticIPAddress` varchar(20) DEFAULT NULL, 
  `not_stop` bit DEFAULT 0,
  PRIMARY KEY (`instanceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ec2_instance_user_connection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instanceId` varchar(50) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_course` int(11) DEFAULT NULL,
  `session_start` datetime DEFAULT NULL,
  `session_end` datetime DEFAULT NULL,
  `session_force_close` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ec2_instance_user_course` (
  `instanceId` varchar(50) NOT NULL DEFAULT '',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_course` int(11) NOT NULL DEFAULT '0',
  `total_time_connected` decimal(10,2) DEFAULT NULL COMMENT 'Time in hours',
  `last_session` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `userKeyCreated` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`instanceId`,`id_user`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userKey` varchar(70) DEFAULT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `last_session` datetime DEFAULT NULL,
  `blocked` bit(1) DEFAULT b'0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userKey` (`userKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `user_course` (
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_course` int(11) NOT NULL DEFAULT '0',
  `is_instructor` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id_user`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `aws_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aws_key` varchar(70) DEFAULT NULL COMMENT 'mazon Web Services Key. Found in the AWS Security Credentials. You can also pass this value as the first parameter to a service constructor.',
  `aws_secret` varchar(70) DEFAULT NULL COMMENT 'Amazon Web Services Secret Key. Found in the AWS Security Credentials. You can also pass this value as the second parameter to a service constructor.',
  `aws_account_id` varchar(70) DEFAULT NULL COMMENT 'Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials',
  `aws_canonical_id` varchar(70) DEFAULT NULL COMMENT 'Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials.',
  `aws_canonical_name` varchar(70) not NULL COMMENT 'Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Found in the AWS Security Credentials (i.e. "Welcome, AWS_CANONICAL_NAME").',
  `aws_mfa_serial` varchar(70) DEFAULT NULL COMMENT '12-digit serial number taken from the Gemalto device used for Multi-Factor Authentication. Ignore this if you\'re not using MFA.',
  `aws_cloudfront_keypair_id` varchar(70) DEFAULT NULL COMMENT 'Amazon CloudFront key-pair to use for signing private URLs. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_keypair_id()',
  `aws_cloudfront_private_key_pem` mediumtext DEFAULT NULL COMMENT 'The contents of the *.pem private key that matches with the CloudFront key-pair ID. Found in the AWS Security Credentials. This can be set programmatically with AmazonCloudFront::set_private_key().
',
  `aws_enable_extensions` bit DEFAULT 0 COMMENT ' Set the value to true to enable autoloading for classes not prefixed with "Amazon" or "CF". If enabled, load sdk.class.php last to avoid clobbering any other autoloaders.',
  `deleted` bit DEFAULT 0 COMMENT ' Set as deleted',
  PRIMARY KEY (`id`),
  KEY `aws_canonical_name` (`aws_canonical_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

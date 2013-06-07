lti-cloudTime
=============

CloudTime (a word mixed between cloud and runtime) lets you easily set up and distribute Amazon EC2 virtual machines. It is a simple and very customized view of an associated Amazon AWS account. It is specially designed for teachers that want to offer cloud infrastructure to their students (i.e. computer programming, engineering and other subjects that need computing).

The LTI CloudTime integration lets teachers to create, start, shutdown an see the virtual machines and assign them to the students over the course. It also lets students to start and shutdown their assigned machines and know how to access to them.

# Requirements

* PHP 5.X

* MySQL 5.X

* Amazon [AWS Account](http://aws.amazon.com)

# Step-by-Step Guide

* Create a MySQL database

* Execute the SQL file install/CloudTime.sql

## Configure the file src/config.inc.php

* Database configuration
	define('BD_HOST', 'localhost');
	define('BD_NAME', 'your db name');
	define('BD_USERNAME', 'your db username');
	define('BD_PASSWORD', 'your db password');
* Amazon Web Services Key. Found in the AWS Security Credentials. 
	define('AWS_KEY', '');

* 	Amazon Web Services Secret Key. Found in the AWS Security Credentials
	define('AWS_SECRET_KEY', '');

* Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials.
	define('AWS_ACCOUNT_ID', '');

* Configure the LTI credentials found in file src/lib/IMSBasicLTI/configuration/authorizedConsumersKey.cfg
p.e: in order to set as a consumer key "external", you have to put the following in the configuration file:
	consumer_key.external.enabled=1 
	consumer_key.external.secret=pwd_12345

# LTI Launch to index.php of application
	http://localhost/CloudTime/index.php

# Cron
You can enable a cron to stop the instances automatically (current is setted a 8 hours but you can change it)

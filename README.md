lti-CloudTime
=============

CloudTime (a word mixed between cloud and runtime) lets you easily set up and distribute Amazon EC2 virtual machines. It is a simple and very customized view of an associated Amazon AWS account. It is specially designed for teachers that want to offer cloud infrastructure to their students (i.e. computer programming, engineering and other subjects that need computing).

The LTI CloudTime integration lets teachers to create, start, shutdown an see the virtual machines and assign them to the students over the course. It also lets students to start and shutdown their assigned machines and know how to access to them.

# Requirements

* PHP 5.X

* MySQL 5.X

* Amazon [AWS Account](http://aws.amazon.com)

# Step-by-Step Guide

#### Create a MySQL database

#### Execute the SQL file install/CloudTime.sql

#### Configure the file src/config.inc.php

###### Database configuration

	define('BD_HOST', 'localhost');
	define('BD_NAME', 'your db name');
	define('BD_USERNAME', 'your db username');
	define('BD_PASSWORD', 'your db password');

###### Folder to store file pems and distribute to students, the server should be able to write it but it shouldn't have http acces

	define('PEM_PROTECTED_FOLDER', '/path/to/folder/');


###### Amazon Web Services Key. Found in the AWS Security Credentials. 

	define('AWS_KEY', '');

###### Amazon Web Services Secret Key. Found in the AWS Security Credentials

	define('AWS_SECRET_KEY', '');

###### Amazon Account ID without dashes. Used for identification with Amazon EC2. Found in the AWS Security Credentials.

	define('AWS_ACCOUNT_ID', '');

#### Configure the LTI credentials 
File src/lib/IMSBasicLTI/configuration/authorizedConsumersKey.cfg
p.e: in order to set as a consumer key "external", you have to put the following in the configuration file:

	consumer_key.external.enabled=1 
	consumer_key.external.secret=pwd_12345

# LTI Launch to index.php of application
	http://localhost/CloudTime/index.php

You can add a custom parameter allowing you to explicitly sets the region for the service to use. The custom parameter name is **aws_region** and possibles values are:
<table cellspacing="0" border="0"><colgroup><col class="col1"><col class="col2"><col></colgroup><thead><tr><th>Region</th><th>Endpoint</th><th>Protocol</th></tr></thead><tbody><tr><td>US East (Northern Virginia) Region <strong>default</strong></td><td>ec2.us-east-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>US West (Oregon) Region</td><td>ec2.us-west-2.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>US West (Northern California) Region</td><td>ec2.us-west-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>EU (Ireland) Region</td><td>ec2.eu-west-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>Asia Pacific (Singapore) Region</td><td>ec2.ap-southeast-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>Asia Pacific (Sydney) Region</td><td>ec2.ap-southeast-2.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>Asia Pacific (Tokyo) Region</td><td>ec2.ap-northeast-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr><tr><td>South America (Sao Paulo) Region</td><td>ec2.sa-east-1.amazonaws.com</td><td>HTTP and HTTPS</td></tr></tbody></table>

If not setted the custom parameter default value is **US East (Northern Virginia) Region**

Find updated values from [here](http://docs.aws.amazon.com/general/latest/gr/rande.html#ec2_region)

# Cron
You can enable a cron to stop the instances automatically (current is setted a 8 hours but you can change it). 

You have to enable a cron:
	*/5 * * * * wget -O /dev/null http://localhost/lti-CloudTime/stopInstancesCron.php

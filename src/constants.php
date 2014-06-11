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
define ('MYAMIS','myamis');
define ('MYINSTANCES','myinstances');
define ('CHANGESTATE','changeState');
define ('STATERUNNING','running');
define ('STATESTOPING','stopping');
define ('STATETERMINATED','terminated');
define ('STATEPENDING','pending');
define ('RELOAD','reload');
define ('USERNAME', 'custom_username');
define ('CONTEXT_ID', 'context_id');
define ('INSTANCE_ID', 'resource_link_id');
define ('LAUNCH_PRESENTATION_LOCALE', 'launch_presentation_locale');
define ('CUSTOM_LANG', 'custom_lang_id');
define ('LANG', 'lang');
define ('AUTHORIZATION_KEY' , 'uocPHP_aws_amazon');
define ('EMAILOKI', 'email');
define ('FULLNAMEOKI', 'fullName');
define ('FIRSTNAMEOKI', 'firstName');
define ('SURNAMEOKI', 'surName');
define ('IMAGEOKI', 'photo');
define ('SESSION_ID_FIELD' , 'custom_sessionid');
define ('ADMINISTRATOR_ROLE', 'administrator');
define ('LEARNER_ROLE', 'learner');
define ('DOMAIN_PREFIX', 'DOMAIN.');
define ('NAME_OPTION' , 'student_option_');
define ('UN_NAME_OPTION' , 'un_student_option_');
define ('COURSE_ID', 'course_id');
define ('EMAIL', 'email');
define ('FULLNAME', 'fullname');
define ('USER_ID', 'user_id');
define ('INSTANCEC2ID', 'InstanceID');
define ('IS_INSTRUCTOR', 'is_instructor');
define ('IS_ADMINISTRATOR', 'is_administrator');
define ('STOPSELECT', 'stop_selected');
define ('STARTSELECT', 'start_selected');
define ('SALVA_DADES', 'save');
define ('HOURS_AUTOSTOP', 8);
define ('CUSTOM_AWS_REGION', 'custom_aws_region');
define ('ACTION_AMI_MNGT', 'ami_management');
define ('DELETE_INSTANCE', 'delete_instance');
define ('DELETE_IMAGE', 'delete_image');
define ('FIELD_AMI_BY_ID', 'add_ami_by_id');
define ('CREATE_IMAGE_FROM_INSTANCE', 'create_image_from_instance');
define ('ASSIGN_USERS', 'assign_users');
define ('LAUNCH_INSTANCES', 'launch_instances');
//abertranb 20131003 allow multiple sites
define ('FIELD_OTHER_CONF', 'custom_idconfiguration');
define ('DEFAULT_AWS_ACCOUNT', 'default_aws_account');
//****** END
define ('RELOAD_USERS', 'reload_users');
define ('AUTO_ASSIGN_USERS', 'auto_assign_users');
define ('CUSTOM_INSTRUCTIONS', 'custom_instructions');
define ('CUSTOM_AWS_USERNAME', 'custom_aws_username');
define ('DEFAULT_USERNAME_AWS', 'ec2-user');
define ('ASSIGN_ELASTIC_IP', 'assign_elastic_ip');
define ('REASSIGN_ELASTIC_IP', 'reassign_elastic_ip');
define ('RELEASE_ELASTIC_IP', 'release_elastic_ip');

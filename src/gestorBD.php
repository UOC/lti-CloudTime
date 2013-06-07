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
// Include the SDK
	require_once './sdk.class.php';
	require_once 'constants.php';
	require_once('config.inc.php');
        class GestorBD
        {
                private $conn;
                private $_debug = false;
                
                public function __construct(){
                	$this->conectar();
                }
                
                public function conectar()
                {
                        $this->conn = mysql_connect(BD_HOST, BD_USERNAME, BD_PASSWORD);
                        if (!$this->conn) {
                                die('No se pudo conectar: ' . mysql_error());
                                return false;
                        }else{
                                mysql_select_db(BD_NAME, $this->conn);
                                return true;
                        }
                }
                
                public function enableDebug () {
                	$this->_debug = true;
                }
                
                public function disableDebug () {
                	$this->_debug = false;
                }
                
                public function debugMessage($msg) {
                	if ($this->_debug) {
                		echo "<p>DEBUG: ".$msg."</p>";
                	}
                }
                
                public function desconectar()
                {
                    mysql_close($this->conn);
                }
                
                public function escapeString($str)
                {
                    return "'".mysql_real_escape_string($str, $this->conn)."'";
                }
                
                public function consulta($query)      //CUIDAOOOOOOOOO! SQL Injection !!
                {
                	$this->debugMessage($query);
                    $result = mysql_query($query, $this->conn);
                    if (!$result) {
                    	
                    	echo("Error BD ".mysql_error());
                    	echo("<p>Query:".$query."</p>");
                    }
                    return $result;
                }
                
                private function obteObjecteComArray($result){
                	return mysql_fetch_assoc($result);
                }
                
                private function numResultats($result) {
                	return mysql_num_rows($result);
                }

                private function obteComArray($result) {
                	$rows = array();
                	while ($row = mysql_fetch_assoc($result)) {
                		$rows[] = $row;
                	}
                	return $rows;
                }
                /**
                *
                * Retorna el llistat de totes les instancies
                */
                public function retornaTotesLesInstancies()
                {
                	$result = $this->consulta("SELECT instanceId, amazon_region  FROM ec2_instance ");
                	if($this->numResultats($result) > 0){
                		$rows = $this->obteComArray($result);
                		return $rows;
                	}
                	return false;
                }
                
                /**
                 * 
                 * Returns the assigned user for the current instance
                 * @param unknown_type $instanceId
                 * @return multitype:unknown |boolean
                 */
                public function getAssignedUserForInstance($instanceId) {
                	$result = $this->consulta('SELECT u.fullname, u.email  FROM ec2_instance_user_course as i '.
                	'inner join user as u on u.id=i.id_user '.
                	'where i.instanceId= '.$this->escapeString($instanceId));
                	if($this->numResultats($result) > 0){
                		$rows = $this->obteComArray($result);
                		return $rows;
                	}
                	return false;
                }
                
                /**
                *
                * Returns the assigned instructor for the current instance
                * @param unknown_type $instanceId
                * @return multitype:unknown |boolean
                */
                public function getAssignedInstructorForInstance($instanceId) {
                	 $result = $this->consulta('SELECT DISTINCT u.fullname, u.email FROM  `user_course` uc '.
										'INNER JOIN user AS u ON uc.id_user = u.id '.
										'WHERE is_instructor = 1 '.
										'AND id_course '.
											'IN ( '.
													'SELECT id_course '.
													'FROM ec2_instance_user_course as i '.
													'WHERE i.instanceId= '.$this->escapeString($instanceId).')');
                   	 if($this->numResultats($result) > 0){
                    	$rows = $this->obteComArray($result);
                     return $rows;
                	}else{
                		return false;
                	}
                }
                
                /**
                 * 
                 * Retorna la ipAssignada per instancia
                 * @param unknown_type $instanceId
                 */
                public function ipAssignada($instanceId)
                {
                        $result = $this->consulta("SELECT ipAddress FROM ec2_instance where instanceId = ".$this->escapeString($instanceId));
                		if($this->numResultats($result) > 0){
                                $row = $this->obteObjecteComArray($result);
                                return $row['ipAddress'];
                        }else{
                                return false;
                        }
                        
                }
                
                /**
                 * 
                 * Si no tiene IP assignada assigna esta
                 * @param unknown_type $instanceId
                 * @param unknown_type $ipAddress
                 * @return unknown|boolean
                 */
                public function assignaIp($instanceId, $ipAddress)
                {
                	$result = true;
                	if (!$this->ipAssignada($instanceId)) {
                		$result = $this->consulta("UPDATE ec2_instance set ipAddress ="+$this->escapeString($ipAddress)+" where instanceId = ".$this->escapeString($instanceId), $this->conn);
                	}
					return $result;           
                }
                
                /**
                 * 
                 * Obte el username assignat
                 * @param unknown_type $instanceId
                 * @param unknown_type $id_course
                 * @return unknown
                 */
                public function getUserPerInstancia($instanceId, $id_course, $all_data=false) {
                	$result = $this->consulta('SELECT u.* FROM ec2_instance_user_course as e '.
                	'inner join user u on u.id = e.id_user '.
                	'where  id_course = '.$this->escapeString($id_course).' and instanceId = '.$this->escapeString($instanceId));
                	if($this->numResultats($result) > 0){
                		$rows = $this->obteComArray($result);
                		$return = array();
                		foreach ($rows as $r) {
                            $data = $r['userKey'];
                            if ($all_data) {
                                $data = $r;
                            }
                        
                			$return[$r['userKey']] = $data;
                		}
                		return $return;
                	} 
                	return false;
                }
                
                /**
                *
                * Get the list of users of course
                * @param unknown_type $id_course
                * @param unknown_type $only_studens
                * @return unknown
                */
                public function getUsersCourse($id_course, $only_students=false, $all_data=false) {
                	$where = $only_students?' and is_instructor=0 ': '';
                	$result = $this->consulta('SELECT u.* FROM user_course as uc '.
                                	'inner join user u on u.id = uc.id_user '.
                                	'where  uc.id_course = '.$this->escapeString($id_course)
                					.$where);
                	if($this->numResultats($result) > 0){
                		$rows = $this->obteComArray($result);
                		$return = array();
                        foreach ($rows as $r) {
                            $data = $r['userKey'];
                            if ($all_data) {
                                $data = $r;
                            }
                        
                            $return[$r['userKey']] = $data;
                        }
                		return $return;
                	}
                	return false;
                }
                
                /**
                 *
                * Obte LA INSTANCIA assignada per l'usuari
                 * @param int $user_id
                * @param int $id_course
                * @return unknown
                */
                public function getInstanciaPerUsuariCurs($user_id, $course_id) {
                	$result = $this->consulta('SELECT instanceId FROM ec2_instance_user_course as e '.
                	'where  id_course = '.$this->escapeString($course_id).' and id_user = '.$this->escapeString($user_id));
                	if($this->numResultats($result) > 0){
						$row = $this->obteObjecteComArray($result);
                		return $row['instanceId'];
                    } else {
                		$result = false;
                    }
                	return $result;
                }
                
                
                /**
                *
                * Si no tiene IP assignada assigna esta
                * @param unknown_type $instanceId
                * @param unknown_type $ipAddress
                * @return unknown|boolean
                */
                public function assignaUsuari($instanceId, $username, $id_course, $userKeyCreated)
                {
                	
	                $result = false;
	                $user = $this->get_user_by_username($username);
	                //TODO pensar com fer-ho ja que no fucniona amb $user_assigned = $this->getInstanciaPerUsuariCurs($user['id'], $id_course);
	                //$user_assigned = $this->getUserPerInstancia($instanceId, $id_course);
	                //Eliminem la relacio anterior si ni hi ha
	                $sql = 'DELETE FROM ec2_instance_user_course WHERE id_user ='.$user['id'].' AND  id_course = '.$id_course;
	                $result = $this->consulta($sql);
	                 
	                //$instance_assigned = $this->getInstanciaPerUsuariCurs($user['id'], $id_course);
	                //if (!$instance_assigned) {
	                	$sql = 'INSERT INTO ec2_instance_user_course (instanceId, id_user, id_course, total_time_connected, created, userKeyCreated) 
	                											VALUES ('.$this->escapeString($instanceId).','.$user['id'].','.$id_course.', 0, now(), '.$this->escapeString($userKeyCreated).')';
	                /*} else {
	                	$sql = 'UPDATE ec2_instance_user_course SET id_user ='.$user['id'].', instanceId ='.$this->escapeString($instanceId).' where id_course = '.$id_course.' AND instanceId ='.$this->escapeString($instance_assigned);
	                }*/
	                $result = $this->consulta($sql);
	                return $result;
                }
                
                
                /**
                *
                * Si no tiene IP assignada assigna esta
                * @param unknown_type $instanceId
                * @param unknown_type $ipAddress
                * @return unknown|boolean
                */
                public function desassignaUsuaris($instanceId, $id_course)
                {
                    
                    $result = false;
                    $sql = 'DELETE FROM ec2_instance_user_course WHERE instanceId ='.$this->escapeString($instanceId).' AND  id_course = '.$id_course;
                    $result = $this->consulta($sql);
                    return $result;
                }
                /**
                *
                * Mira si ja esta a la bd o no
                * @param unknown_type $instanceId
                */
                public function existeixInstancia($instanceId)
                {
                	$result = $this->consulta("SELECT instanceId FROM ec2_instance where instanceId = ".$this->escapeString($instanceId));
					return $this->numResultats($result) > 0;                
                }
                /**
                 * 
                 * Afegeix o actualitza 
                 * @param unknown_type $instanceId
                 * @param unknown_type $imageId
                 * @param unknown_type $name
                 * @param unknown_type $keyName
                 * @param unknown_type $instanceState
                 * @param unknown_type $ipAddress
                 * @param unknown_type $ip_amazon
                 * @param unknown_type $privateDnsName
                 * @param unknown_type $launchTime
                 * @param unknown_type $instanceType
                 * @param unknown_type $kernelId
                 * @param unknown_type $architecture
                 * @param unknown_type $monitoring
                 * @param unknown_type $blockDeviceMapping
                 * @return unknown
                 */
                public function afegeixInstancia($instanceId, $imageId, $name, $keyName, $instanceState, 
                								$ipAddress, $ip_amazon, $privateDnsName, $launchTime,
												$instanceType, $kernelId, $architecture, $monitoring, 
												$blockDeviceMapping, $amazon_region = null) {
                	$result = false;
                	if (!$this->existeixInstancia($instanceId)) {
                		$sql = 'INSERT INTO ec2_instance (instanceId, imageId, name, keyName, instanceState,  ipAddress, ip_amazon, privateDnsName, 
														launchTime, instanceType, kernelId, architecture, monitoring, blockDeviceMapping, amazon_region) 
											VALUES ('.$this->escapeString($instanceId).','.$this->escapeString($imageId).','.$this->escapeString($name).','.
											$this->escapeString($keyName).','.$this->escapeString($instanceState).','.$this->escapeString($ipAddress).','.
											$this->escapeString($ip_amazon).','.$this->escapeString($privateDnsName).','.$this->escapeString($launchTime).','.
											$this->escapeString($instanceType).','.$this->escapeString($kernelId).','.$this->escapeString($architecture).','.
											$this->escapeString($monitoring).','.$this->escapeString($blockDeviceMapping).', '.($amazon_region==null?'null':$this->escapeString($amazon_region)).')';
                	} else {
                		$qMes = '';
                		if ($instanceState=='running') {
                			$qMes = ',ipAddress = '.$this->escapeString($ipAddress).', ip_amazon = '.$this->escapeString($ip_amazon).', privateDnsName = '.$this->escapeString($privateDnsName);
                		}
                		$sql = 'UPDATE ec2_instance SET imageId ='.$this->escapeString($imageId).', name = '.$this->escapeString($name).', keyName = '.$this->escapeString($keyName).',
                		 				instanceState = '.$this->escapeString($instanceState).',
                						launchTime = '.$this->escapeString($launchTime).', instanceType = '.$this->escapeString($instanceType).', kernelId = '.$this->escapeString($kernelId).', 
                						architecture = '.$this->escapeString($architecture).', monitoring = '.$this->escapeString($monitoring).', blockDeviceMapping = '.$this->escapeString($blockDeviceMapping).', amazon_region = '.($amazon_region==null?'null':$this->escapeString($amazon_region)).'  
                												'.$qMes.'	where instanceId ='.$this->escapeString($instanceId);
                	}
                	$result = $this->consulta($sql);
                	return $result;
                } 
                
                /**
                 * 
                 * Obte si existeix l'usuari 
                 * @param unknown_type $userKey
                 * @return array|boolean
                 */
                public function get_user_by_username($userKey) {
                	$result = $this->consulta("SELECT * FROM user where userKey = ".$this->escapeString($userKey));
                	if($this->numResultats($result) > 0){
                		$row = $this->obteObjecteComArray($result);
                		return $row;
                	}else{
                		return false;
                	}
                		
                }

                /**
                 * 
                 * Register user in database
                 * @param unknown_type $username
                 * @param unknown_type $name
                 * @param unknown_type $email
                 * @param unknown_type $image
                 * @return boolean
                 */
                public function register_user($username, $name, $email, $image) {
                	$result = false;
                	$sql = 'INSERT INTO user (userKey, fullname, email, image, last_session,  blocked, created) 
                												VALUES ('.$this->escapeString($username).','.$this->escapeString($name).','.$this->escapeString($email).','.
                	$this->escapeString($image).', now(), 0, now())';
                	$result = $this->consulta($sql);
                	return $result;
                }

                /**
                 * 
                 * Updates user data
                 * @param unknown_type $username
                 * @param unknown_type $name
                 * @param unknown_type $email
                 * @param unknown_type $image
                 * @return unknown
                 */
                public function update_user($username, $name, $email, $image) {
                	$result = false;
                	$sql = 'UPDATE user SET fullname = '.$this->escapeString($name).', email = '.$this->escapeString($email).', image = '.$this->escapeString($image).', last_session=now() '.  
                	'WHERE userKey = '.$this->escapeString($username);
                	$result = $this->consulta($sql);
                	return $result;
                } 
                
                
                /**
                 *
                  * Obte si existeix el curs
                * @param unknown_type $courseKey
                 * @return array|boolean
                */
                public function get_course_by_id($id) {
                $result = $this->consulta("SELECT * FROM course where id = ".$this->escapeString($id));
                if($this->numResultats($result) > 0){
                	$row = $this->obteObjecteComArray($result);
                	                	return $row;
                }else{
                	return false;
                }
                }
                
                /**
                *
                * Obte si existeix el curs
                * @param unknown_type $courseKey
                * @return array|boolean
                */
                public function get_course_by_courseKey($courseKey) {
	                $result = $this->consulta("SELECT * FROM course where courseKey = ".$this->escapeString($courseKey));
	                if($this->numResultats($result) > 0){
	                	$row = $this->obteObjecteComArray($result);
	                	return $row;
	                }else{
	                	return false;
	                }
                }
                
               	/**
               	*
               	* Register courseKey in database
               	* @param unknown_type $courseKey
               	* @param unknown_type $title
               	* @param string $amazon_region referes to aws amazon_region
               	* @return boolean
                */
    			public function register_course($courseKey, $title, $amazon_region=null) {
                	$result = false;
                	$sql = 'INSERT INTO course (courseKey, title, created, amazon_region)
                				VALUES ('.$this->escapeString($courseKey).','.$this->escapeString($title).', now(), '.($amazon_region==null?'null':$this->escapeString($amazon_region)).')';
          			$result = $this->consulta($sql);
                	return $result;
                }
                
                /**
                *
                * Updates course data
                * @param unknown_type $courseKey
                * @param unknown_type $title
                * @param string $amazon_region referes to aws amazon_region
            	* @return unknown
                */
                public function update_course($courseKey, $title, $amazon_region=null) {
    	            $result = false;
	                $sql = 'UPDATE course SET title = '.$this->escapeString($title).', amazon_region = '.($amazon_region==null?'null':$this->escapeString($amazon_region)).
	                ' WHERE courseKey = '.$this->escapeString($courseKey);
	                $result = $this->consulta($sql);
	                return $result;
                }
                /**
                 * 
                 * Obte el rol en la bd
                 * @param int $course_id
                 * @param int $user_id
                 * @return boolean
                 */
                public function obte_rol($course_id, $user_id) {
                	$result = $this->consulta("SELECT * FROM user_course where id_user = ".$user_id." AND id_course = ".$course_id);
                	if($this->numResultats($result) > 0){
                		$row = $this->obteObjecteComArray($result);
                		return $row;
                	}else{
                		return false;
                	}
                }
                /**
                 * 
                 * Afegeix l'usuari al curs si no esta
                 * @param unknown_type $course_id
                 * @param unknown_type $user_id
                 * @param unknown_type $isInstructor
                 * @return unknown
                 */
                public function join_course($course_id, $user_id, $isInstructor) {
                	$result = false;
                	if (!$this->obte_rol($course_id, $user_id)) {
                		$sql = 'INSERT INTO user_course (id_user, id_course, is_instructor) 
                												VALUES ('.$this->escapeString($user_id).','.$this->escapeString($course_id).','.($isInstructor?1:0).')';
                	} else {
                		$sql = 'UPDATE user_course SET is_instructor ='.($isInstructor?1:0).' where id_user = '.$user_id.' AND id_course = '.$course_id;
                	}
                	$result = $this->consulta($sql);
                	return $result;
                }
                /**
                 * 
                 * Afegeix l'usuari al curs si no esta
                 * @param unknown_type $course_id
                 * @param unknown_type $username
                 * @return unknown
                 */
                public function afegeixEstudiant($course_id, $username) {
                	$isInstructor = 0;
                	$result = false;
                	$user=$this->get_user_by_username($username);
                	if (!$user) {
                		if ($this->register_user($username, $username, '', '')) {
                			$user = $this->get_user_by_username($username);
                		}
                	}
                	if ( $user ) {
                		$user_id = $user['id'];
	                	$result = $this->join_course($course_id, $user_id, $isInstructor);
                	}
                	return $result;
                }
                

                /**
                 * Deletes an instance from DB
                 * @param unknown $instanceId
                 * @return unknown
                 */
                public function eliminaInstancia($instanceId)
                {
                	$result = false;
                	$sql = 'DELETE FROM ec2_instance WHERE instanceId ='.$this->escapeString($instanceId);
                	$result = $this->consulta($sql);
                	return $result;
                }


                /**
                 * 
                 * Afegeix o actualitza 
                 * @param unknown_type $imageId
                 * @param unknown_type $imageState
                 * @param unknown_type $name
                 * @param unknown_type $description
                 * @param unknown_type $isPublic
                 * @param unknown_type $architecture
                 * @param unknown_type $imageType
                 * @param unknown_type $kernelId
                 * @param unknown_type $amazon_region
                 * @return unknown
                 */
                public function afegeixAmi($imageId, $imageState, $name, 
                                                $description, $isPublic, $architecture, $imageType,
                                                $kernelId, $amazon_region = null) {
                    $result = false;
                    if (!$this->existeixAmi($imageId)) {
                        $sql = 'INSERT INTO ec2_ami (imageId, imageState, name, description,  isPublic, architecture, imageType, kernelId, amazon_region) 
                                            VALUES ('.$this->escapeString($imageId).','.$this->escapeString($imageState).','.
                                            $this->escapeString($name).','.$this->escapeString($description).','.($isPublic?1:0).','.
                                            $this->escapeString($architecture).','.$this->escapeString($imageType).','.
                                            $this->escapeString($kernelId).','.
                                            ($amazon_region==null?'null':$this->escapeString($amazon_region)).')';
                    } else {
                        $sql = 'UPDATE ec2_ami SET name = '.$this->escapeString($name).', imageState = '.$this->escapeString($imageState).',
                                        description = '.$this->escapeString($description).',
                                        isPublic = '.($isPublic?1:0).', architecture = '.$this->escapeString($architecture).',
                                        imageType = '.$this->escapeString($imageType).', kernelId = '.$this->escapeString($kernelId).', amazon_region = '.($amazon_region==null?'null':$this->escapeString($amazon_region)).'  
                                        where imageId = '.$this->escapeString($imageId);
                    }
                    $result = $this->consulta($sql);
                    return $result;
                } 
                /**
                *
                * Mira si ja esta a la bd o no
                * @param unknown_type $imageId
                */
                public function existeixAmi($imageId)
                {
                    $result = $this->consulta("SELECT imageId FROM ec2_ami where imageId = ".$this->escapeString($imageId));
                    return $this->numResultats($result) > 0;                
                }
                /**
                * Relaciona una ami amb el curs
                */
                public function afegeixAmiCourse($imageId, $course_id) {
                    $result = false;
                    if (!$this->existeixAmiCourse($imageId, $course_id)) {
                        $sql = 'INSERT INTO ec2_ami_course (imageId, courseId) 
                                            VALUES ('.$this->escapeString($imageId).','.$this->escapeString($course_id).')';
                        $result = $this->consulta($sql);
                    } else {

                        $result = true;
                   
                    }
                    return $result;
                } 
                /**
                *
                * Mira si ja esta a la bd o no
                * @param unknown_type $imageId
                * @param unknown_type $course_id
                */
                public function existeixAmiCourse($imageId, $course_id)
                {
                    $result = $this->consulta("SELECT imageId FROM ec2_ami_course where imageId = ".$this->escapeString($imageId)." AND courseId = ".$this->escapeString($course_id));
                    return $this->numResultats($result) > 0;                
                }
                
                /**
                *
                * Get the list of amis by course
                * @param unknown_type $id_course
                * @return unknown
                */
                public function getAmisByCourseId($id_course) {
                    $result = $this->consulta('SELECT ami.* FROM ec2_ami_course as ami_course '.
                                    'inner join ec2_ami ami on ami.imageId = ami_course.imageId '.
                                    'where  ami_course.courseId = '.$this->escapeString($id_course)
                                    );
                    if($this->numResultats($result) > 0){
                        $rows = $this->obteComArray($result);
                        return $rows;
                    }
                    return false;
                }


                /**
                 * Deletes an image from DB
                 * @param unknown $instanceId
                 * @return unknown
                 */
                public function eliminaImatge($imageId)
                {
                    $result = false;
                    $sql = 'DELETE FROM ec2_ami_course WHERE imageId ='.$this->escapeString($imageId);
                    $result = $this->consulta($sql);
                    if ($result) {
                        $sql = 'DELETE FROM ec2_ami  WHERE imageId ='.$this->escapeString($imageId);
                        $result = $this->consulta($sql);
                    }
                    return $result;
                }

        }
?>
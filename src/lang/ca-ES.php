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
$language['no estas autoritzat'] = 'No estas autoritzat';
$language['no tens instancies assignades'] = 'no tens màquines assignades';
$language['Ec2 instance'] = 'Ec2 instance';
$language['Segur que vol canviar lestat a la instancia'] = 'Segur que vol canviar l\'estat a la màquina';
$language['back'] = 'tornar';
$language['noinstancesassignated'] = 'No hem trobat cap màquina assignada al seu usuari, contacta amb el professor';
$language['myinstancesmanagement'] = 'Gestió de les meves màquines';
$language['instruccions_caps'] = 'A continuació mostrem les instruccions per tal d\'encendre i parar la màquina';
$language['instruccions1'] = 'El primer que necessites és tenir la màquina encesa.';
$language['instruccions2'] = 'Una vegada estigui encesa podras consultar la IP (si encara no esta disponible refresca la pàgina).';
$language['instruccions3'] = 'Aleshores pots obrir una sessi&oacute; per SSH indicant l\'username <b>user</b> i el password que t\'ha indicat el professor. Per connectar-te des de linux/mac obre el terminal i escriu:';
$language['instruccions3_ec2-user'] = 'Aleshores pots obrir una sessi&oacute; per SSH indicant l\'username <b>%USERNAME%</b> i la parella de claus que et pots descarregar %s. Has de donar permissos 400 al fitxer. Per connectar-te des de linux/mac obre el terminal i escriu:';
$language['instruccions4'] = 'Si ho fas amb windows hauras d\'usar una eina tipus Putty';
$language['dadesactualsinstancia'] = 'L\'identificador de la màquina és <b>%s</b> l\'estat actual és:';
$language['start'] = 'Inicia Màquina';
$language['stop'] = 'Atura Màquina';
$language['amb_ip'] = 'amb ip';
$language['no_ip'] = 'Encara no té ip assignada refresca la pàgina clicant a';
$language['primer_encen_instance'] = 'Primer encen la màquina';
$language['refresh'] = 'refresca';
$language['apaga_maquina'] = 'Una vegada acabis d\'usar la màquina has d\'aturar-la clicant a';
$language['Ec2CourseInterface'] = 'Ec2 Course Interface';
$language['busca'] = 'busca';
$language['instanceId'] = 'instanceId';
$language['Usuari assignat'] = 'Usuari/s assignat/s';
$language['Info'] = 'Informaci&oacute; Acc&eacute;s';
$language['AccessInfo'] = 'Acc&eacute;s';
$language['Nom'] = 'Nom';
$language['imageId'] = 'imageId';
$language['instanceState'] = 'instanceState';
$language['ipAddress'] = 'ipAddress';
$language['ip amazon'] = 'ip amazon';
$language['privateDnsName'] = 'privateDnsName';
$language['date launched'] = 'date launched';
$language['launchTime'] = 'launchTime';
$language['instanceType'] = 'instanceType';
$language['kernelId'] = 'kernelId';
$language['Arquitectura'] = 'Arquitectura';
$language['Selecciona quantes maquines'] = 'Selecciona el número de màquines a crear';
$language['Crear maquines com'] = 'Crear màquines com';		
$language['selecciona alguna instancia'] = 'Has de seleccionar el número de màquines a crear';
$language['Error creant maquines'] = 'Error 101 creant màquines';	
$language['Error obtenint resposta de crear maquines'] = 'Error 102 obtenint la resposta de crear màquines'; 
$language['Error creant maquines resposta sense instancia']	= 'Error 103 obtenint la resposta de la creació de màquines';
$language['Maquina'] = 'Màquina';
$language['Maquina creada correctament'] = 'Màquina %s creada correctament';
$language['No es pot eliminar'] = 'Impossible eliminar ja que està associada a un estudiant';
$language['Elimina maquina'] = 'Elimina màquina';
$language['Eliminar'] = 'Eliminar';
$language['Segur que vol eliminar la instancia'] = 'Segur que vol eliminar la màquina';
$language['Maquina eliminada correctament'] = 'Màquina %s eliminada correctament';
$language['Error eliminant maquina de la bd'] = 'Error 201 eliminant la màquina de la BD %s';
$language['Error eliminant maquina amazon'] = 'Error 202 eliminant la màquina %s';
$language['Create'] = 'Crear';
$language['Create image'] = 'Crear imatge';
$language['InvalidSession'] = 'Sessi&oacute; inv&agrave;lida ha de tornar a llan&ccedil;ar l\'aplicaci&oacute;';
$language['Ec2CourseInterfaceAmis'] = 'Imatges';
$language['Ec2CourseInterfaceInstances'] = 'Inst&agrave;ncies';
$language['is_public_ami_true'] = 'S&iacute;';
$language['is_public_ami_false'] = 'No';
$language['imageState'] = 'Estat';
$language['imageType'] = 'Tipus d\'Imatge';
$language['Elimina image'] = 'Elimina imatge';
$language['segons'] = 'segons';
$language['Close'] = 'Tanca';
$language['search'] = 'Busca';
$language['reload'] = 'Recarrega';
$language['save'] = 'Salva';
$language['stop_selected'] = 'Aturar Seleccionades';
$language['Actions'] = 'Accions';
$language['start_selected'] = 'Iniciar Seleccionades';
$language['Save changes'] = 'Salvar canvis';
$language['Delete image'] = 'Eliminat imatge';
$language['Ami Id'] = 'Id Ami';
$language['add_ami_by_id'] = 'afegir imatge per id';
$language['Ami Id Explination'] = 'Has d\'indicar l\'identificador de l\'ami, de la forma "ami-XXXXXXX". El trobar&agrave;s en la consola d\'AWS';
$language['AssociateAMIOK'] = 'Ami %s associada correctament al curs';
$language['AssociateAMIError'] = 'Error associatant l\'Ami %s al curs';
$language['ErrorAssociatingAMIDoesBotExistImage'] = 'Ami %s no existeix';
$language['ErrorCreatingImageFromInstance'] = 'Error creant imatge a partir de la inst&agrave;ncia %s';
$language['CreatedImageSuccessfully'] = 'Imatge %s creada correctament a partir de la inst&agrave;ncia.';
$language['Launch from image'] = 'Crea Int&agrave;ncies';
$language['Segur que vol eliminar la imatge'] = 'Segur que vol eliminar la imatge';
$language['Imatge eliminada correctament'] = 'Imatge %s eliminada correctament';
$language['Error eliminant imatge de la bd'] = 'Error 301 eliminant la imatge de la BD %s';
$language['Error eliminant imatge amazon'] = 'Error 302 eliminant la imatge %s';
$language['lti:errornotauthorized'] = 'Error no est&agrave;s autoritzat en aquest curs, has de ser Admin, Professor o Estudiant';
$language['lti:resource_id_required'] = 'Error falten parametres, no s\'ha indicat el resource_id';
$language['register_user_error'] = 'Error registrant a l\'usuari en el sistema';
$language['updated_user_error'] = 'Error actualitzant a l\'usuario en el sistema';
$language['lti:errorjoincourse'] = 'Error registrant a l\'usuario en el curs';
$language['lti:errorregistercourse'] = 'Error registrant el curs en el sistema';
$language['lti:errorupdatingcourse'] = 'Error actualitzant el curs en el sistema';
$language['desassignar'] ='Desassignar';
$language['assignar'] ='Assignar';
$language['hores'] ='Hores';
$language['Developed By UOC'] = 'Desenvolupat per la Universitat Oberta de Catalunya';
$language['Usuaris des-assignats correctament'] = 'Usuaris des/assignats correctament a la inst&agrave;ncia %s';
$language['Instancies parades correctament'] = 'Inst&agrave;ncies parades correctament';
$language['Instancies iniciades correctament'] = 'Inst&agrave;ncies iniciades correctament';
$language['Instancia parada correctament'] = 'Inst&agrave;ncia %s parada correctament';
$language['Instancia iniciada correctament'] = 'Inst&agrave;ncia %s iniciada correctament';
$language['Error folder no exist or no writable'] = 'Error el directori PEM no existeix o no es pot escriure';
$language['Error file can not write'] = 'Error fitxer PEM no es pot escriure';
$language['Error file can not open'] = 'Error no es pot obrir el fitxer PEM';
$language['Public Amis'] = 'Cerca ESB-Backed 32 o 64 p&uacte;bliques en la p&agrave;gina %s, has de seleccionar-la de tla regi&oacute; actual';
$language['Pots descarregar key pair'] = 'Pots descarregar la parella de claus des de %s.';
$language['aqui'] = 'aqu&iacute;';
$language['Missing parameters'] = 'Falten par&agrave;metres';
$language['Change name'] = 'Canviar nom';
$language['reload_users'] = 'Recarrega Usuaris';
$language['auto_assign_users'] = 'Autoassignar usuaris';
$language['change_instructions'] = 'Canvia instruccions';
$language['Username2ConnectInstance'] = 'Username per connectar a la màquina';
$language['ReplacementsInstructions'] = 'Pots usar les variables: %USERNAME% => nom d\'usuari per connectar, %IP% => La ip assignada';
$language['InfoElasticIP'] = 'Elastic Ips d\'Amazon &eacute;s similar a la adre&cedil;a IP est&agrave;tica en els centres de dades tradicionals, amb una difer&egrave;ncia clau. Un usuari pot assignar una adre&cedil; IP el&agrave;stica a qualsevol inst&agrave;ncia de m&agrave;quina virtual sense la ayuda de un administrador de la xarxa i sense esperes a que el DNS es propagui. M&eacute;s info %saqu&iacute;%s';
$language['assignIP'] = 'Assignar Elastic IP';
$language['assignIPShouldBeRunning'] = 'Per assignar una elastic IP ha d\'estar encesa';
$language['Segur que vol assingar Elastic IP per'] = 'Segur que vol assignar una elastic IP per la instància';
$language['Error allocating elastic ip'] = 'Error obtenint l\'elastic IP a la inst&agrave;ncia %s';
$language['Error associating elastic ip'] = 'Error associant l\'elastic IP a la inst&agrave;ncia %s';
$language['IP Associated sucessfully to instance'] = 'IP %s associada correctament a la inst&agrave;ncia %s';
$language['Error associating instance to ip in db'] = 'Error associant la inst&agrave;ncia %s a la ip %s en bd';
$language['disassociateIP'] = 'Desassociar IP';
$language['InfoElasticIIP'] = 'Si l\'alliberes la IP pots aconseguir una altra per&ograve; segurament no ser&agrave; la mateixa. Elastic Ips d\'Amazon &eacute;s similar a la adre&cedil;a IP est&agrave;tica en els centres de dades tradicionals, amb una difer&egrave;ncia clau. Un usuari pot assignar una adre&cedil; IP el&agrave;stica a qualsevol inst&agrave;ncia de m&agrave;quina virtual sense la ayuda de un administrador de la xarxa i sense esperes a que el DNS es propagui. M&eacute;s info %saqu&iacute;%s';
$language['releaseIP'] = 'Alliberar IP';
$language['InfoElasticReleaseIP'] = 'Si alliberes la IP pots aconseguir una altra per&ograve; segurament no ser&agrave; la mateixa. Elastic Ips d\'Amazon &eacute;s similar a la adre&cedil;a IP est&agrave;tica en els centres de dades tradicionals, amb una difer&egrave;ncia clau. Un usuari pot assignar una adre&cedil; IP el&agrave;stica a qualsevol inst&agrave;ncia de m&agrave;quina virtual sense la ayuda de un administrador de la xarxa i sense esperes a que el DNS es propagui. M&eacute;s info %saqu&iacute;%s';
//$language['InfoElasticDisassociateIP'] = 'Si l\'alliberes la IP pots aconseguir una altra per&ograve; segurament no ser&agrave; la mateixa. Elastic Ips d\'Amazon &eacute;s similar a la adre&cedil;a IP est&agrave;tica en els centres de dades tradicionals, amb una difer&egrave;ncia clau. Un usuari pot assignar una adre&cedil; IP el&agrave;stica a qualsevol inst&agrave;ncia de m&agrave;quina virtual sense la ayuda de un administrador de la xarxa i sense esperes a que el DNS es propagui. M&acute;s info %saqu&iacute;%s';
$language['Segur que vol alliberar Elastic IP per'] = 'Segur que vol alliberar Elastic IP per';
$language['Released sucessfully to instance'] = 'Alliberada satisfact&ograve;riament la ip de la inst&agrave;cia %s';
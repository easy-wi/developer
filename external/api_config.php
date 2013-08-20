<?php 
/**
 * File: api_config.php.
 * Author: Ulrich Block
 * Copyright 2010-2012
 * Contact: support@easy-wi.com
 * Page: easy-wi.com
 */

// Configuring the API. Should be placed in another file and included

// The database access
$config['dbHost']='localhost';
$config['dbName']='database'; 
$config['dbUser']='databaseUser'; 
$config['dbPwd']='securePassword'; 

// Access to the file 
$config['passwordToken']='myPasswordToken';
$config['allowedIPs']=array('1.1.1.1','1.1.1.2');

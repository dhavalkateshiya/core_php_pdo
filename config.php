<?php
//ini_set("display_errors",1);

// Data base setting 
$db_host		=	"localhost";
$db_user		=	"dhavalk";
$db_password	=	"dhavalk";
$db_name		=	"dhavalk";

$site_url		=	"http://192.168.1.111:808/ar_flow/site/";
$base_path		=	dirname(__FILE__);

if(!defined('ARFLOW_DB_HOST'))
	define( 'ARFLOW_DB_HOST', $db_host );

if(!defined('ARFLOW_DB_USER'))
	define( 'ARFLOW_DB_USER', $db_user );

if(!defined('ARFLOW_DB_PASSWORD'))
	define( 'ARFLOW_DB_PASSWORD',$db_password );

if(!defined('ARFLOW_DB_NAME'))
	define( 'ARFLOW_DB_NAME', $db_name  );

if (!defined('ARFLOW_SITEURL'))
    define( 'ARFLOW_SITEURL', $site_url );

if (!defined('ARFLOW_BASEPATH'))
    define( 'ARFLOW_BASEPATH', $base_path);

if(!defined('TIME_ZONE'))
	define( 'TIME_ZONE', 'UTC');

//Set Default Time Zone
date_default_timezone_set(TIME_ZONE);

//Start Session if not create.
if(!isset($_SESSION))
{
	session_start();
}

$urlNEW = explode('?',$_SERVER['REQUEST_URI']);
$file = basename($urlNEW[0], ".php");			// Get File Name 

//Add database class
require_once("classes/database-class.php");
$DB    = new DB; //Database class Object

//Other class Files
require_once("classes/category-class.php");
?>
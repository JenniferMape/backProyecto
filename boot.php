<?php
require ('env.php');
require('./vendor/idiorm.php');
require 'vendor/autoload.php';

/******************************
 * Conexion DB
*******************************/
ORM::configure("mysql:host=".DB_HOST.";dbname=".DB_NAME,null);

ORM::configure('username', DB_USER);

// ORM::configure('password', $db_password);

ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

ORM::configure('return_result_sets', true);


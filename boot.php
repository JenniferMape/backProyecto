<?php
require('./vendor/idiorm.php');
require 'vendor/autoload.php';


/******************************
 * Conexion DB
*******************************/
// Acceso a las variables de entorno
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');

// Configuración de la base de datos en ORM
ORM::configure("mysql:host=" . $dbHost . ";dbname=" . $dbName);
ORM::configure('username', $dbUser);
ORM::configure('password', $dbPassword);

ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('return_result_sets', true);


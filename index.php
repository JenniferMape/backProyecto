<?php



/******************************
 * Mostrar errores
*******************************/

ini_set('display_errors',1);
ini_set("log_errors",1);
ini_set("error_log", "C:\\xampp\htdocs\api\logs");

// Encabezados CORS
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

// Manejo de solicitudes OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // Código de respuesta para "No Content"
    exit;
}

// // Encabezados CORS para otras solicitudes
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// header("Allow: GET, POST, OPTIONS, PUT, DELETE");
// header("Access-Control-Allow-Headers: Authorization, Content-Type");
// // Manejo de solicitudes OPTIONS para CORS
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     header("Access-Control-Allow-Origin: *");
//     header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
//     header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
//     exit;
// }


/******************************
 * Requires
*******************************/
 require_once("boot.php");
 require_once "controllers\\routes_controller.php";

 $controladorRutas = new RoutesController();

 $controladorRutas->index();


?>
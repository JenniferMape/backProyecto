<?php
$cR = new RoutesController();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routesArray = explode('/', trim($uri, '/'));
$routesArray = array_filter($routesArray);
$routesArray = array_values($routesArray); // Reindexar para asegurarse de que los índices sean secuenciales
// echo '<pre>'; print_r($routesArray); echo '</pre>';

// Debugging: descomentar para ver la estructura del array de rutas
// echo '<pre>'; print_r($routesArray); echo '</pre>';

/**
 * =================================================
 * Manejo cuando no se hace ninguna petición a la API
 * =================================================
 */
if (empty($routesArray)) {
    $json = array(
        'status' => 404,
        'result' => 'Not found'
    );
    echo json_encode($json);
    http_response_code($json["status"]);
    return;
}

/**
 * =================================================
 * Manejo cuando sí se hace una petición a la API
 * =================================================
 */
if (!empty($routesArray)) {
    $controllerName = preg_replace('/[^a-zA-Z0-9_]/', '', $routesArray[0]); // Sanitización básica para seguridad
    //echo '<pre>'; print_r($controllerName); echo '</pre>';
    
    $controllerPath = './controllers/' . $controllerName . '_controller.php';

    if (file_exists($controllerPath)) {
        include($controllerPath);
    } else {
        $json = array(
            'status' => 404,
            'result' => 'Controller not found',
            'array' => $routesArray[0]
        );
        echo json_encode($json);
        http_response_code(404);
        return;
    }
}

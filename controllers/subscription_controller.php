<?php
include('./helpers/HTTPMethod.php');
include('./models/subscription.php');
include('./helpers/response.php');
include('./helpers/filterUrl.php');

$method = new HTTPMethod();
$methodR = $method->getMethod();
$controller = new Subscription();

// Verificar si la primera parte de la URL es 'subscription'
if ($routesArray[0] == 'subscription') {
    switch ($methodR['method']) {
        // Manejar peticiones de tipo GET 
        case 'GET':
            // Si se proporciona un ID numérico, obtener suscripciones por usuario
            if (!empty($routesArray[1]) && is_numeric($routesArray[1])) {
                $id = (int)$routesArray[1];
                $subscriptions = $controller->getSubscriptionsByUser($id);
                if ($subscriptions) {
                    sendJsonResponse(200, $subscriptions);
                } else {
                    sendJsonResponse(404, null, 'No se encontraron suscripciones para el usuario especificado.');
                }
            } else {
                // Obtener todas las suscripciones si no se proporciona un ID
                $subscriptions = $controller->getAllSubscriptions();
                if($subscriptions){
                    sendJsonResponse(200, $subscriptions);
                }else{
                    sendJsonResponse(404, null, 'No hay suscripciones.');
                }
            }
            break;

        // Manejar peticiones de tipo POST para crear una nueva suscripción
        case 'POST':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Verificación de campos obligatorios
                if (empty($_POST['id_user_subscription']) || empty($_POST['id_category_subscription'])) {
                    sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                    return;
                }
                
                $data = [
                    'id_user_subscription' => filter_input(INPUT_POST, 'id_user_subscription', FILTER_VALIDATE_INT),
                    'id_category_subscription' => filter_input(INPUT_POST, 'id_category_subscription', FILTER_VALIDATE_INT)
                ];

                if ($controller->addSubscription($data)) {
                    sendJsonResponse(201, null, 'Suscripción creada exitosamente.');
                } else {
                    sendJsonResponse(500, null, 'Error al crear la suscripción.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
            break;

        // Manejar peticiones de tipo PUT para actualizar una suscripción
        case 'PUT':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if ($controller->updateSubscription($data)) {
                    sendJsonResponse(200, $data, 'La suscripción ha sido actualizada.');
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar la suscripción.');
                }
            } else {
                sendJsonResponse(400, null, 'Datos inválidos.');
            }
            break;

        // Manejar peticiones de tipo DELETE para eliminar una suscripción
        case 'DELETE':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
            if ($id && $controller->deleteSubscription($id)) {
                sendJsonResponse(200, null, 'La suscripción ha sido borrada satisfactoriamente.');
            } else {
                sendJsonResponse(404, null, 'La suscripción no existe.');
            }
            break;

        // Manejar peticiones que no se ajusten a los anteriores métodos
        default:
            sendJsonResponse(405, null, 'Método no permitido.');
            break;
    }
} else {
    sendJsonResponse(404, null, 'Recurso no encontrado.');
}

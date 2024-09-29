<?php
include('./helpers/HTTPMethod.php');
include('./models/favorite.php');
include('./helpers/response.php');
include('./helpers/filterUrl.php');

$method = new HTTPMethod();
$methodR = $method->getMethod();
$controller = new Favorite();

// Verificar si la primera parte de la URL es 'favorite'
if ($routesArray[0] == 'favorite') {
    switch ($methodR['method']) {
            // Manejar peticiones de tipo GET 
        case 'GET':
            if (!empty($routesArray[1]) && is_numeric($routesArray[1])) {
                // Obtener el id de la URL
                $id = (int)$routesArray[1];
                $favorites = $controller->getFavoritesByUser($id);
                if ($favorites) {
                    sendJsonResponse(200, $favorites);
                } else {
                    sendJsonResponse(404, null, 'No se encontraron favoritos.');
                }
            } else {
                $allFavorites = $controller->getAllFavorites();
                // Verificar si se encontraron favoritos
                if ($allFavorites) {
                    sendJsonResponse(200, $allFavorites);
                } else {
                    sendJsonResponse(404, null, 'No se encontraron favoritos.');
                }
            }
            break;

            // Manejar peticiones de tipo POST para crear un nuevo favorito
        case 'POST':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Comprobación de que los campos obligatorios estén completados
                if (empty($_POST['id_user_favorite']) || empty($_POST['id_offer_favorite'])) {
                    sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                    return;
                }

                $data = [
                    'id_user_favorite' => filter_input(INPUT_POST, 'id_user_favorite', FILTER_VALIDATE_INT),
                    'id_offer_favorite' => filter_input(INPUT_POST, 'id_offer_favorite', FILTER_VALIDATE_INT)
                ];

                if ($controller->addFavorite($data)) {
                    sendJsonResponse(201, null, 'Favorito agregado correctamente.');
                } else {
                    sendJsonResponse(500, null, 'Error al añadir la oferta a favoritos.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
            break;

            // Manejar peticiones de tipo PUT para actualizar un favorito
        case 'PUT':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if ($controller->updateFavorite($data)) {
                    sendJsonResponse(200, $data, 'El favorito ha sido actualizado.');
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar el favorito.');
                }
            } else {
                sendJsonResponse(400, null, 'Datos inválidos.');
            }
            break;

            // Manejar peticiones de tipo DELETE para eliminar un favorito
        case 'DELETE':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
            if ($id && $controller->deleteFavorite($id)) {
                sendJsonResponse(200, null, 'Favorito eliminado correctamente.');
            } else {
                sendJsonResponse(404, null, 'Error al eliminar la oferta de favoritos.');
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

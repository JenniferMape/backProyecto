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
                $id = (int) $routesArray[1];
                // Verificar si se solicitó con detalles completos
                $details = isset($_GET['details']) && $_GET['details'] === 'true';
        
                if ($details) {
                    // Obtener detalles completos de las ofertas favoritas
                    $favorites = $controller->getFavoritesWithDetailsByUser($id);
                } else {
                    // Obtener solo los IDs de las ofertas favoritas
                    $favorites = $controller->getFavoritesByUser($id);
                }
        
                if ($favorites) {
                    sendJsonResponse(200, $favorites);
                } else {
                    sendJsonResponse(404, null, 'No se encontraron favoritos.');
                }
            } else {
                $allFavorites = $controller->getAllFavorites();
                if ($allFavorites) {
                    sendJsonResponse(200, $allFavorites);
                } else {
                    sendJsonResponse(404, null, 'No se encontraron favoritos.');
                }
            }
            break;

            // Manejar peticiones de tipo POST para crear un nuevo favorito  o eliminar uno existe
            case 'POST':
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $json_data = file_get_contents('php://input');
                    $data = json_decode($json_data, true);
            
                    // Validar JSON y datos
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        sendJsonResponse(400, null, 'Formato JSON inválido.');
                        return;
                    }
            
                    if (empty($data['id_user_favorite']) || empty($data['id_offer_favorite'])) {
                        sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                        return;
                    }
            
                    $id_user_favorite = filter_var($data['id_user_favorite'], FILTER_VALIDATE_INT);
                    $id_offer_favorite = filter_var($data['id_offer_favorite'], FILTER_VALIDATE_INT);
            
                    if ($id_user_favorite === false || $id_offer_favorite === false) {
                        sendJsonResponse(400, null, 'Los datos deben ser enteros válidos.');
                        return;
                    }
            
                    $favoriteData = [
                        'id_user_favorite' => $id_user_favorite,
                        'id_offer_favorite' => $id_offer_favorite,
                    ];
            
                    // Usar el método toggleFavorite
                    $result = $controller->toggleFavorite($favoriteData);
                    if ($result) {
                        if ($result['action'] === 'added') {
                            sendJsonResponse(201, $result['favorite'], 'Favorito agregado correctamente.');
                        } else {
                            sendJsonResponse(200, null, 'Favorito eliminado correctamente.');
                        }
                    } else {
                        sendJsonResponse(500, null, 'Error al procesar la acción de favoritos.');
                    }
                } else {
                    sendJsonResponse(405, null, 'Método no permitido.');
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

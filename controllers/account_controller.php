<?php
include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');


$method = new HTTPMethod();
$methodR = $method->getMethod();


//  Verificar si la primera parte es 'account'
if ($routesArray[0] == 'account') {
    $account = new Account();
    // Verificar si la segunda parte es un número que se corresponde con un id de usuario
    if (empty($routesArray[1]) || is_numeric($routesArray[1])) {
        // Manejar peticiones a /account
        switch ($methodR['method']) {
                // Manejar peticiones de tipo GET para obtener a un usuario en específico
            case 'GET':
                // Obtener el id del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if ($id <= 0) {
                    sendJsonResponse(400, null, 'ID no válido');
                } else {
                    $usuario = $account->getAccount($id);
                    if ($usuario) {

                        sendJsonResponse(200, $usuario);
                    } else {
                        sendJsonResponse(404, null, 'Usuario no encontrado');
                    }
                }
                break;


                // Manejar peticiones de tipo PUT para actualizar el usuario
            case 'PUT':
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($account->updateAccount($data)) {
                        sendJsonResponse(200, $data, 'Datos actualizados correctamente.');
                    } else {
                        sendJsonResponse(500, null, 'Error al actualizar la información del usuario');
                    }
                } else {
                    sendJsonResponse(400, null, 'datos inválidos');
                }
                break;

            case 'DELETE':
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if ($account->deleteAccount($id)) {
                    sendJsonResponse(200, null, 'Usuario eliminado correctamente.');
                } else {
                    sendJsonResponse(404, null, 'Error al eliminar al usuario');
                }
            
                break;

                // Manejar peticiones que no se ajusten a los anteriores métodos
            default:
                sendJsonResponse(405, null, 'Metodo no permitido');
                break;
        }
    } else if ($routesArray[1] == 'avatar') {

        // Manejar peticiones a /account/avatar

        switch ($methodR['method']) {
            case 'GET':
                // Obtener el id del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);

                if ($id <= 0) {
                    sendJsonResponse(400, null, 'ID no válido');
                } else {
                    // Llamar a la función getAvatar y obtener la respuesta
                    $avatar = $account->getAvatar($id);
                    $avatarUrl = stripslashes($avatar);

                    if ($avatarUrl) {
                        sendJsonResponse(200, $avatarUrl);
                    } else {
                        sendJsonResponse(404, null, 'Usuario no encontrado');
                    }
                }
                break;


            case 'POST':
                // Obtener el ID del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                // Verificar que el ID es válido y que hay un archivo subido sin errores
                if ($id && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['avatar'];

                    // Intentar actualizar el avatar
                    $result = $account->updateAvatar($id, $file);

                    if ($result === true) {
                        // Si la actualización fue exitosa, obtener el nuevo avatar
                        $avatar = $account->getAvatar($id);
                        sendJsonResponse(200, $avatar, 'Avatar actualizado con éxito.');
                    } else {
                        // Si hay un error, devolverlo
                        sendJsonResponse(400, null, $result);
                    }
                } else {
                    // Devolver un error si el ID no es válido o no hay archivo
                    sendJsonResponse(400, null, 'Datos inválidos o archivo no proporcionado.');
                }
                break;



            case 'DELETE':
                // Obtener el id del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                if ($id) {
                    $resultado = $account->deleteAvatar($id);

                    if (isset($resultado['success'])) {
                        sendJsonResponse(200, null, $resultado['success']);
                    } else {
                        sendJsonResponse(400, null, $resultado['error']);
                    }
                } else {
                    sendJsonResponse(400, null, 'ID de usuario no válido.');
                }
                break;



            default:
                sendJsonResponse(405, null, 'Método no permitido');
                break;
        }
    } else {
        sendJsonResponse(404, null, 'Recurso no encontrado');
    }
} else {
    sendJsonResponse(404, null, 'Recurso no encontrado');
}

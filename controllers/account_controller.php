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
                    sendJsonResponse(400, null, 'Invalid ID');
                } else {
                    $usuario = $account->getAccount($id);
                    if ($usuario) {

                        sendJsonResponse(200, $usuario);
                    } else {
                        sendJsonResponse(404, null, 'User not found');
                    }
                }
                break;


                // Manejar peticiones de tipo PUT para actualizar el usuario
            case 'PUT':
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($account->updateAccount($data)) {
                        sendJsonResponse(200, $data, 'User information updated successfully.');
                    } else {
                        sendJsonResponse(500, null, 'Failed to update user information.');
                    }
                } else {
                    sendJsonResponse(400, null, 'Invalid data');
                }
                break;

            case 'DELETE':
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if ($account->deleteAccount($id)) {
                    sendJsonResponse(200, null, 'User deleted successfully.');
                } else {
                    sendJsonResponse(404, null, 'User not found');
                }
                break;

                // Manejar peticiones que no se ajusten a los anteriores métodos
            default:
                sendJsonResponse(405, null, 'Method Not Allowed');
                break;
        }
    } else if ($routesArray[1] == 'avatar') {
        // print_r($_GET);

        // Manejar peticiones a /account/avatar

        switch ($methodR['method']) {
            case 'GET':
                // Obtener el id del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);

                if ($id <= 0) {
                    sendJsonResponse(400, null, 'Invalid ID');
                } else {
                    // Llamar a la función getAvatar y obtener la respuesta
                    $avatar = $account->getAvatar($id);
                    $avatarUrl = stripslashes($avatar);

                    if ($avatarUrl) {
                        sendJsonResponse(200, $avatarUrl);
                    } else {
                        sendJsonResponse(404, null, 'User not found');
                    }
                }
                break;



            case 'POST':
                // Obtener el id del usuario de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                if ($id && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['avatar'];

                    // Validar el tamaño del archivo (ejemplo: 2MB máximo)
                    $maxFileSize = 2 * 1024 * 1024; // 2 MB
                    if ($file['size'] > $maxFileSize) {
                        sendJsonResponse(400, null, 'File size exceeds the 2MB limit.');
                        break;
                    }

                    // Validar el tipo de archivo (solo imágenes)
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $mimeType = mime_content_type($file['tmp_name']);
                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        sendJsonResponse(400, null, 'Invalid file type. Only JPG, PNG, and GIF are allowed.');
                        break;
                    }

                    // Definir la ruta de almacenamiento
                    $uploadDir = __DIR__ . '/uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true); // Crear el directorio si no existe
                    }

                    // Generar el nuevo nombre del archivo (basado en el ID del usuario)
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $avatarFileName = $id . '.' . $extension;
                    $uploadPath = $uploadDir . $avatarFileName;

                    // Mover el archivo subido a la carpeta de destino
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Actualizar el avatar del usuario en la base de datos
                        if ($account->updateAvatar($id, $avatarFileName)) {
                            $avatar = $account->getAvatar($id);
                            sendJsonResponse(200, $avatar, 'Avatar updated successfully.');
                        } else {
                            sendJsonResponse(500, null, 'Failed to update avatar in the database.');
                        }
                    } else {
                        sendJsonResponse(500, null, 'Failed to upload avatar file.');
                    }
                } else {
                    sendJsonResponse(400, null, 'Invalid data or file not provided.');
                }
                break;

            case 'DELETE':
                // Obtener el id del avatar de la URL
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                if ($id) {
                    // Obtener la URL del avatar actual antes de eliminarlo
                    $avatar = $account->getAvatar($id);

                    if ($account->updateAvatar($id, null)) {
                        // Eliminar el archivo del servidor
                        if ($avatar && file_exists($avatar['avatar_url'])) {
                            unlink($avatar['avatar_url']);
                        }
                        sendJsonResponse(200, null, 'Avatar deleted successfully.');
                    } else {
                        sendJsonResponse(500, null, 'Failed to delete avatar in the database.');
                    }
                } else {
                    sendJsonResponse(400, null, 'Invalid ID');
                }
                break;


            default:
                sendJsonResponse(405, null, 'Method Not Allowed');
                break;
        }
    } else {
        sendJsonResponse(404, null, 'Resource not found');
    }
} else {
    sendJsonResponse(404, null, 'Resource not found');
}

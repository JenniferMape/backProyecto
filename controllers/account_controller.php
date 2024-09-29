<?php
include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');


$method = new HTTPMethod();
$methodR = $method->getMethod();
// echo '<pre>';
// print_r($routesArray);
// echo '</pre>';

//  Verificar si la primera parte es 'account'
if ($routesArray[0] == 'account') {
    $account = new Account();
    // Verificar si la segunda parte es un número que se corresponde con un id de usuario
    if (empty($routesArray[1])||is_numeric($routesArray[1])) {
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


            // Manejar peticiones de tipo DELETE para eliminar el usuario
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
         print_r($_GET);
        
        // Manejar peticiones a /account/avatar
   
        switch ($methodR['method']) {
            case 'GET':
               
                // Obtener el id del avatar de la URL'
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                //echo $id;
                if ($id <= 0) {
                    sendJsonResponse(400, null, 'Invalid ID');
                } else {
                   $avatar=$account->getAvatar($id);
                    if ($avatar) {
                        sendJsonResponse(200, $avatar);
                    } else {
                        sendJsonResponse(404, null, 'User not found');
                    }
                }
                break;
            
            case 'POST':
                // Obtener el id del avatar de la URL'
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if(empty($_POST['avatar_user']) || $id <= 0) {
                    sendJsonResponse(400, null, 'Invalid data');
                }else{
                    $avatar_user = $_POST['avatar_user'];
                    if($account->updateAvatar($id, $avatar_user)){
                        sendJsonResponse(200, $avatar_user, 'Avatar updated successfully.');
                    }else{
                        sendJsonResponse(500, null, 'Failed to update avatar.');
                    }
                }
                break;

            case 'PUT':
                // Obtener el id del avatar de la URL'
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                // Obtener el avatar del cuerpo de la solicitud
                $json_data = file_get_contents('php://input');
                // Decodificar el avatar JSON en un array asociativo
                $data = json_decode($json_data, true);
                $avatar_user = $data['avatar_user'];

                // Validar los datos
                if( $id <= 0 || empty($avatar_user)) {
                    sendJsonResponse(400, null, 'Invalid data');
                }else{
                    if($account->updateAvatar($id, $avatar_user)){
                        sendJsonResponse(200, $avatar_user, 'Avatar updated successfully.');
                    }else{
                        sendJsonResponse(500, null, 'Failed to update avatar.');
                    }
                }
                break;

            case 'DELETE':
                // Obtener el id del avatar de la URL'
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                
                if( $id <= 0) {
                    sendJsonResponse(400, null, 'Invalid data');
                }else{
                    if($account->updateAvatar($id,  null, true)){
                        sendJsonResponse(200, null, 'Avatar deleted successfully.');
                    }else{
                        sendJsonResponse(500, null, 'Failed to delete avatar.');
                    }
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
?>

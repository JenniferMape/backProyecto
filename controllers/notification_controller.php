<?php
include('./helpers/HTTPMethod.php');
include('./models/notification.php');
include('./helpers/response.php');
include('./helpers/filterUrl.php');

$method = new HTTPMethod();
$methodR = $method->getMethod();
 $controller = new Notification();
// echo '<pre>';
// print_r($routesArray);
// echo '</pre>';

//  Verificar si la primera parte es 'notification'
if ($routesArray[0] == 'notification') {
   
    // Verificar si la segunda parte es un número que se corresponde con un id de usuario
    if (empty($routesArray[1])||is_numeric($routesArray[1])) {
        // Manejar peticiones a /notification
        switch ($methodR['method']) {
            
            case 'GET':
                
            // Obtener y parsear los parámetros de la URL
            $filterString = isset($_GET['filter']) ? $_GET['filter'] : null;
            $filters = $filterString ? parseFilter($filterString) : [];

            // Construir la consulta
            $where = array();

            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'type':
                        //Si la clave no existe o su valor es null, entonces el operador ?? asigna el valor a esa clave.
                        $where['type_notification']??=$value;
                       break;
                    case 'idUser':
                        $where['id_user_notification']??=$value;
                        break;
                    case 'idComment':
                        $where['id_comment_notification']??= $value;
                        break;
                    case 'idPost':
                        $where['id_post_notification']??= $value;
                        break;
                    case 'idOffer':
                        $where['id_offer_notification']??= $value;
                        break;
                    case 'read':
                        $where['is_read_notification']??= $value;
                        break;
                    case 'category':
                        $where['id_category_notification']??=$value;
                        break;
                    default:
                        break;
                }
            }

           
            //echo '<pre>'; print_r( $where); echo '</pre>';
            $notification = $controller->getFilterNotification($where);
            sendJsonResponse(200, $notification, 'OK');
            break;

          
            case 'POST':
                if(empty($_POST['type_notification'])){
                    sendJsonResponse(400,null, 'Datos incompletos');
                }     
                $data=array(                  
                    'id_category_notification'=>$_POST['id_category_notification']?? null,
                    'id_offer_notification'=>$_POST['id_offer_notification'],
                    'id_user_notification'=>$_POST['id_user_notification'],
                    'id_comment_notification'=>$_POST['id_comment_notification'],
                    'type_notification'=>$_POST['type_notification'],
                    'is_read_notification'=>$_POST['is_read_notification']?? 0
                );
                $notification = $controller->createNotification($data);
                sendJsonResponse(200, $notification, 'OK');
                
            break;
            // Manejar peticiones de tipo PUT para actualizar el usuario
            case 'PUT':
                    // Obtener el id de la categoría de la URL
                    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                    if ($id <= 0) {
                        sendJsonResponse(400, null, 'ID no válido');
                    } else {
                         $isRead= $controller->updateNotification($id);
                       
                           if($isRead){
                                sendJsonResponse(200, 'Notificación no leida');
                            }else{
                                sendJsonResponse(200, 'Notificación leida');
                            }
                    }
            break;


            // Manejar peticiones de tipo DELETE para eliminar el usuario
            case 'DELETE':
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if ($id <= 0) {
                    sendJsonResponse(400, null, 'ID no válido');
                } else {
                    if ($controller->deleteNotification($id)) {
                        sendJsonResponse(200, 'Notificación eliminada con éxito');
                    } else {
                        sendJsonResponse(404, null,'Notificación no encontrada');
                    }
                }
            break;

            // Manejar peticiones que no se ajusten a los anteriores métodos
            default:
                sendJsonResponse(405,  null,'Method Not Allowed');
            break;
        }
    
    } else {
        sendJsonResponse(404, null,'Resource not found');
    }
} else {
    sendJsonResponse(404,null, 'Resource not found');
}
?>

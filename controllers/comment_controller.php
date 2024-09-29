<?php
include('./helpers/HTTPMethod.php');
include('./models/comment.php');
include('./helpers/response.php');
include('./helpers/filterUrl.php');

$method = new HTTPMethod();
$methodR = $method->getMethod();
$controller=new Comment();

if ($routesArray[0] == 'comment') {
    switch ($methodR['method']) {
        // Manejar peticiones de tipo GET 
        case 'GET':
            if(!isset($_GET['filter']) && empty($_GET['filter'])){
                $allComments=$controller->getAllComments();
                sendJsonResponse(200, $allComments, 'Listado de comentarios');
                return;
            }else{
            // Obtener y parsear los parámetros de la URL
                $filterString = $_GET['filter'];
                $filters = $filterString ? parseFilter($filterString) : [];

                if(isset($filters['id'])){
                    $id=$filters['id'];
                    if ($id <= 0) {
                        sendJsonResponse(400, null, 'ID no válido');
                    } else {
                        $commentById = $controller->getCommentById($id);
                        if ($commentById) {
                            sendJsonResponse(200, $commentById);
                        } else {
                            sendJsonResponse(404, null, 'Comentario no encontrado');
                        }
                    }
                }
                if(isset($filters['idUser'])){
                    $id_user=$filters['idUser'];
                    if ($id_user <= 0) {
                        sendJsonResponse(400, null, 'ID usuario no válido');
                    } else {
                        $commentsByUser = $controller->getCommentsByUser($id_user);
                        if ($commentsByUser) {
                            sendJsonResponse(200, $commentsByUser);
                        } else {
                            sendJsonResponse(404, null, 'Comentarios no encontrados');
                        }
                    }
                }
                if(isset($filters['idOffer'])){
                    $id_offer=$filters['idOffer'];
                    if ($id_offer <= 0) {
                        sendJsonResponse(400, null, 'ID oferta no válido');
                    } else {
                        $commentsByOffer = $controller->getCommentsByOffer($id_offer);
                        if ($commentsByOffer) {
                            sendJsonResponse(200, $commentsByOffer);
                        } else {
                            sendJsonResponse(404, null, 'Comentarios no encontrados');
                        }
                    }
                }
           }
        break;

        // Manejar peticiones de tipo POST para crear un nuevo comentario
        case 'POST':
            if(empty($_POST['id_user_comment'])||empty($_POST['id_offer_comment'])||empty($_POST['message_comment'])){
                sendJsonResponse(400, null, 'Datos incompletos');
            }
           
            $data=array(
                'id_user_comment'=>$_POST['id_user_comment'],
                'id_offer_comment'=>$_POST['id_offer_comment'],
                'message_comment'=>$_POST['message_comment'],
                'id_response_comment'=>$_POST['id_response_comment'] ?? null
            );
            $isCreated = $controller->addComment($data);

            if ($isCreated) {
                sendJsonResponse(201,'Comentario creado exitosamente.');
            } else {
                sendJsonResponse(500, null, 'Error al crear la comentario.');
            }
        break;

        // Manejar peticiones de tipo PUT para actualizar un comentario
        case 'PUT':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($controller->updateComment($data)) {
                    sendJsonResponse(200, $data, 'Comentario actualizada con éxito');
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar el comentario');
                }
            } else {
                sendJsonResponse(400, null, 'Datos no válidos');
            }
           
        break;

        // Manejar peticiones de tipo DELETE para eliminar un comentario
        case 'DELETE':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE)  {
              
                if ($controller->deleteComment($data)) {
                        sendJsonResponse(200,'Comentario eliminado con exito.');
                    } else {
                        sendJsonResponse(404, null, 'Comentario no encontrado o no es de ese autor');
                    }
             }else{
                sendJsonResponse(400, null, 'ID comentario o ID usuario no válido');
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
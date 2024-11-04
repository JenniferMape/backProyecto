<?php
include('./helpers/HTTPMethod.php');
include('./models/category.php');
include('./helpers/response.php');


$method = new HTTPMethod();
$methodR = $method->getMethod();
$controller = new Category();

//Verificar si la primera parte es 'category'
if ($routesArray[0] == 'category') {
        // Manejar peticiones a /category
        switch ($methodR['method']) {
            // Manejar peticiones de tipo GET 
            case 'GET':
                //Petición get que está acompañada por un id
                if (!empty($routesArray[1])&&is_numeric($routesArray[1])) {

                    // Obtener el id de la categoría de la URL
                    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                    if ($id <= 0) {
                        sendJsonResponse(400, null, 'ID no válido');
                    } else {
                        $categoryById = $controller->getCategory($id);
                        if ($categoryById) {
                            sendJsonResponse(200, $categoryById);
                        } else {
                            sendJsonResponse(404, null, 'Categoría no encontrada');
                        }
                    }
                //Petición get que devuelve todas las categorias, no va acompañada por un id
                } else {
                    $allCategories = $controller->getAllCategories();

                    // Validación y respuesta
                    if (!empty($allCategories)) {
                        sendJsonResponse(200, $allCategories);
                    } else {
                        sendJsonResponse(404, null, 'No hay categorías disponibles');
                    }
                }
                break;

            // Manejar peticiones de tipo POST para crear una nueva categoría
            case 'POST':
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Comprobación de que los campos obligatorios estén completados
                    if (empty($_POST['name_category']) || empty($_POST['description_category'])) {
                        sendJsonResponse(400, null,'Todos los campos obligatorios deben ser completados.');
                        return;
                    };
                    $data=array(
                        'name_category' => $_POST['name_category'],
                        'description_category' => $_POST['description_category']
                    );
                
                    $isCreated = $controller->addCategory($data);

                    if ($isCreated) {
                        sendJsonResponse(201,'Categoría creada exitosamente.');
                    } else {
                        sendJsonResponse(500, null, 'Error al crear la categoría.');
                    }
                } else {
                    sendJsonResponse(405, null, 'Método no permitido.');
                }


                break;
            // Manejar peticiones de tipo PUT para actualizar el usuario
            case 'PUT':
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($controller->updateCategory($data)) {
                        sendJsonResponse(200, $data, 'Información de la categoría actualizada con éxito');
                    } else {
                        sendJsonResponse(500, null, 'Error al actualizar la información de la categoría');
                    }
                } else {
                    sendJsonResponse(400, null, 'Datos no válidos');
                }
                break;


            // Manejar peticiones de tipo DELETE para eliminar el usuario
            case 'DELETE':
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
                if ($controller->deleteCategory($id)) {
                    sendJsonResponse(200, null, 'Categoría eliminada con éxito');
                } else {
                    sendJsonResponse(404, null, 'Categoría no encontrada');
                }
                break;

            // Manejar peticiones que no se ajusten a los anteriores métodos
            default:
                sendJsonResponse(405, null, 'Método no permitido');
            break;
        }
    
} else {
    sendJsonResponse(404, null, 'Recurso no encontrado');
}
?>

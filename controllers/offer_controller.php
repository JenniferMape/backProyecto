<?php
include('./helpers/HTTPMethod.php');
include('./models/offer.php');
include('./helpers/response.php');
include('./helpers/filterUrl.php');

$method = new HTTPMethod();
$methodR = $method->getMethod();
$controller = new Offer();


if ($routesArray[0] == 'offer') {
    switch ($methodR['method']) {
            // Manejar peticiones de tipo GET 
        case 'GET':
            if (!isset($_GET['filter']) && empty($_GET['filter'])) {
                $allOffers = $controller->getAllOffers();
                sendJsonResponse(200, $allOffers, 'Listado de todas las ofertas');
                return;
            } else {
                // Obtener y parsear los parámetros de la URL
                $filterString = $_GET['filter'];
                $filters = $filterString ? parseFilter($filterString) : [];

                if (isset($filters['id'])) {
                    $id = $filters['id'];
                    if ($id <= 0) {
                        sendJsonResponse(400, null, 'ID no válido');
                    } else {
                        $offerById = $controller->getOfferById($id);
                        if ($offerById) {
                            sendJsonResponse(200, $offerById);
                        } else {
                            sendJsonResponse(404, null, 'Oferta no encontrada');
                        }
                    }
                }
                if (isset($filters['title'])) {
                    $title = $filters['title'];
                    //echo '<pre>'; print_r( $title); echo '</pre>';
                    $offersByTitle = $controller->getOffersByTitle($title);
                    if ($offersByTitle) {
                        sendJsonResponse(200, $offersByTitle);
                    } else {
                        sendJsonResponse(404, null, 'No se encontraron ofertas con ese título');
                    }
                }
                if (isset($filters['category'])) {
                    $category = $filters['category'];

                    $offersByCategory = $controller->getOffersByCategory($category);
                    if ($offersByCategory) {
                        sendJsonResponse(200, $offersByCategory);
                    } else {
                        sendJsonResponse(404, null, 'No se encontraron ofertas de esa categoría');
                    }
                }
                if (isset($filters['company'])) {
                    $id_company_offer = $filters['company'];

                    $offersByCompany = $controller->getOffersByCompany($id_company_offer);
                    if ($offersByCompany) {
                        sendJsonResponse(200, $offersByCompany);
                    } else {
                        sendJsonResponse(404, null, 'No se encontraron ofertas de esa empresa');
                    }
                }
                if (isset($filters['minPrice']) & isset($filters['maxPrice'])) {
                    $minPrice = $filters['minPrice'];
                    $maxPrice = $filters['maxPrice'];
                    $offersByPrice = $controller->findOffersByPriceRange($minPrice, $maxPrice);
                    if ($offersByPrice) {
                        sendJsonResponse(200, $offersByPrice);
                    } else {
                        sendJsonResponse(404, null, 'No se encontraron ofertas con ese rango de precios');
                    }
                }
            }

            // Manejar peticiones de tipo POST para crear una nueva categoría
        case 'POST':
            // Comprobación de que los campos obligatorios estén completados
            if (
                empty($_POST['id_company_offer']) ||
                empty($_POST['id_category_offer']) ||
                empty($_POST['title_offer']) ||
                empty($_POST['description_offer']) ||
                empty($_POST['price_offer']) ||
                empty($_POST['start_date_offer']) ||
                empty($_POST['end_date_offer'])
            ) {
                sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                return;
            }

            // Comprobar si hay un archivo de imagen subido
            $image_offer = null;
            if (isset($_FILES['image_offer']) && $_FILES['image_offer']['error'] === UPLOAD_ERR_OK) {
                $image_offer = $_FILES['image_offer'];
            } else {
                sendJsonResponse(400, null, 'Error al cargar la imagen.'); // Si la imagen no se subió correctamente
                return;
            }

            // Obtener otros datos y asignar null si no existen
            $discount_code_offer = 
            $web_offer = $_POST['web_offer'] ?? null;
            $address_offer = $_POST['address_offer'] ?? null;

            $data = array(
                'id_company_offer' => $_POST['id_company_offer'],
                'id_category_offer' => $_POST['id_category_offer'],
                'title_offer' => $_POST['title_offer'],
                'description_offer' => $_POST['description_offer'],
                'price_offer' => $_POST['price_offer'],
                'start_date_offer' => $_POST['start_date_offer'],
                'end_date_offer' => $_POST['end_date_offer'],
                'discount_code_offer' => $_POST['discount_code_offer'] ?? null,
                'web_offer' => $_POST['web_offer'] ?? null,
                'address_offer' =>$_POST['address_offer'] ?? null
            );

            try {
                // Añadir la oferta con la imagen
                $offer = $controller->addOffer($data, $image_offer);

                // Si la oferta se creó correctamente
                sendJsonResponse(201, 'Oferta creada exitosamente.', $offer);
            } catch (RuntimeException $e) {
                // Captura errores específicos de subida de imagen o creación de oferta
                sendJsonResponse(500, null, 'Error al crear la oferta: ' . $e->getMessage());
            } catch (Exception $e) {
                // Captura cualquier otro error
                sendJsonResponse(500, null, 'Error inesperado: ' . $e->getMessage());
            }
            break;


        case 'PUT':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($controller->updateOffer($data)) {
                    sendJsonResponse(200, $data, 'Información de la oferta actualizada con éxito');
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar la información de la la oferta');
                }
            } else {
                sendJsonResponse(400, null, 'Datos no válidos');
            }
            break;


            // Manejar peticiones de tipo DELETE para eliminar el usuario
        case 'DELETE':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
            if ($controller->deleteOffer($id)) {
                sendJsonResponse(200, 'Oferta eliminada con exito.');
            } else {
                sendJsonResponse(404, null, 'Oferta no encontrada');
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

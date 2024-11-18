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
                        sendJsonResponse(400, NULL, 'ID no válido');
                    } else {
                        $offerById = $controller->getOfferById($id);
                        if ($offerById) {
                            sendJsonResponse(200, $offerById);
                        } else {
                            sendJsonResponse(404, NULL, 'Oferta no encontrada');
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
                        sendJsonResponse(404, NULL, 'No se encontraron ofertas con ese título');
                    }
                }
                if (isset($filters['category'])) {
                    $category = $filters['category'];

                    $offersByCategory = $controller->getOffersByCategory($category);
                    if ($offersByCategory) {
                        sendJsonResponse(200, $offersByCategory);
                    } else {
                        sendJsonResponse(404, NULL, 'No se encontraron ofertas de esa categoría');
                    }
                }
                if (isset($filters['company'])) {
                    $id_company_offer = $filters['company'];

                    $offersByCompany = $controller->getOffersByCompany($id_company_offer);
                    if ($offersByCompany) {
                        sendJsonResponse(200, $offersByCompany);
                    } else {
                        sendJsonResponse(404, NULL, 'No se encontraron ofertas de esa empresa');
                    }
                }
                if (isset($filters['minPrice']) & isset($filters['maxPrice'])) {
                    $minPrice = $filters['minPrice'];
                    $maxPrice = $filters['maxPrice'];
                    $offersByPrice = $controller->findOffersByPriceRange($minPrice, $maxPrice);
                    if ($offersByPrice) {
                        sendJsonResponse(200, $offersByPrice);
                    } else {
                        sendJsonResponse(404, NULL, 'No se encontraron ofertas con ese rango de precios');
                    }
                }
            }
            break;

        case 'POST':
            // Comprobación de si es creación o edición
            $isCreating = empty($_POST['id']);

            // Verificar campos obligatorios solo si es creación
            if ($isCreating && (
                empty($_POST['id_company_offer']) ||
                empty($_POST['id_category_offer']) ||
                empty($_POST['title_offer']) ||
                empty($_POST['description_offer']) ||
                empty($_POST['new_price_offer']) ||
                empty($_POST['original_price_offer']) ||
                empty($_POST['start_date_offer']) ||
                empty($_POST['end_date_offer'])
            )) {
                sendJsonResponse(400, NULL, 'Todos los campos obligatorios deben ser completados.');
                return;
            }

            // Comprobar si hay un archivo de imagen subido (opcional)
            $image_offer = NULL;
            if (isset($_FILES['image_offer']) && $_FILES['image_offer']['error'] === UPLOAD_ERR_OK) {
                $image_offer = $_FILES['image_offer'];
            }

            // Obtener los datos de la oferta, excluyendo valores null
            $data = array_filter([
                'id' => $_POST['id'] ?? NULL,
                'id_company_offer' => $_POST['id_company_offer'] ?? NULL,
                'id_category_offer' => $_POST['id_category_offer'] ?? NULL,
                'title_offer' => $_POST['title_offer'] ?? NULL,
                'description_offer' => $_POST['description_offer'] ?? NULL,
                'new_price_offer' => $_POST['new_price_offer'] ?? NULL,
                'original_price_offer' => $_POST['original_price_offer'] ?? NULL,
                'start_date_offer' => $_POST['start_date_offer'] ?? NULL,
                'end_date_offer' => $_POST['end_date_offer'] ?? NULL,
                'discount_code_offer' => $_POST['discount_code_offer'] ?? NULL,
                'web_offer' => $_POST['web_offer'] ?? NULL,
                'address_offer' => $_POST['address_offer'] ?? NULL
            ], function ($value) {
                return $value !== null;
            });

            try {
                if (!$isCreating) {
                    // Actualizar oferta si tiene ID
                    $offer = $controller->updateOffer($data, $image_offer);

                    if ($offer) {
                        sendJsonResponse(200, $offer, 'Información de la oferta actualizada con éxito');
                    } else {
                        sendJsonResponse(404, NULL, 'Oferta no encontrada.');
                    }
                } else {
                    // Crear nueva oferta si no tiene ID
                    $offer = $controller->addOffer($data, $image_offer);
                    sendJsonResponse(201, $offer,'Oferta creada exitosamente.');
                }
            } catch (RuntimeException $e) {
                sendJsonResponse(500, NULL, 'Error al procesar la oferta: ' . $e->getMessage());
            } catch (Exception $e) {
                sendJsonResponse(500, NULL, 'Error inesperado: ' . $e->getMessage());
            }
            break;

            // Manejar peticiones de tipo DELETE para eliminar el usuario
        case 'DELETE':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1]]);
            if ($controller->deleteOffer($id)) {
                sendJsonResponse(200,NULL, 'Oferta eliminada con exito.');
            } else {
                sendJsonResponse(404, NULL, 'Oferta no encontrada');
            }
            break;

            // Manejar peticiones que no se ajusten a los anteriores métodos
        default:
            sendJsonResponse(405, NULL, 'Método no permitido');
            break;
    }
}

<?php


class Offer
{

    public function getAllOffers()
    {
        $offers = ORM::for_table('offers')
            ->join('categories', ['offers.id_category_offer', '=', 'categories.id']) // Join con categorías
            ->join('users', ['offers.id_company_offer', '=', 'users.id'])           // Join con compañías (usuarios)
            ->select('offers.*')                 // Selecciona todos los campos de la tabla ofertas
            ->select('categories.name_category', 'category_name') // Nombre de la categoría
            ->select('users.name_user', 'company_name')           // Nombre de la compañía
            ->order_by_desc('offers.created_offer') // Ordena por fecha de creación
            ->find_many();
        return $this->convertCollection($offers);
    }

    public function getOfferById($id)
    {
        $offer = ORM::for_table('offers')
            ->join('categories', ['offers.id_category_offer', '=', 'categories.id']) // Join con categorías
            ->join('users', ['offers.id_company_offer', '=', 'users.id'])           // Join con compañías (usuarios)
            ->select('offers.*')                  // Selecciona todos los campos de la tabla ofertas
            ->select('categories.name_category', 'category_name') // Nombre de la categoría
            ->select('users.name_user', 'company_name')           // Nombre de la compañía
            ->find_one($id); // Encuentra la oferta por su ID
        return $offer ? $this->convertObj($offer) : null;
    }

    public function getOffersByTitle($title)
    {
        $title = trim($title);
        $titleFormat = '%' . $title . '%';

        // Log para depurar
        error_log("Buscando ofertas con el título: " . $titleFormat);

        $offers = ORM::for_table('offers')
            ->where_like('title_offer', $titleFormat)
            ->find_many();

        if (!$offers) {
            error_log("No se encontraron ofertas para el título: " . $titleFormat);
            return null;
        }

        return $this->convertCollection($offers);
    }

    public function getOffersByCategory($id_category_offer)
    {
        $offers = ORM::for_table('offers')
            ->join('categories', ['offers.id_category_offer', '=', 'categories.id']) // Join con categorías
            ->join('users', ['offers.id_company_offer', '=', 'users.id'])           // Join con compañías (usuarios)
            ->select('offers.*')                  // Selecciona todos los campos de la tabla ofertas
            ->select('categories.name_category', 'category_name') // Nombre de la categoría
            ->select('users.name_user', 'company_name')           // Nombre de la compañía
            ->where('offers.id_category_offer', $id_category_offer) // Filtra por la categoría
            ->order_by_desc('offers.created_offer') // Ordena por fecha de creación
            ->find_many();

        return $this->convertCollection($offers);
    }

    public function getOffersByCompany($id_company_offer)
    {
        $offers = ORM::for_table('offers')->where('id_company_offer', $id_company_offer)->find_many();
        return $this->convertCollection($offers);
    }

    public function findOffersByPriceRange($minPrice, $maxPrice, $orderBy = 'new_price_offer', $orderDirection = 'asc')
    {
        // Valida que la dirección es válida 
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            throw new InvalidArgumentException("Invalid order direction: $orderDirection");
        }

        // Valida el nombre de la columna de ordenamiento 
        $validOrderColumns = [
            'id',
            'id_company_offer',
            'id_category_offer',
            'title_offer',
            'new_price_offer',
            'original_price_offer',
            'description_offer',
            'start_date_offer',
            'end_date_offer',
            'discount_code_offer',
            'image_offer',
            'web_offer',
            'address_offer',
            'created_offer',
            'updated_offer'
        ];
        if (!in_array($orderBy, $validOrderColumns)) {
            throw new InvalidArgumentException("Invalid order by column: $orderBy");
        }

        // Construye la consulta SQL
        $query = ORM::for_table('offers')
            ->where_gte('new_price_offer', $minPrice)
            ->where_lte('new_price_offer', $maxPrice);

        // Aplica la ordenación dependiendo de la dirección
        if ($orderDirection === 'asc') {
            $query->order_by_asc($orderBy);
        } else {
            $query->order_by_desc($orderBy);
        }

        $offers = $query->find_many();
        return $this->convertCollection($offers);
    }

    public function addOffer($data, $image = null)
    {
        // Verificación básica de los datos antes de crear la oferta
        if (empty($data['id_company_offer']) || empty($data['title_offer']) || empty($data['new_price_offer']) || empty($data['original_price_offer'])) {
            throw new InvalidArgumentException("Los datos de la oferta no son válidos");
        }

        // Crear la oferta en la base de datos
        $offer = ORM::for_table('offers')->create();
        $offer->id_company_offer = $data['id_company_offer'];
        $offer->id_category_offer = $data['id_category_offer'];
        $offer->title_offer = $data['title_offer'];
        $offer->description_offer = $data['description_offer'];
        $offer->new_price_offer = $data['new_price_offer'];
        $offer->original_price_offer = $data['original_price_offer'];
        $offer->start_date_offer = $data['start_date_offer'];
        $offer->end_date_offer = $data['end_date_offer'];
        $offer->discount_code_offer = $data['discount_code_offer'];
        $offer->web_offer = $data['web_offer'];
        $offer->address_offer = $data['address_offer'];

        // Guardar la oferta básica para obtener el ID
        $offer->save();

        // Subir la imagen de la oferta usando el ID recién creado
        if ($image) {
            $uploadedFileName = $this->uploadOfferImage($offer->id, $image);
            $offer->image_offer = URL.'/uploads/offers/' . $data['id_company_offer'] . '/' . $offer->id . '/' . $uploadedFileName;
            $offer->save();
        }


        return $this->convertObj($offer); // Retorna el objeto convertido
    }


    public function updateOffer($dataOffer, $image = null)
    {
        // Buscar la oferta en la base de datos
        $offer = ORM::for_table('offers')->find_one($dataOffer['id']);

        if ($offer) {
            // Actualizar los campos de la oferta con los datos recibidos
            $offer->id_category_offer = isset($dataOffer['id_category_offer']) ? $dataOffer['id_category_offer'] : $offer->id_category_offer;
            $offer->title_offer = isset($dataOffer['title_offer']) ? $dataOffer['title_offer'] : $offer->title_offer;
            $offer->description_offer = isset($dataOffer['description_offer']) ? $dataOffer['description_offer'] : $offer->description_offer;
            $offer->new_price_offer = isset($dataOffer['new_price_offer']) ? $dataOffer['new_price_offer'] : $offer->new_price_offer;
            $offer->original_price_offer = isset($dataOffer['original_price_offer']) ? $dataOffer['original_price_offer'] : $offer->original_price_offer;
            $offer->start_date_offer = isset($dataOffer['start_date_offer']) ? $dataOffer['start_date_offer'] : $offer->start_date_offer;
            $offer->end_date_offer = isset($dataOffer['end_date_offer']) ? $dataOffer['end_date_offer'] : $offer->end_date_offer;
            $offer->discount_code_offer = isset($dataOffer['discount_code_offer']) ? $dataOffer['discount_code_offer'] : $offer->discount_code_offer;
            $offer->web_offer = isset($dataOffer['web_offer']) ? $dataOffer['web_offer'] : $offer->web_offer;
            $offer->address_offer = isset($dataOffer['address_offer']) ? $dataOffer['address_offer'] : $offer->address_offer;

            // Manejo de la imagen

            if ($image) {
                // Si ya existe una imagen para la oferta, eliminarla
                if (!empty($offer->image_offer)) {
                    $this->deleteOfferImage($offer->image_offer);
                }

                // Subir la nueva imagen y asignar la ruta a la oferta
                $uploadedFileName = $this->uploadOfferImage($offer->id, $image);
                $offer->image_offer = URL.'/uploads/offers/' . $dataOffer['id_company_offer'] . '/' . $offer->id . '/' . $uploadedFileName;
            }
            //Guardar los cambios en la base de datos
            $offer->save();

            //Retornar el objeto actualizado
            return $this->convertObj($offer);
        } else {
            //Responder con error si la oferta no existe
            return false;
        }
    }


    public function deleteOffer($id)
    {
        // Buscar la oferta en la base de datos
        $offer = ORM::for_table('offers')->find_one($id);

        if ($offer) {
            if (!empty($offer->image_offer)) {
                $this->deleteOfferImage($offer->image_offer);
            }

            $offer->delete();

            return true;
        } else {
            return false;
        }
    }

    public function deleteOfferImage($imagePath)
    {
        // Construir la ruta completa en el servidor
        $serverPath = $_SERVER['DOCUMENT_ROOT'] . parse_url($imagePath, PHP_URL_PATH);

        if (file_exists($serverPath)) {
            // Eliminar el archivo
            unlink($serverPath);

            // Obtener la carpeta donde se encontraba la imagen
            $directoryPath = dirname($serverPath);

            // Verificar si la carpeta está vacía
            if (is_dir($directoryPath) && count(array_diff(scandir($directoryPath), ['.', '..'])) === 0) {
                rmdir($directoryPath); // Eliminar la carpeta si está vacía
            }

            return true;
        } else {
            return "El archivo no existe en la ruta especificada: " . $serverPath;
        }
    }
    public function uploadOfferImage($offerId, $image = null)
    {
        // Verificar si la oferta existe
        $offer = ORM::for_table('offers')->find_one($offerId);

        if ($offer) {
            $companyId = $offer->id_company_offer;

            // Directorio base para almacenar imágenes de ofertas
            $baseUploadDir = realpath(__DIR__ . '/../uploads/offers/') . '/';

            // Crear estructura de carpetas
            $companyUploadDir = $baseUploadDir . $companyId . '/';
            $offerUploadDir = $companyUploadDir . $offerId . '/';

            if (!file_exists($companyUploadDir)) {
                mkdir($companyUploadDir, 0755, true);
            }
            if (!file_exists($offerUploadDir)) {
                mkdir($offerUploadDir, 0755, true);
            }

            if (is_array($image) && isset($image['name']) && !empty($image['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $mimeType = mime_content_type($image['tmp_name']);

                if (in_array($extension, $allowedExtensions) && in_array($mimeType, $allowedMimeTypes)) {
                    $newFileName = uniqid() . '.' . $extension;
                    $uploadPath = $offerUploadDir . $newFileName;

                    if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                        // Retorna solo el nombre del archivo
                        return $newFileName;
                    } else {
                        throw new RuntimeException('Error al mover el archivo subido.');
                    }
                } else {
                    throw new RuntimeException('Tipo de archivo no permitido. Solo se permiten archivos JPG, PNG o GIF.');
                }
            }

            throw new RuntimeException('No se ha proporcionado ninguna imagen para subir.');
        } else {
            throw new RuntimeException('Oferta no encontrada.');
        }
    }


    private function convertObj($obj)
    {
        return [
            'id' => $obj->id ?? null,
            'id_company_offer' => $obj->id_company_offer ?? null,
            'id_category_offer' => $obj->id_category_offer ?? null,
            'title_offer' => $obj->title_offer ?? null,
            'new_price_offer' => $obj->new_price_offer ?? null,
            'original_price_offer' => $obj->original_price_offer ?? null,
            'description_offer' => $obj->description_offer ?? null,
            'start_date_offer' => $obj->start_date_offer ?? null,
            'end_date_offer' => $obj->end_date_offer ?? null,
            'discount_code_offer' => $obj->discount_code_offer ?? null,
            'image_offer' => $obj->image_offer ?? null,
            'web_offer' => $obj->web_offer ?? null,
            'address_offer' => $obj->address_offer ?? null,
            'created_offer' => $obj->created_offer ?? null,
            'updated_offer' => $obj->updated_offer ?? null,
            "category_name" => $obj->category_name,
            "company_name" => $obj->company_name
        ];
    }
    private function convertCollection($collection)
    {
        $result = [];
        foreach ($collection as $item) {
            $result[] = $this->convertObj($item);
        }
        return $result;
    }
}

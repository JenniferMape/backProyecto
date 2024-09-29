<?php

class Offer {

    public function getAllOffers() {
        $offers = ORM::for_table('offers')->find_many();
        return $this->convertCollection($offers);
    }

    public function getOfferById($id) {
        $offer = ORM::for_table('offers')->find_one($id);
        return $offer ? $this->convertObj($offer) : null;
    }

    public function getOffersByTitle($title) {
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

    public function getOffersByCategory($id_category_offer) {
        $offers = ORM::for_table('offers')->where('id_category_offer', $id_category_offer)->find_many();
    
        return $this->convertCollection($offers);
    }

    public function getOffersByCompany($id_company_offer) {
        $offers = ORM::for_table('offers')->where('id_company_offer', $id_company_offer)->find_many();
        return $this->convertCollection($offers);
    }

    public function findOffersByPriceRange($minPrice, $maxPrice, $orderBy = 'price_offer', $orderDirection = 'asc') {
        // Valida que la dirección es válida 
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            throw new InvalidArgumentException("Invalid order direction: $orderDirection");
        }

        // Valida el nombre de la columna de ordenamiento 
        $validOrderColumns = [
            'id', 'id_company_offer', 'id_category_offer', 'title_offer', 'price_offer', 'description_offer',
            'start_date_offer', 'end_date_offer', 'discount_code_offer', 'image_offer', 'web_offer',
            'address_offer', 'created_offer', 'updated_offer'
        ];
        if (!in_array($orderBy, $validOrderColumns)) {
            throw new InvalidArgumentException("Invalid order by column: $orderBy");
        }

        // Construye la consulta SQL
        $query = ORM::for_table('offers')
            ->where_gte('price_offer', $minPrice)
            ->where_lte('price_offer', $maxPrice);

        // Aplica la ordenación dependiendo de la dirección
        if ($orderDirection === 'asc') {
            $query->order_by_asc($orderBy);
        } else {
            $query->order_by_desc($orderBy);
        }

        $offers = $query->find_many();
        return $this->convertCollection($offers);
    }

    public function addOffer($data) {
        // Verificación básica de los datos antes de crear la oferta
        if (empty($data['id_company_offer']) || empty($data['title_offer']) || empty($data['price_offer'])) {
            throw new InvalidArgumentException("Los datos de la oferta no son válidos"); 
        }

        $offer = ORM::for_table('offers')->create();
        $offer->id_company_offer = $data['id_company_offer'];
        $offer->id_category_offer = $data['id_category_offer'];
        $offer->title_offer = $data['title_offer'];
        $offer->description_offer = $data['description_offer'];
        $offer->price_offer = $data['price_offer'];
        $offer->start_date_offer = $data['start_date_offer'];
        $offer->end_date_offer = $data['end_date_offer'];
        $offer->discount_code_offer = $data['discount_code_offer'];
        $offer->image_offer = $data['image_offer'];
        $offer->web_offer = $data['web_offer'];
        $offer->address_offer = $data['address_offer'];
       
        $offer->save();
        return $this->convertObj($offer); // Retorna el objeto convertido
    }

    public function updateOffer($dataOffer) {
        $offer = ORM::for_table('offers')->find_one($dataOffer['id']);

        if ($offer) {
            // Comprueba que los datos no estén vacíos y si no mantiene los que ya tiene
            $offer->id_category_offer =  $dataOffer['id_category_offer'] ?? $offer->id_category_offer;
            $offer->title_offer =  $dataOffer['title_offer'] ?? $offer->title_offer;
            $offer->description_offer = $dataOffer['description_offer'] ?? $offer->description_offer;
            $offer->price_offer = $dataOffer['price_offer'] ?? $offer->price_offer;
            $offer->start_date_offer = $dataOffer['start_date_offer'] ?? $offer->start_date_offer;
            $offer->end_date_offer = $dataOffer['end_date_offer'] ?? $offer->end_date_offer;
            $offer->discount_code_offer = $dataOffer['discount_code_offer'] ?? $offer->discount_code_offer;
            $offer->image_offer = $dataOffer['image_offer'] ?? $offer->image_offer;
            $offer->web_offer = $dataOffer['web_offer'] ?? $offer->web_offer;
            $offer->address_offer = $dataOffer['address_offer'] ?? $offer->address_offer;
           
            // Guardar los cambios en la base de datos
            $offer->save();

            // Responder con éxito
            return true;
        } else {
            // Responder con error si la oferta no existe
            return false;
        }
    }

    public function deleteOffer($id) {
        $offer = ORM::for_table('offers')->find_one($id);
        if ($offer) {
            $offer->delete();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj) {
        return [
            'id' => $obj->id ?? null,
            'id_company_offer' => $obj->id_company_offer ?? null,
            'id_category_offer' => $obj->id_category_offer ?? null,
            'title_offer' => $obj->title_offer ?? null,
            'price_offer' => $obj->price_offer ?? null,
            'description_offer' => $obj->description_offer ?? null,
            'start_date_offer' => $obj->start_date_offer ?? null,
            'end_date_offer' => $obj->end_date_offer ?? null,
            'discount_code_offer' => $obj->discount_code_offer ?? null,
            'image_offer' => $obj->image_offer ?? null,
            'web_offer' => $obj->web_offer ?? null,
            'address_offer' => $obj->address_offer ?? null,
            'created_offer' => $obj->created_offer ?? null,
            'updated_offer' => $obj->updated_offer ?? null
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
?>

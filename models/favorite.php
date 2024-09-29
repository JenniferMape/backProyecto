<?php

class Favorite {

    public function getAllFavorites() {
        $favorites = ORM::for_table('favorites')->find_many();
        return $this->convertCollection($favorites);
    }

    public function getFavoritesByUser($id_user) {
        $favorites = ORM::for_table('favorites')->where('id_user_favorite', $id_user)->find_many();
        return $this->convertCollection($favorites);
    }

    public function addFavorite($data) {
        // Verificación básica de los datos antes de crear el favorito
        if (empty($data['id_user_favorite']) || empty($data['id_offer_favorite'])) {
            return false; // Retorna false si los datos no son válidos
        }

        $favorite = ORM::for_table('favorites')->create();
        $favorite->id_user_favorite = $data['id_user_favorite'];
        $favorite->id_offer_favorite = $data['id_offer_favorite'];
        $favorite->save();

        return $this->convertObj($favorite); // Retorna el objeto convertido
    }

    public function updateFavorite($data) {
        $favorite = ORM::for_table('favorites')->find_one($data['id']);

        if ($favorite) {
            // Actualizar los datos del favorito en la base de datos
            $favorite->id_user_favorite =  $data['id_user_favorite'] ?? $favorite->id_user_favorite;
            $favorite->id_offer_favorite = $data['id_offer_favorite'] ?? $favorite->id_offer_favorite;
        
            // Guardar los cambios en la base de datos
            $favorite->save();

            return true;
        } else {
            return false;
        }
    }

    public function deleteFavorite($id) {
        $favorite = ORM::for_table('favorites')->find_one($id);
        if ($favorite) {
            $favorite->delete();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj) {
        return [
            'id' => $obj->id ?? null,
            'id_user_favorite' => $obj->id_user_favorite ?? null,
            'id_offer_favorite'=> $obj->id_offer_favorite ?? null,
            'created_favorite' => $obj->created_favorite ?? null,
            'updated_favorite' => $obj->updated_favorite ?? null
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

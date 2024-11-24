<?php

class Favorite {

    public function getAllFavorites() {
        $favorites = ORM::for_table('favorites')->find_many();
        return $this->convertCollection($favorites);
    }
    public function getFavoritesWithDetailsByUser($id_user){
        // Realizar la consulta con un JOIN
        $favorites = ORM::for_table('favorites')
            ->select('offers.id', 'id')
            ->select('offers.title_offer', 'title_offer')
            ->select('offers.image_offer', 'image_offer')
            ->select('offers.start_date_offer', 'start_date_offer')
            ->select('offers.end_date_offer', 'end_date_offer')
            ->join('offers', ['favorites.id_offer_favorite', '=', 'offers.id'])
            ->where('favorites.id_user_favorite', $id_user)
            ->find_array();

        // Devolver los resultados
        return $favorites ?: [];
    }

    public function getFavoritesByUser($id_user) {
        $favorites = ORM::for_table('favorites')->where('id_user_favorite', $id_user)->find_many();
        
        return $this->convertCollection($favorites);
    }

    public function toggleFavorite($data) {
        // Verificar los datos de entrada
        if (empty($data['id_user_favorite']) || empty($data['id_offer_favorite'])) {
            return false; // Datos inválidos
        }
    
        // Buscar si ya existe el favorito
        $favorite = ORM::for_table('favorites')
            ->where('id_user_favorite', $data['id_user_favorite'])
            ->where('id_offer_favorite', $data['id_offer_favorite'])
            ->find_one();
    
        if ($favorite) {
            // Si existe, eliminar el favorito
            $favorite->delete();
            return ['action' => 'removed']; // Indicar que se eliminó
        } else {
            // Si no existe, crear un nuevo favorito
            $newFavorite = ORM::for_table('favorites')->create();
            $newFavorite->id_user_favorite = $data['id_user_favorite'];
            $newFavorite->id_offer_favorite = $data['id_offer_favorite'];
            $newFavorite->save();
    
            return ['action' => 'added', 'favorite' => $this->convertObj($newFavorite)]; // Indicar que se agregó
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

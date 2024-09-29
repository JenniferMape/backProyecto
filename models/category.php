<?php

class Category {

    public function getAllCategories() {
        $categories = ORM::for_table('categories')->find_many();
        return $this->convertCollection($categories);
    }
 
    public function getCategory($id) {
        $category = ORM::for_table('categories')->find_one($id);
        return $category ? $this->convertObj($category) : null;
    }

    public function addCategory($data) {
        // Verificación básica de los datos antes de crear la categoría
        if (empty($data['name_category']) || empty($data['description_category'])) {
            return false; // Retorna false si los datos no son válidos
        }

        $category = ORM::for_table('categories')->create();
        $category->name_category = $data['name_category'];
        $category->description_category = $data['description_category'];
        $category->save();

        return $this->convertObj($category); // Retorna el objeto convertido
    }

    public function updateCategory($data) {
        $category = ORM::for_table('categories')->find_one($data['id']);
    
        if ($category) {
            // Actualizar los datos de la categoría en la base de datos
            $category->name_category = $data['name_category'] ?? $category->name_category;
            $category->description_category =  $data['description_category'] ?? $category->description_category;
        
            // Guardar los cambios en la base de datos
            $category->save();

            return true;
        } else {
            return false;
        }
    }

    public function deleteCategory($id) {
        $category = ORM::for_table('categories')->find_one($id);
        if ($category) {
            $category->delete();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj) {
        return [
            'id' => $obj->id ?? null,
            'name_category' => $obj->name_category ?? null,
            'description_category' => $obj->description_category ?? null,
            'created_category' => $obj->created_category ?? null,
            'updated_category' => $obj->updated_category ?? null
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

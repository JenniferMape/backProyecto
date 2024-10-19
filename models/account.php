<?php

class Account
{

    public function getAccount($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);
        return $usuario ? $this->convertObj($usuario) : null;
    }

    public function updateAccount($data)
    {
        $usuario = ORM::for_table('users')->find_one($data['id']);

        if ($usuario) {
            // Actualizar los datos del usuario en la base de datos
            $usuario->name_user = $data['name_user'] ?? $usuario->name_user;
            $usuario->email_user = $data['email_user'] ?? $usuario->email_user;

            // Verificar si la contraseña se ha proporcionado y actualizarla
            if (!empty($data['password_user'])) {
                $usuario->password_user = password_hash($data['password_user'], PASSWORD_DEFAULT);
            }

            // Validar el CIF si se proporciona
            if (!empty($data['cif_user']) && $this->validarCIF($data['cif_user'])) {
                $usuario->cif_user = $data['cif_user'];
            }

            // Actualizar el avatar si está presente
            $usuario->avatar_user = $data['avatar_user'] ?? $usuario->avatar_user;

            // Guardar los cambios en la base de datos
            $usuario->save();

            return true;
        } else {
            return false;
        }
    }


    public function deleteAccount($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);
        if ($usuario) {
            $usuario->delete();
            return true;
        } else {
            return false;
        }
    }
    public function getAvatar($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);
        $baseUrl = 'http://chollocuenca.com/';

        if ($usuario) {
            $avatar = $usuario->avatar_user;
            // Si no hay avatar, devolver la URL de la imagen por defecto
            return empty($avatar) ? $baseUrl . 'default.png' : $baseUrl . $avatar;
        } else {
            return null; // Usuario no encontrado
        }
    }


    public function updateAvatar($id, $avatar = null, $delete = false)
    {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            if ($delete) {
                $usuario->avatar_user = null;
            } else if (is_array($avatar) && isset($avatar['name']) && !empty($avatar['name'])) {
                // Verificación de la extensión del archivo
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

                // Verificación del tipo MIME
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $mimeType = mime_content_type($avatar['tmp_name']);

                if (in_array($extension, $allowedExtensions) && in_array($mimeType, $allowedMimeTypes)) {
                    // Generar un nuevo nombre para el archivo basado en el ID del usuario
                    $newFileName = $id . '.' . $extension;

                    // Cambia a ruta absoluta para evitar problemas
                    $uploadDir = realpath(__DIR__ . '/../uploads/avatars/') . '/';
                    $uploadPath = $uploadDir . $newFileName;

                    // Mover el archivo subido a la carpeta de destino
                    if (move_uploaded_file($avatar['tmp_name'], $uploadPath)) {
                        // Guardar la ruta relativa del avatar en la base de datos
                        $usuario->avatar_user = 'uploads/avatars/' . $newFileName;
                    } else {
                        return ['error' => 'Error al mover el archivo subido.'];
                    }
                } else {
                    return ['error' => 'Tipo de archivo no permitido. Solo se permiten archivos JPG, PNG o GIF.'];
                }
            }

            $usuario->save();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj)
    {
        $baseUrl = 'http://chollocuenca.com'; 
        return [
            'id' => $obj->id ?? null,
            'name_user' => $obj->name_user ?? null,
            'email_user' => $obj->email_user ?? null,
            'cif_user' => $obj->cif_user ?? null,
            'avatar_user' => !empty($obj->avatar_user) ? $baseUrl . $obj->avatar_user : $baseUrl . 'default.png',
            'type_user' => $obj->type_user ?? null,
            'created_user' => $obj->created_user ?? null,
            'updated_user' => $obj->updated_user ?? null
        ];
    }
    

    public function validarCIF($cif)
    {
        $cifRegex = '/^[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J]$/i';

        if (preg_match($cifRegex, $cif)) {
            $control = 'JABCDEFGHI';
            $sumaPar = 0;
            $sumaImpar = 0;

            for ($i = 1; $i < 8; $i++) {
                $numero = (int) $cif[$i];

                // Sumar los dígitos en posiciones pares
                if ($i % 2 == 0) {
                    $sumaPar += $numero;
                } else {
                    // Duplicar los dígitos en posiciones impares y sumar los dígitos del resultado
                    $imp = 2 * $numero;
                    if ($imp > 9) $imp = 1 + ($imp - 10);
                    $sumaImpar += $imp;
                }
            }

            // Sumar todas las cifras obtenidas
            $sumaTotal = $sumaPar + $sumaImpar;

            // Calcular el dígito de control
            $digitoControl = (10 - ($sumaTotal % 10)) % 10;
            $letraControl = $control[$digitoControl];

            // El dígito de control debe coincidir con el último carácter del CIF
            return strtoupper($cif[8]) == $letraControl;
        }
        return false;
    }
}

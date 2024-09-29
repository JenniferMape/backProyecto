<?php

class Account {

    public function getAccount($id) {
        $usuario = ORM::for_table('users')->find_one($id);
        return $usuario ? $this->convertObj($usuario) : null;
    }

    public function updateAccount($data) {
        $usuario = ORM::for_table('users')->find_one($data['id']);

        if ($usuario) {
            // Actualizar los datos del usuario en la base de datos
            $usuario->name_user = $data['name_user'] ?? $usuario->name_user;
            $usuario->email_user = $data['email_user'] ?? $usuario->email_user;
            if (isset($data['password_user']) && !empty($data['password_user'])) {
                $usuario->password_user = password_hash($data['password_user'], PASSWORD_DEFAULT);
            }
            if (isset($data['cif_user']) && !empty($data['cif_user']) && $this->validarCIF($data['cif_user'])) {
                $usuario->cif_user = $data['cif_user'];
            }
            $usuario->avatar_user = $data['avatar_user'] ?? $usuario->avatar_user;

            // Guardar los cambios en la base de datos
            $usuario->save();

            return true;
        } else {
            return false;
        }
    }

    public function deleteAccount($id) {
        $usuario = ORM::for_table('users')->find_one($id);
        if ($usuario) {
            $usuario->delete();
            return true;
        } else {
            return false;
        }
    }

    public function getAvatar($id) {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            $avatar = $usuario->avatar_user;
            return empty($avatar) ? 'default.png' : $avatar;
        } else {
            return null;
        }
    }

    public function updateAvatar($id, $avatar = null, $delete = false) {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            if ($delete) {
                $usuario->avatar_user = null; 
            } else if ($avatar && !empty($avatar)) {
                $usuario->avatar_user = $avatar;
            }
            $usuario->save();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj) {
        return [
            'id' => $obj->id ?? null,
            'name_user' => $obj->name_user ?? null,
            'email_user' => $obj->email_user ?? null,
            'cif_user' => $obj->cif_user ?? null,
            'avatar_user' => $obj->avatar_user ?? 'default.png',
            'type_user' => $obj->type_user ?? null,
            'created_user' => $obj->created_user ?? null,
            'updated_user' => $obj->updated_user ?? null
        ];
    }

    public function validarCIF($cif) {
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
?>

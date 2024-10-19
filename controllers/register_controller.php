<?php

// Si el método es OPTIONS, devolver inmediatamente los encabezados sin ejecutar más código.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


$method = new HTTPMethod();
$methodR = $method->getMethod();
//  echo '<pre>'; print_r($method->getMethod()); echo '</pre>';+

if (!empty($routesArray[1])) {
    switch ($routesArray[1]) {

        case 'login':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $input = file_get_contents('php://input');

                // Decodificar el JSON
                $data = json_decode($input, true);

                // Verificar si la decodificación fue exitosa
                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }
                // Validación inicial de campos requeridos
                if (empty($data['email_user']) || empty($data['password_user'])) {
                    sendJsonResponse(400, null, 'Todos los campos son obligatorios.');
                    return;
                }

                // Validación de email
                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }

                // Conexión con la base de datos y búsqueda del usuario
                $usuario = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();

                // Verificar si el usuario existe y la contraseña es correcta
                if ($usuario && password_verify($data['password_user'], $usuario->password_user)) {
                    $key = JWT_SECRET;
                    $issuedAt = time();
                    $expirationTime = $issuedAt + 3600;
                    $payload = [
                        'iat' => $issuedAt,
                        'exp' => $expirationTime,
                        'data' => [
                            'userId' => $usuario->id,
                            'email' => $usuario->email_user,
                        ],
                    ];

                    // Generar el token
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    $baseUrl = 'http://chollocuenca.com/'; // Asegúrate de que esta URL sea correcta
                    sendJsonResponse(200, [
                        'user' => [
                            'id' => $usuario->id,
                            'email_user' => $usuario->email_user,
                            'name_user' => $usuario->name_user,
                            'type_user' => $usuario->type_user,
                            'avatar_user' => !empty($usuario->avatar_user) ? $baseUrl . $usuario->avatar_user : $baseUrl . 'default.png',
                        ],
                        'token' => $jwt
                    ], 'Inicio de sesión exitoso.');
                } else {
                    // Datos incorrectos
                    sendJsonResponse(403, null, 'Correo electrónico o contraseña incorrectos.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }

            break;

        case 'recovery':
            // if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //     // Validación inicial de campos requeridos
            //     if (empty($_POST['current_password']) || empty($_POST['new_password'])) {

            //         $cR->sendJsonResponse(400, null,'Todos los campos son obligatorios.');

            //         return;
            //     }

            //     // Verificar si la contraseña actual es correcta
            //     if (!$usuario || !password_verify($_POST['current_password'], $usuario->password_user)) {
            //         $cR->sendJsonResponse(403, null,'La contraseña actual es incorrecta.');
            //         return;
            //     }

            //     // Validación de la nueva contraseña (p.ej., longitud mínima)
            //     if (strlen($_POST['new_password']) < 8) {
            //         $cR->sendJsonResponse(400, null,'La nueva contraseña debe tener al menos 8 caracteres.');
            //         return;
            //     }

            //     // Cambiar la contraseña en la base de datos
            //     $usuario->password_user = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            //     $usuario->save();
            //     $cR->sendJsonResponse(200, $usuario->name_user,'Contraseña actualizada correctamente.');

            // } else {
            //     $cR->sendJsonResponse(405, null,'Método no permitido.');
            // }
            //GENERO UNA NUEVA CONTRASEÑA, SE MANDA AL EMAIL (COMPROBAR QUE EXISTA ANTES) Y SE GUARDA LA NUEVA CONTRASEÑA EN LA BD
            break;
        case 'new':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $input = file_get_contents('php://input');

                // Decodificar el JSON
                $data = json_decode($input, true);

                // Verificar si la decodificación fue exitosa
                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }

                // Comprobación de que los campos obligatorios estén completados
                if (
                    empty($data['name_user']) || empty($data['email_user']) ||
                    empty($data['password_user']) || empty($data['type_user'])
                ) {
                    sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                    return;
                }

                // Validación de email
                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }

                // Validación de la contraseña (longitud mínima)
                if (strlen($data['password_user']) < 8) {
                    sendJsonResponse(400, null, 'La contraseña debe tener al menos 8 caracteres.');
                    return;
                }

                // Verificar si el usuario ya existe
                $existingUser = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();
                if ($existingUser) {
                    sendJsonResponse(409, null, 'El correo electrónico ya está registrado.');
                    return;
                }

                $usuario = ORM::for_table('users')->create();

                $usuario->name_user = $data['name_user'];
                $usuario->email_user = $data['email_user'];
                $usuario->password_user = password_hash($data['password_user'], PASSWORD_DEFAULT);
                $usuario->type_user = $data['type_user'];

                if ($data['type_user'] === 'COMPANY') {
                    if (empty($data['cif_user'])) {
                        sendJsonResponse(400, null, 'El CIF es requerido para los usuarios tipo \'EMPRESA\'.');
                        return;
                    }
                    $usuario->cif_user = $data['cif_user'];
                }

                $usuario->avatar_user = isset($data['avatar_user']) ? $data['avatar_user'] : null;

                if ($usuario->save()) {
                    $key = JWT_SECRET;
                    $issuedAt = time();
                    $expirationTime = $issuedAt + 3600;
                    $payload = [
                        'iat' => $issuedAt,
                        'exp' => $expirationTime,
                        'data' => [
                            'userId' => $usuario->id,
                            'email' => $usuario->email_user,
                        ],
                    ];

                    // Generar el token
                    $jwt = JWT::encode($payload, $key, 'HS256');

                    sendJsonResponse(200, [
                        'user' => [
                            'id' => $usuario->id,
                            'email_user' => $usuario->email_user,
                            'name_user' => $usuario->name_user,
                            'type_user' => $usuario->type_user,
                            'avatar_user' => $usuario->avatar_user
                        ],
                        'token' => $jwt
                    ], 'Usuario guardado correctamente.');
                } else {
                    sendJsonResponse(500, null, 'Error al guardar el usuario.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
            break;


        default:
            echo 'default';
            break;
    }
}

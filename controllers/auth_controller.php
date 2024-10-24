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

        case 'checkToken':
            if ($_SERVER["REQUEST_METHOD"] == "GET") {

                // Obtener el header Authorization
                $headers = getallheaders();
                $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

                if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
                    sendJsonResponse(400, null, 'Token no proporcionado en el encabezado.');
                    return;
                }

                // Extraer el token del encabezado
                $token = str_replace('Bearer ', '', $authHeader);

                $key = JWT_SECRET;

                try {
                    // Decodificar el token utilizando la clase Key
                    $decoded = JWT::decode($token, new Key($key, 'HS256'));

                    // Extraer la información del usuario desde el token
                    $userId = $decoded->data->userId;

                    // Verificar si el usuario existe en la base de datos
                    $usuario = ORM::for_table('users')->find_one($userId);

                    if ($usuario) {

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


                        // Responder con éxito si el usuario es válido
                        sendJsonResponse(200, [
                            'user' => [
                                'id' => $usuario->id,
                                'email_user' => $usuario->email_user,
                                'name_user' => $usuario->name_user,
                                'type_user' => $usuario->type_user,
                                'avatar_user' => $usuario->avatar_user
                            ],
                            'token' => $jwt
                        ], 'Token válido.');
                    } else {
                        sendJsonResponse(403, null, 'Usuario no encontrado.');
                    }
                } catch (Exception $e) {
                    // Si el token es inválido o expiró
                    sendJsonResponse(401, null, 'Token inválido o expirado.');
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

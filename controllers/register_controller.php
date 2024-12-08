<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');
include('./models/register.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Firebase\JWT\JWT;
$JWT_SECRET = getenv('JWT_SECRET');


$method = new HTTPMethod();
$methodR = $method->getMethod();
$register = new Register();
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
                    $key = $JWT_SECRET;
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
                            'avatar_user' => $usuario->avatar_user,
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
        
            //GENERO UNA NUEVA CONTRASEÑA, SE MANDA AL EMAIL (COMPROBAR QUE EXISTA ANTES) Y SE GUARDA LA NUEVA CONTRASEÑA EN LA BD
               if ($_SERVER["REQUEST_METHOD"] == "POST") {

                // Obtener los datos del cuerpo de la solicitud POST
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
        
                // Verificar si la decodificación fue exitosa
                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }
        
                // Verificar si el email está presente en los datos
                if (empty($data['email_user'])) {
                    sendJsonResponse(400, null, 'El campo de email es obligatorio.');
                    return;
                }
        
                // Validar el formato del email
                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }
        
                // Buscar el usuario en la base de datos por el email
                $user = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();
        
                if (!$user) {
                    sendJsonResponse(404, null, 'No se encontró un usuario con ese email.');
                    return;
                }
        
                // Generar una nueva contraseña aleatoria
                $newPassword = $register->generateRandomPassword();
        
                // Hashear la nueva contraseña
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
                // Actualizar la contraseña en la base de datos
                $user->password_user = $hashedPassword;
        
                if ($user->save()) {
                    // Enviar la nueva contraseña al email
                    $mail = new PHPMailer(true);
                    try {
                        // Configuración del servidor SMTP
                        $mail->isSMTP();
                        $mail->Host = getenv('MAIL_HOST');
                        $mail->SMTPAuth = true;
                        $mail->Username = getenv('MAIL_USERNAME');
                        $mail->Password = getenv('MAIL_PASSWORD');
                        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') == 'TLS' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = getenv('MAIL_PORT');
        
                        // Remitente y destinatario
                        $mail->setFrom(getenv('MAIL_USERNAME'), 'Chollo Cuenca');
                        $mail->addAddress($data['email_user']); // Enviar al correo del usuario
        
                        // Contenido del correo
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperacion de cuenta - CholloCuenca';
                        $mail->Body = "
                            <h2>Recuperación de cuenta</h2>
                            <p>Tu nueva contraseña es: <strong>$newPassword</strong></p>
                            <p>Recuerda cambiarla por una contraseña más segura una vez inicies sesión.</p>
                        ";
        
                        $mail->send();
                        sendJsonResponse(200, null, 'Se ha enviado una nueva contraseña a tu correo.');
                    } catch (Exception $e) {
                        sendJsonResponse(500, null, 'No se pudo enviar el correo.');
                        error_log('Error al enviar el correo: ' . $mail->ErrorInfo);
                    }
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar la contraseña.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
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
                    $key = $JWT_SECRET;
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
     function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }
}

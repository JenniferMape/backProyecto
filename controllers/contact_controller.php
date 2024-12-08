<?php
require 'vendor/autoload.php'; // Asegúrate de que PHPMailer esté instalado
include('./helpers/response.php');
include('./helpers/HTTPMethod.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$method = new HTTPMethod();
$methodR = $method->getMethod();

// Verificar si la primera parte de la URL es 'contact'
if ($routesArray[0] == 'contact') {
    switch ($methodR['method']) {
        case 'POST': // Manejar el envío del formulario
            header('Content-Type: application/json');

            // Verifica si los datos se enviaron correctamente
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['email']) || empty($data['message'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Por favor, completa todos los campos.']);
                exit;
            }

            // Datos del formulario
            $userEmail = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $userMessage = htmlspecialchars($data['message']);

            // Configura el envío del correo
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
                $mail->setFrom(getenv('MAIL_USERNAME'), 'Chollo Cuenca'); // Tu correo
                $mail->addAddress(getenv('MAIL_USERNAME')); // Receptor (tu cuenta)

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Nuevo mensaje de contacto en CholloCuenca';
                $mail->Body = "
                    <h2>Nuevo mensaje de contacto</h2>
                    <p><strong>Email:</strong> $userEmail</p>
                    <p><strong>Mensaje:</strong></p>
                    <p>$userMessage</p>
                ";

                $mail->send();
                http_response_code(200);
                echo json_encode(['message' => 'Mensaje enviado con éxito.']);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo enviar el mensaje.']);
                error_log('Error al enviar el correo: ' . $mail->ErrorInfo);
            }
            break;

        default:
            sendJsonResponse(405, null, 'Método no permitido.');
            break;
    }
} else {
    sendJsonResponse(404, null, 'Recurso no encontrado.');
}

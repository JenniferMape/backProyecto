<?php

//sacar a un archivo en la carpeta helpers
     function sendJsonResponse($status, $data, $message = '') {
        header('Content-Type: application/json');
        http_response_code($status);
        
        $response = ['status' => $status];
        if ($status >= 400) {
            $response['error'] = $message;
        } else {
            $response['result'] = $data;
            $response['message']=$message;
        }
    
        echo json_encode($response);
        exit;
    }
 
    
    ?>
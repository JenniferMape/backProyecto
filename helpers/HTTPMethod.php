<?php
class HTTPMethod{

    protected $method;

    public function __construct(){
        $this->method=$_SERVER['REQUEST_METHOD'];  
    }

    public function getMethod(){
        switch ($this->method){
            /**=================================================
            * Peticiones GET
            ====================================================*/
            case 'GET':
               $json = array(
                  'status'=>200,
                  'result'=>'Solicitud GET',
                  'method'=>'GET'
               );
               return $json;
            break;
            /**=================================================
             * Peticiones POST
            ====================================================*/
            case 'POST':
               $json = array(
                  'status'=>200,
                  'result'=>'Solicitud POST',
                  'method'=>'POST'
               );
               return $json;
            break;
            /**=================================================
             * Peticiones PUT
            ====================================================*/
            case 'PUT':
               $json = array(
                  'status'=>200,
                  'result'=>'Solicitud PUT',
                  'method'=>'PUT'
               );
               return $json;
            break;
            /**=================================================
             * Peticiones DELETE
            ====================================================*/
            case 'DELETE':
               $json = array(
                  'status'=>200,
                  'result'=>'Solicitud DELETE',
                  'method'=>'DELETE'
               );
               return $json;
            break;
            default:
            $json = array(
                'status'=>403,
                'result'=>'Unauthorized'
             );
             echo json_encode($json,http_response_code($json["status"]));
         }
    }
    
}
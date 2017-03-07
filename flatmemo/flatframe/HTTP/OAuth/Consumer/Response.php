<?php
 require_once 'HTTP/OAuth/Message.php'; require_once 'HTTP/OAuth/Exception.php'; require_once 'HTTP/Request2/Response.php'; class HTTP_OAuth_Consumer_Response extends HTTP_OAuth_Message
 {
     protected $response = null;
     public function __construct(HTTP_Request2_Response $response)
     {
         $this->response = $response;
     }
     public function getDataFromBody()
     {
         $result = array();
         parse_str($this->getBody(), $result);
         return $result;
     }
     public function getResponse()
     {
         return $this->response;
     }
     public function __call($method, $args)
     {
         if (method_exists($this->response, $method)) {
             return call_user_func_array(array($this->response, $method), $args);
         }
         throw new BadMethodCallException($method);
     }
 }

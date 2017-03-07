<?php
 require_once 'HTTP/OAuth/Exception.php'; require_once 'HTTP/OAuth/Consumer/Response.php'; class HTTP_OAuth_Consumer_Exception_InvalidResponse extends HTTP_OAuth_Exception
 {
     public $response = null;
     public function __construct($message, HTTP_OAuth_Consumer_Response $response)
     {
         parent::__construct($message);
         $this->response = $response;
     }
     public function __call($method, $args)
     {
         if (method_exists($this->response->getResponse(), $method) || method_exists($this->response, $method)) {
             return call_user_func_array(array($this->response, $method), $args);
         }
         throw new BadMethodCallException($method);
     }
 }

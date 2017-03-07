<?php
 require_once 'HTTP/OAuth/Message.php'; require_once 'HTTP/OAuth/Signature.php'; require_once 'HTTP/OAuth/Provider/Exception/InvalidRequest.php'; class HTTP_OAuth_Provider_Request extends HTTP_OAuth_Message
 {
     protected $headers = array();
     protected $method = '';
     public function __construct()
     {
         $this->setHeaders();
         $this->setParametersFromRequest();
     }
     public function setHeaders(array $headers = array())
     {
         if (count($headers)) {
             $this->headers = $headers;
         } elseif (is_array($this->apacheRequestHeaders())) {
             $this->debug('Using apache_request_headers() to get request headers');
             $this->headers = $this->apacheRequestHeaders();
         } elseif (is_array($this->peclHttpHeaders())) {
             $this->debug('Using pecl_http to get request headers');
             $this->headers = $this->peclHttpHeaders();
         } else {
             $this->debug('Using $_SERVER to get request headers');
             foreach ($_SERVER as $name => $value) {
                 if (substr($name, 0, 5) == 'HTTP_') {
                     $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                     $this->headers[$name] = $value;
                 }
             }
         }
     }
     protected function apacheRequestHeaders()
     {
         if (function_exists('apache_request_headers')) {
             return apache_request_headers();
         }
         return null;
     }
     protected function peclHttpHeaders()
     {
         if (extension_loaded('http') && class_exists('HttpMessage')) {
             $message = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);
             return $message->getHeaders();
         }
         return null;
     }
     public function setParametersFromRequest()
     {
         $params = array();
         $auth = $this->getHeader('Authorization');
         if ($auth !== null) {
             $this->debug('Using OAuth data from header');
             $parts = explode(',', $auth);
             foreach ($parts as $part) {
                 list($key, $value) = explode('=', trim($part));
                 if (strstr(strtolower($key), 'oauth ') || strstr(strtolower($key), 'uth re') || substr(strtolower($key), 0, 6) != 'oauth_') {
                     continue;
                 }
                 $value = trim($value);
                 $value = str_replace('"', '', $value);
                 $params[$key] = $value;
             }
         }
         if ($this->getRequestMethod() == 'POST') {
             $this->debug('getting data from POST');
             $contentType = substr($this->getHeader('Content-Type'), 0, 33);
             if ($contentType !== 'application/x-www-form-urlencoded') {
                 throw new HTTP_OAuth_Provider_Exception_InvalidRequest('Invalid ' . 'content type for POST request');
             }
             $params = array_merge($params, $this->parseQueryString($this->getPostData()));
         }
         $params = array_merge($params, $this->parseQueryString($this->getQueryString()));
         if (empty($params)) {
             throw new HTTP_OAuth_Provider_Exception_InvalidRequest('No oauth ' . 'data found from request');
         }
         $this->setParameters(HTTP_OAuth::urldecode($params));
     }
     public function isValidSignature($consumerSecret, $tokenSecret = '')
     {
         if (!$this->oauth_signature_method) {
             throw new HTTP_OAuth_Provider_Exception_InvalidRequest('Missing oauth_signature_method in request');
         }
         $sign = HTTP_OAuth_Signature::factory($this->oauth_signature_method);
         $check = $sign->build($this->getRequestMethod(), $this->getUrl(), $this->getParameters(), $consumerSecret, $tokenSecret);
         if ($this->oauth_signature === $check) {
             $this->info('Valid signature');
             return true;
         }
         $this->err('Invalid signature');
         return false;
     }
     public function getQueryString()
     {
         if (!empty($_SERVER['QUERY_STRING'])) {
             return $_SERVER['QUERY_STRING'];
         }
         return null;
     }
     public function getRequestMethod()
     {
         if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
             return 'HEAD';
         }
         return $_SERVER['REQUEST_METHOD'];
     }
     public function getUrl()
     {
         $schema = 'http';
         if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
             $schema .= 's';
         }
         return $schema . '://' . $this->getHeader('Host') . $this->getRequestUri();
     }
     public function getRequestUri()
     {
         if (!array_key_exists('REQUEST_URI', $_SERVER)) {
             return null;
         }
         $uri = $_SERVER['REQUEST_URI'];
         $pos = stripos($uri, '://');
         if (!$pos) {
             return $uri;
         }
         return substr($uri, strpos($uri, '/', $pos + 3));
     }
     public function getHeader($header)
     {
         foreach ($this->headers as $name => $value) {
             if (strtolower($header) == strtolower($name)) {
                 return $value;
             }
         }
         return null;
     }
     public function getHeaders()
     {
         return $this->headers;
     }
     protected function getPostData()
     {
         return file_get_contents('php://input');
     }
     protected function parseQueryString($string)
     {
         $data = array();
         if (empty($string)) {
             return $data;
         }
         foreach (explode('&', $string) as $part) {
             if (!strstr($part, '=')) {
                 continue;
             }
             list($key, $value) = explode('=', $part);
             $data[$key] = self::urldecode($value);
         }
         return $data;
     }
 }

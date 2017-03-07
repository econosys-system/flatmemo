<?php
 require_once 'HTTP/OAuth/Message.php'; class HTTP_OAuth_Provider_Response extends HTTP_OAuth_Message
 {
     const STATUS_UNSUPPORTED_PARAMETER = 0;
     const STATUS_UNSUPPORTED_SIGNATURE_METHOD = 1;
     const STATUS_MISSING_REQUIRED_PARAMETER = 2;
     const STATUS_DUPLICATED_OAUTH_PARAMETER = 3;
     const STATUS_INVALID_CONSUMER_KEY = 4;
     const STATUS_INVALID_TOKEN = 5;
     const STATUS_INVALID_SIGNATURE = 6;
     const STATUS_INVALID_NONCE = 7;
     const STATUS_INVALID_VERIFIER = 8;
     const STATUS_INVALID_TIMESTAMP = 9;
     protected static $statusMap = array( self::STATUS_UNSUPPORTED_PARAMETER => array( 400, 'Unsupported parameter' ), self::STATUS_UNSUPPORTED_SIGNATURE_METHOD => array( 400, 'Unsupported signature method' ), self::STATUS_MISSING_REQUIRED_PARAMETER => array( 400, 'Missing required parameter' ), self::STATUS_DUPLICATED_OAUTH_PARAMETER => array( 400, 'Duplicated OAuth Protocol Parameter' ), self::STATUS_INVALID_CONSUMER_KEY => array( 401, 'Invalid Consumer Key' ), self::STATUS_INVALID_TOKEN => array( 401, 'Invalid / expired Token' ), self::STATUS_INVALID_SIGNATURE => array( 401, 'Invalid signature' ), self::STATUS_INVALID_NONCE => array( 401, 'Invalid / used nonce' ), self::STATUS_INVALID_VERIFIER => array( 401, 'Invalid verifier' ), self::STATUS_INVALID_TIMESTAMP => array( 401, 'Invalid timestamp' ), );
     protected $headers = array();
     protected $body = '';
     public function setRealm($realm)
     {
         $header = 'OAuth realm="' . $realm . '"';
         $this->setHeader('WWW-Authenticate', $header);
     }
     public function setHeader($name, $value)
     {
         $this->headers[$name] = $value;
     }
     public function getHeader($name)
     {
         if (array_key_exists($name, $this->headers)) {
             return $this->headers[$name];
         }
         return null;
     }
     public function getHeaders()
     {
         return $this->headers;
     }
     public function setHeaders(array $headers)
     {
         $this->headers = $headers;
     }
     public function setStatus($status)
     {
         if (!array_key_exists($status, self::$statusMap)) {
             throw new HTTP_OAuth_Exception('Invalid status');
         }
         list($code, $text) = self::$statusMap[$status];
         $this->setBody($text);
         if ($this->headersSent()) {
             throw new HTTP_OAuth_Exception('Status already sent');
         }
         switch ($code) { case 400: $this->header('HTTP/1.1 400 Bad Request'); break; case 401: $this->header('HTTP/1.1 401 Unauthorized'); break; }
     }
     protected function headersSent()
     {
         return headers_sent();
     }
     protected function header($header)
     {
         return header($header);
     }
     protected function prepareBody()
     {
         if ($this->headersSent() && $this->getBody() !== '') {
             $this->err('Body already sent, not setting');
         } else {
             $this->setBody(HTTP_OAuth::buildHTTPQuery($this->getParameters()));
         }
     }
     public function setBody($body)
     {
         $this->body = $body;
     }
     public function getBody()
     {
         return $this->body;
     }
     public function send()
     {
         $this->prepareBody();
         if (!$this->headersSent()) {
             $this->header('HTTP/1.1 200 OK');
             foreach ($this->getHeaders() as $name => $value) {
                 $this->header($name . ': ' . $value);
             }
         } else {
             $this->err('Headers already sent, can not send headers');
         }
         echo $this->getBody();
     }
 }

<?php
 require_once 'HTTP/Request2.php'; require_once 'HTTP/Request2/Observer/Log.php'; require_once 'HTTP/OAuth/Message.php'; require_once 'HTTP/OAuth/Consumer/Response.php'; require_once 'HTTP/OAuth/Signature.php'; require_once 'HTTP/OAuth/Exception.php'; class HTTP_OAuth_Consumer_Request extends HTTP_OAuth_Message
 {
     const AUTH_HEADER = 1;
     const AUTH_POST = 2;
     const AUTH_GET = 3;
     protected $authType = self::AUTH_HEADER;
     protected $secrets = array('', '');
     protected $request = null;
     public function __construct($url = null, array $secrets = array())
     {
         if ($url !== null) {
             $this->setUrl($url);
         }
         if (count($secrets)) {
             $this->setSecrets($secrets);
         }
     }
     public function accept($object)
     {
         switch (get_class($object)) { case 'HTTP_Request2': $this->request = $object; foreach (self::$logs as $log) {
             $this->request->attach(new HTTP_Request2_Observer_Log($log));
         } break; default: if ($object instanceof Log) {
             HTTP_OAuth::attachLog($object);
             $this->getHTTPRequest2()->attach(new HTTP_Request2_Observer_Log($object));
         } break; }
     }
     protected function getHTTPRequest2()
     {
         if (!$this->request instanceof HTTP_Request2) {
             $this->accept(new HTTP_Request2);
         }
         return $this->request;
     }
     public function setSecrets(array $secrets = array())
     {
         if (count($secrets) == 1) {
             $secrets[1] = '';
         }
         $this->secrets = $secrets;
     }
     public function getSecrets()
     {
         return $this->secrets;
     }
     public function setAuthType($type)
     {
         static $valid = array(self::AUTH_HEADER, self::AUTH_POST, self::AUTH_GET);
         if (!in_array($type, $valid)) {
             throw new InvalidArgumentException('Invalid Auth Type, see class ' . 'constants');
         }
         $this->authType = $type;
     }
     public function getAuthType()
     {
         return $this->authType;
     }
     public function send()
     {
         $this->buildRequest();
         $request = $this->getHTTPRequest2();
         $headers = $request->getHeaders();
         $contentType = isset($headers['content-type']) ? $headers['content-type'] : '';
         if ($this->getMethod() == 'POST' && $contentType == 'application/x-www-form-urlencoded') {
             $body = $this->getHTTPRequest2()->getBody();
             $body = str_replace('+', '%20', $body);
             $this->getHTTPRequest2()->setBody($body);
         }
         try {
             $response = $this->getHTTPRequest2()->send();
         } catch (Exception $e) {
             throw new HTTP_OAuth_Exception($e->getMessage(), $e->getCode());
         }
         return new HTTP_OAuth_Consumer_Response($response);
     }
     protected function buildRequest()
     {
         $method = $this->getSignatureMethod();
         $this->debug('signing request with: ' . $method);
         $sig = HTTP_OAuth_Signature::factory($this->getSignatureMethod());
         $this->oauth_timestamp = time();
         $this->oauth_nonce = md5(microtime(true) . rand(1, 999));
         $this->oauth_version = '1.0';
         $params = array_merge($this->getParameters(), $this->getUrl()->getQueryVariables());
         $this->oauth_signature = $sig->build($this->getMethod(), $this->getUrl()->getURL(), $params, $this->secrets[0], $this->secrets[1]);
         $params = $this->getOAuthParameters();
         switch ($this->getAuthType()) { case self::AUTH_HEADER: $auth = $this->getAuthForHeader($params); $this->setHeader('Authorization', $auth); break; case self::AUTH_POST: foreach ($params as $name => $value) {
             $this->addPostParameter($name, $value);
         } break; case self::AUTH_GET: break; }
         switch ($this->getMethod()) { case 'POST': foreach ($this->getParameters() as $name => $value) {
             if (substr($name, 0, 6) == 'oauth_') {
                 continue;
             }
             $this->addPostParameter($name, $value);
         } break; case 'GET': $url = $this->getUrl(); foreach ($this->getParameters() as $name => $value) {
             if (substr($name, 0, 6) == 'oauth_') {
                 continue;
             }
             $url->setQueryVariable($name, $value);
         } $this->setUrl($url); break; default: break; }
     }
     protected function getAuthForHeader(array $params)
     {
         $url = $this->getUrl();
         $realm = $url->getScheme() . '://' . $url->getHost() . '/';
         $header = 'OAuth realm="' . $realm . '"';
         foreach ($params as $name => $value) {
             $header .= ", " . HTTP_OAuth::urlencode($name) . '="' . HTTP_OAuth::urlencode($value) . '"';
         }
         return $header;
     }
     public function __call($method, $args)
     {
         $httpRequest2 = $this->getHTTPRequest2();
         if (is_callable(array($httpRequest2, $method))) {
             return call_user_func_array(array($httpRequest2, $method), $args);
         }
         throw new BadMethodCallException($method);
     }
 }

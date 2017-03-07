<?php
 require_once 'HTTP/OAuth.php'; require_once 'HTTP/OAuth/Consumer/Request.php'; require_once 'HTTP/OAuth/Consumer/Exception/InvalidResponse.php'; class HTTP_OAuth_Consumer extends HTTP_OAuth
 {
     protected $key = null;
     protected $secret = null;
     protected $token = null;
     protected $tokenSecret = null;
     protected $signatureMethod = 'HMAC-SHA1';
     protected $consumerRequest = null;
     protected $lastRequest = null;
     protected $lastResponse =null;
     public function __construct($key, $secret, $token = null, $tokenSecret = null)
     {
         $this->key = $key;
         $this->secret = $secret;
         $this->setToken($token);
         $this->setTokenSecret($tokenSecret);
     }
     public function getRequestToken($url, $callback = 'oob', array $additional = array(), $method = 'POST')
     {
         $this->debug('Getting request token from ' . $url);
         $additional['oauth_callback'] = $callback;
         $this->debug('callback: ' . $callback);
         $response = $this->sendRequest($url, $additional, $method);
         $data = $response->getDataFromBody();
         if (empty($data['oauth_token']) || empty($data['oauth_token_secret'])) {
             throw new HTTP_OAuth_Consumer_Exception_InvalidResponse('Failed getting token and token secret from response', $response);
         }
         $this->setToken($data['oauth_token']);
         $this->setTokenSecret($data['oauth_token_secret']);
     }
     public function getAccessToken($url, $verifier = '', array $additional = array(), $method = 'POST')
     {
         if ($this->getToken() === null || $this->getTokenSecret() === null) {
             throw new HTTP_OAuth_Exception('No token or token_secret');
         }
         $this->debug('Getting access token from ' . $url);
         if ($verifier !== null) {
             $additional['oauth_verifier'] = $verifier;
         }
         $this->debug('verifier: ' . $verifier);
         $response = $this->sendRequest($url, $additional, $method);
         $data = $response->getDataFromBody();
         if (empty($data['oauth_token']) || empty($data['oauth_token_secret'])) {
             throw new HTTP_OAuth_Consumer_Exception_InvalidResponse('Failed getting token and token secret from response', $response);
         }
         $this->setToken($data['oauth_token']);
         $this->setTokenSecret($data['oauth_token_secret']);
     }
     public function getAuthorizeUrl($url, array $additional = array())
     {
         $params = array('oauth_token' => $this->getToken());
         $params = array_merge($additional, $params);
         return sprintf('%s?%s', $url, HTTP_OAuth::buildHTTPQuery($params));
     }
     public function sendRequest($url, array $additional = array(), $method = 'POST')
     {
         $params = array( 'oauth_consumer_key' => $this->key, 'oauth_signature_method' => $this->getSignatureMethod() );
         if ($this->getToken()) {
             $params['oauth_token'] = $this->getToken();
         }
         $params = array_merge($additional, $params);
         $req = clone $this->getOAuthConsumerRequest();
         $req->setUrl($url);
         $req->setMethod($method);
         $req->setSecrets($this->getSecrets());
         $req->setParameters($params);
         $this->lastResponse = $req->send();
         $this->lastRequest = $req;
         return $this->lastResponse;
     }
     public function getKey()
     {
         return $this->key;
     }
     public function getSecret()
     {
         return $this->secret;
     }
     public function getToken()
     {
         return $this->token;
     }
     public function setToken($token)
     {
         $this->debug('token is now: ' . $token);
         $this->token = $token;
     }
     public function getTokenSecret()
     {
         return $this->tokenSecret;
     }
     public function setTokenSecret($secret)
     {
         $this->debug('token_secret is now: ' . $secret);
         $this->tokenSecret = $secret;
     }
     public function getSignatureMethod()
     {
         return $this->signatureMethod;
     }
     public function setSignatureMethod($method)
     {
         $this->signatureMethod = $method;
     }
     public function getSecrets()
     {
         return array($this->secret, (string) $this->tokenSecret);
     }
     public function accept($object)
     {
         $class = get_class($object);
         switch ($class) { case 'HTTP_OAuth_Consumer_Request': $this->consumerRequest = $object; break; case 'HTTP_Request2': $this->getOAuthConsumerRequest()->accept($object); break; default: throw new HTTP_OAuth_Exception('Could not accept: ' . $class); break; }
     }
     public function getOAuthConsumerRequest()
     {
         if (!$this->consumerRequest instanceof HTTP_OAuth_Consumer_Request) {
             $this->consumerRequest = new HTTP_OAuth_Consumer_Request;
         }
         return $this->consumerRequest;
     }
     public function getLastRequest()
     {
         return $this->lastRequest;
     }
     public function getLastResponse()
     {
         return $this->lastResponse;
     }
 }

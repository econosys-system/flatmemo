<?php
 class Dropbox_OAuth_PEAR extends Dropbox_OAuth
 {
     protected $oAuth;
     protected $consumerKey;
     public function __construct($consumerKey, $consumerSecret)
     {
         if (!class_exists('HTTP_OAuth_Consumer')) {
             include 'HTTP/OAuth/Consumer.php';
         }
         if (!class_exists('HTTP_OAuth_Consumer')) {
             throw new Dropbox_Exception('The HTTP_OAuth_Consumer class could not be found! Did you install the pear HTTP_OAUTH class?');
         }
         $this->OAuth = new HTTP_OAuth_Consumer($consumerKey, $consumerSecret);
         $this->consumerKey = $consumerKey;
     }
     public function setToken($token, $token_secret = null)
     {
         parent::setToken($token, $token_secret);
         $this->OAuth->setToken($this->oauth_token);
         $this->OAuth->setTokenSecret($this->oauth_token_secret);
     }
     public function fetch($uri, $arguments = array(), $method = 'GET', $httpHeaders = array())
     {
         $consumerRequest = new HTTP_OAuth_Consumer_Request();
         $consumerRequest->setUrl($uri);
         $consumerRequest->setMethod($method);
         $consumerRequest->setSecrets($this->OAuth->getSecrets());
         $parameters = array( 'oauth_consumer_key' => $this->consumerKey, 'oauth_signature_method' => 'HMAC-SHA1', 'oauth_token' => $this->oauth_token, );
         if (is_array($arguments)) {
             $parameters = array_merge($parameters, $arguments);
         } elseif (is_string($arguments)) {
             $consumerRequest->setBody($arguments);
         }
         $consumerRequest->setParameters($parameters);
         if (count($httpHeaders)) {
             foreach ($httpHeaders as $k=>$v) {
                 $consumerRequest->setHeader($k, $v);
             }
         }
         $response = $consumerRequest->send();
         switch ($response->getStatus()) { case 304: return array( 'httpStatus' => 304, 'body' => null, ); break; case 403: throw new Dropbox_Exception_Forbidden('Forbidden. This could mean a bad OAuth request, or a file or folder already existing at the target location.'); case 404: return array( 'httpStatus' => 404, 'body' => null, ); case 507: throw new Dropbox_Exception_OverQuota('This dropbox is full'); }
         return array( 'httpStatus' => $response->getStatus(), 'body' => $response->getBody() );
     }
     public function getRequestToken()
     {
         $this->OAuth->getRequestToken(self::URI_REQUEST_TOKEN);
         $this->setToken($this->OAuth->getToken(), $this->OAuth->getTokenSecret());
         return $this->getToken();
     }
     public function getAccessToken()
     {
         $this->OAuth->getAccessToken(self::URI_ACCESS_TOKEN);
         $this->setToken($this->OAuth->getToken(), $this->OAuth->getTokenSecret());
         return $this->getToken();
     }
 }

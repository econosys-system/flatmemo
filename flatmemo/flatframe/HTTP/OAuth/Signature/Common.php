<?php
 require_once 'HTTP/OAuth.php'; abstract class HTTP_OAuth_Signature_Common extends HTTP_OAuth
 {
     public function getBase($method, $url, array $params)
     {
         if (array_key_exists('oauth_signature', $params)) {
             unset($params['oauth_signature']);
         }
         $croppedUrl = reset(explode('?', $url));
         $parts = array($method, $croppedUrl, HTTP_OAuth::buildHTTPQuery($params));
         $base = implode('&', HTTP_OAuth::urlencode($parts));
         $this->debug('Signing with base string: ' . $base);
         return $base;
     }
     public function getKey($consumerSecret, $tokenSecret = '')
     {
         $secrets = array($consumerSecret, $tokenSecret);
         $key = implode('&', HTTP_OAuth::urlencode($secrets));
         $this->debug('Signing with key: ' . $key);
         return $key;
     }
     abstract public function build($method, $url, array $params, $consumerSecret, $tokenSecret = '');
 }

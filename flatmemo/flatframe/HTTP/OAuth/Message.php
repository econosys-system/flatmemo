<?php
 require_once 'HTTP/OAuth.php'; abstract class HTTP_OAuth_Message extends HTTP_OAuth implements ArrayAccess, Countable, IteratorAggregate
 {
     protected static $oauthParams = array( 'consumer_key', 'token', 'token_secret', 'signature_method', 'signature', 'timestamp', 'nonce', 'verifier', 'version', 'callback', 'session_handle' );
     protected $parameters = array();
     public function getOAuthParameters()
     {
         $params = array();
         foreach (self::$oauthParams as $param) {
             if ($this->$param !== null) {
                 $params[$this->prefixParameter($param)] = $this->$param;
             }
         }
         ksort($params);
         return $params;
     }
     public function getParameters()
     {
         $params = $this->parameters;
         ksort($params);
         return $params;
     }
     public function setParameters(array $params)
     {
         foreach ($params as $name => $value) {
             $this->parameters[$this->prefixParameter($name)] = $value;
         }
     }
     public function getSignatureMethod()
     {
         if ($this->oauth_signature_method !== null) {
             return $this->oauth_signature_method;
         }
         return 'HMAC-SHA1';
     }
     public function __get($var)
     {
         $var = $this->prefixParameter($var);
         if (array_key_exists($var, $this->parameters)) {
             return $this->parameters[$var];
         }
         $method = 'get' . ucfirst($var);
         if (method_exists($this, $method)) {
             return $this->$method();
         }
         return null;
     }
     public function __set($var, $val)
     {
         $this->parameters[$this->prefixParameter($var)] = $val;
     }
     public function offsetExists($offset)
     {
         return isset($this->parameters[$this->prefixParameter($offset)]);
     }
     public function offsetGet($offset)
     {
         return $this->parameters[$this->prefixParameter($offset)];
     }
     public function offsetSet($offset, $value)
     {
         $this->parameters[$this->prefixParameter($offset)] = $value;
     }
     public function offsetUnset($offset)
     {
         unset($this->parameters[$this->prefixParameter($offset)]);
     }
     public function count()
     {
         return count($this->parameters);
     }
     public function getIterator()
     {
         return new ArrayIterator($this->parameters);
     }
     protected function prefixParameter($param)
     {
         if (in_array($param, self::$oauthParams)) {
             $param = 'oauth_' . $param;
         }
         return $param;
     }
 }

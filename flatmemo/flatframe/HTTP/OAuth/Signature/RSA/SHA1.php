<?php
 require_once 'HTTP/OAuth/Signature/Common.php'; require_once 'HTTP/OAuth/Exception/NotImplemented.php'; class HTTP_OAuth_Signature_RSA_SHA1
 {
     public function build($method, $url, array $params, $consumerSecret, $tokenSecret = '')
     {
         throw new HTTP_OAuth_Exception_NotImplemented;
     }
 }

<?php
 require_once 'HTTP/OAuth.php'; abstract class HTTP_OAuth_Signature
 {
     public static function factory($method)
     {
         $method = str_replace('-', '_', $method);
         $class = 'HTTP_OAuth_Signature_' . $method;
         $file = str_replace('_', '/', $class) . '.php';
         include_once $file;
         if (class_exists($class) === false) {
             throw new InvalidArgumentException('No such signature class');
         }
         $instance = new $class;
         if (!$instance instanceof HTTP_OAuth_Signature_Common) {
             throw new InvalidArgumentException('Signature class does not extend HTTP_OAuth_Signature_Common');
         }
         return $instance;
     }
 }

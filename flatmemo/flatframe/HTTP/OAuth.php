<?php
 abstract class HTTP_OAuth
 {
     protected static $logs = array();
     public static function attachLog(Log $log)
     {
         self::$logs[] = $log;
     }
     public static function detachLog(Log $detach)
     {
         foreach (self::$logs as $key => $log) {
             if ($log == $detach) {
                 unset(self::$logs[$key]);
             }
         }
     }
     public function log($message, $method)
     {
         foreach (self::$logs as $log) {
             $log->$method($message);
         }
     }
     public function debug($message)
     {
         $this->log($message, 'debug');
     }
     public function info($message)
     {
         $this->log($message, 'info');
     }
     public function err($message)
     {
         $this->log($message, 'err');
     }
     public static function buildHttpQuery(array $params)
     {
         if (empty($params)) {
             return '';
         }
         $keys = self::urlencode(array_keys($params));
         $values = self::urlencode(array_values($params));
         $params = array_combine($keys, $values);
         uksort($params, 'strcmp');
         $pairs = array();
         foreach ($params as $key => $value) {
             $pairs[] = $key . '=' . $value;
         }
         return implode('&', $pairs);
     }
     public static function urlencode($item)
     {
         static $search = array('+', '%7E');
         static $replace = array('%20', '~');
         if (is_array($item)) {
             return array_map(array('HTTP_OAuth', 'urlencode'), $item);
         }
         if (is_scalar($item) === false) {
             return $item;
         }
         return str_replace($search, $replace, rawurlencode($item));
     }
     public static function urldecode($item)
     {
         if (is_array($item)) {
             return array_map(array('HTTP_OAuth', 'urldecode'), $item);
         }
         return rawurldecode($item);
     }
 }

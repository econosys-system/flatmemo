<?php
 function Dropbox_autoload($className)
 {
     if (strpos($className, 'Dropbox_')===0) {
         include dirname(__FILE__) . '/' . str_replace('_', '/', substr($className, 8)) . '.php';
     }
 } spl_autoload_register('Dropbox_autoload');

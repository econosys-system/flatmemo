<?php
 function smarty_modifier_kihou_hatena($string='')
 {
     if ($string=='') {
         return $string;
     }
     $string = str_replace("\r\n", "\n", $string);
     $string = str_replace("\r", "\n", $string);

		 return HatenaSyntax::render($string);
 }

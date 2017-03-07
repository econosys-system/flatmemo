<?php
 function smarty_modifier_kihou_flatmemo($string='')
 {
     if ($string=='') {
         return $string;
     }
     $string = str_replace("\r\n", "\n", $string);
     $string = str_replace("\r", "\n", $string);
     $out = '';
     $ar = preg_split("/\n/", $string);
     foreach ($ar as $k => $v) {
         if (preg_match("{<img }", $v)) {
         } elseif (preg_match("{<a href=}", $v)) {
         } else {
             $v = preg_replace_callback('/\[?(https?:\/\/[^:^ ]+)(:title=([^\]]*))?\]?/', create_function('$m', '
			return "<a href=\"{$m[1]}\" target=\"_blank\" >" 
				  . (isset($m[3]) ? $m[3] : $m[1] )
				  . "</a>";
			'), $v);
         }
         $out .= $v."<br />\n";
     }
     return $out;
 }

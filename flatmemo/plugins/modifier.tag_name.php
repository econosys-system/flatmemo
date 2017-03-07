<?php
 function smarty_modifier_tag_name($tag_id='', $tag_master_loop)
 {
     if ($tag_id=='') {
         return;
     }
     foreach ($tag_master_loop as $k => $v) {
         if ($v['tag_id'] == $tag_id) {
             return htmlspecialchars($v['tag_name']);
         }
     }
 }

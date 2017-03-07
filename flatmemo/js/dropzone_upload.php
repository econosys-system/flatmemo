<?php
$ds = DIRECTORY_SEPARATOR;
$store_folder = dirname(__FILE__) . $ds . '..' . $ds . 'data' . $ds . 'tmp' . $ds;
// echo $store_folder;
if (!empty($_FILES)) {
	$temp_file = $_FILES['file']['tmp_name'];
	$ext = get_ext($_FILES['file']['name']);	// ファイル名から拡張子を取得
	$target_file = basename($_FILES['file']['tmp_name'].$ext);
	$ja_file     = $_FILES['file']['name'];
	$target_file_fullpath =  $store_folder . $target_file;
	move_uploaded_file($temp_file, $target_file_fullpath);
	echo "{$target_file}\t{$ja_file}";		// 英語ファイル名[TAB]日本語ファイル名　を返す
}

//========== _get_ext mime-typeを調べて拡張子を返す
function get_ext($file_name){
	$p = pathinfo($file_name);
	if ( isset($p['extension']) ){
		return '.'.$p['extension'];
	}
	else{
		return '';
	}
}

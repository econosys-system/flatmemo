<?php

// Version 1.0	とりあえず作成
// Version 1.1	delete_tmpを追加
// Version 1.11	delete_tmpの引数が指定無しの場合、newしたときのディレクトリをセットするよう変更
// Version 2.0  データ構造を変更。ファイル名、クラス名 もそれに伴い変更
// Version 2.01 move 時に chmod するよう変更
// Version 2.02 拡張子をオリジナルファイル名から取得するよう変更
// Version 2.03 アップロード上限値を超えたときに die するように変更
// Version 2.04 Usage追加
// Version 2.10 PHP7対応


/* Usage

<form name="FM" id="FM" method="post" enctype="multipart/form-data" action="{$_program_uri}" onsubmit="accessing('accessing');" >


====== DropZone を使う場合 ======

<link href="./css/dropzone.css" type="text/css" rel="stylesheet" />
<script src="./js/dropzone.js"></script>
<script src="./js/dropzone_config.js"></script>

<div id="image_drop_area" >ここにアップロードファイルをドロップ<br>（またはクリックするとファイル選択が開きます）</div>

加圧
===============================

*/

class exfileupload2
{
    public $version = '2.10';
    public $notice  = '本プログラムは econosys system の著作物です。著作者の許可無き改変、別システムでの使用を禁止します。copyright (c) econosys system http:///econosys-system.com/';
    public $tmp_path;
    public $filelist = array();

    //========== コンストラクタ
    public function __construct($tmp_path)
    {
        $this->tmp_path=$tmp_path;
    }



    //========== ファイルの移動
    public function move()
    {
        foreach ($_FILES as $key => $value) {
            if ($_FILES[$key]['error']==UPLOAD_ERR_INI_SIZE) {
                $this->filelist["$key"]['name']='';
                $this->filelist["$key"]['uploaded_name']='';
                $this->filelist["$key"]['uploaded_basename']='';
                $this->filelist["$key"]['error']="exfileupload2 ERROR : can not upload over ".ini_get('upload_max_filesize')." Bytes";
                die($this->filelist["$key"]['error']);
            } elseif (is_uploaded_file($_FILES[$key]['tmp_name'])) {
                // $ext = $this->_get_ext($_FILES[$key]['type']);	// mimeタイプから拡張子を取得
                $ext = $this->_get_ext($_FILES[$key]['name']);    // ファイル名から拡張子を取得
                // 設定したテンポラリディレクトリに移動
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $this->tmp_path.'/'.basename($_FILES[$key]['tmp_name'].$ext))) {
                    $this->filelist["$key"]['name']=$_FILES[$key]['name'];
                    $this->filelist["$key"]['type']=$_FILES[$key]['type'];
                    $this->filelist["$key"]['size']=$_FILES[$key]['size'];
                    $this->filelist["$key"]['ext']=$ext;
                    $this->filelist["$key"]['uploaded_name']=$this->tmp_path.'/'.basename($_FILES[$key]['tmp_name'].$ext);
                    $this->filelist["$key"]['uploaded_basename']=basename($_FILES[$key]['tmp_name'].$ext);
                    chmod($this->tmp_path.'/'.basename($_FILES[$key]['tmp_name'].$ext), 0666);
                } else {
                    die('[ ERROR: can not move'.$this->tmp_path.' ]');
                }
            }
        }
        return $this->filelist;
    }



    //========== _get_ext mime-typeを調べて拡張子を返す
    public function _get_ext($name)
    {
        if (preg_match('/jpeg/', $name)) {
            return '.jpg';
        } elseif (preg_match('/jpg/', $name)) {
            return '.jpg';
        } elseif (preg_match('/gif/', $name)) {
            return '.gif';
        } elseif (preg_match('/png/', $name)) {
            return '.png';
        } elseif (preg_match('/\.([a-zA-z0-9]+)$/', $name, $result)) {
            $ext = strtolower($result[1]);
            return ".{$ext}";
        }
        //else if ( $result=preg_replace('/(.+)\//', '', $name) ){ return '.'.$result; }
    }



    //========== delete_tmp：古い（1日以上前の）テンポラリファイルを削除する
    public function delete_tmp($dirpath='')
    {
        if ($dirpath=='') {
            $dirpath=$this->tmp_path;
        }
        $deleted_list=array();
        $dir = dir($dirpath);
        while (($file=$dir->read()) !== false) {
            //print $file."<br>";
            if (preg_match('/^\./', $file)) {
                continue;
            }    // ディレクトリと .始まりのファイルは削除しない
            else {
                $filetime=filemtime("$dirpath/$file");
                $nowtime=time();
                $int_f=intval($filetime);
                $int_n=intval($nowtime);
                $sa=($int_n-$int_f);            // 現在の日付との差
                if ($sa > (60*60*24)) {            // 24H以上前のファイルは削除
                    //print $file."を削除します";
                    array_push($deleted_list, $file);
                    if (! unlink("$dirpath/$file")) {
                        die("ファイル[".$dirpath."/".$file."]の削除に失敗しました");
                    }
                }
            }
        }
        return $deleted_list;
    }



    //========== dump
    public function dump($data)
    {
        mb_convert_variables('EUC', 'auto', $data);
        print "<pre>";
        print_r($data);
        print "</pre>";
    }
}


#flatmemo version2.13 説明書

flatmemo Copyright 2002-2017 (c) econosys system [http://econosys-system.com/]()  
flatmemo page [http://econosys-system.com/freesoft/flatmemo.html]()  


##● flatmemoインストール方法

####1. アーカイブを解凍すると【flatmemo】というディレクトリが作成されていますので、ディレクトリごとFTPソフトでアップロードして下さい。

####2. アップロードしたflatmemoディレクトリの中に移動し、
ディレクトリ【data】【smarty_cache】【templates_c】のパーミッションを【777】にします。  
ファイル【textdb.yml.cache】のパーミッションを【666】にします。  

-----

>  |-- config.cgi  
>  |-- **[data]**						( パーミッション 777 )  
>  |-- [flatframe]  
>  |-- flatframe.php  
>  |-- [g]  
>  |-- [j]  
>  |-- memo.php  
>  |-- memo_admin.php  
>  |-- [plugins]  
>  |-- **[smarty_cache]**				( パーミッション 777 )  
>  |-- [templates]  
>  |-- **[templates_c]**				( パーミッション 777 )  
>  |-- textdb.yml  
>  |-- **textdb.yml.cache**				( パーミッション 666 )  

-----



####3. ブラウザから起動します。ブラウザのアドレス欄に（ http://あなたのサーバー/flatmemo/memo.php ）と入力し起動します。

####4.【カテゴリの編集】を押して新しいカテゴリを作成します。（初期パスワードは password です）

####5.後は好きなデータを登録していって下さい。

##● カスタマイズ方法

####1. ファイル【config.cgi】を任意のテキストエディタで編集します。（拡張子 .cgi ですが中身はただのテキストファイルです。）

####2. ディレクトリ【templates】の中の *.html が Smarty形式のテンプレートファイルです。自由にカスタマイズしてください。

##● ライセンス

本スクリプトは「フリーソフトです」  
ライセンスは修正BSDライセンスとします  
「著作権表示」さえ変更しなければ自由に使用、改変、再配布など行えます。  
商用利用もOKです。  
flatmemoに関するご要望ご質問  
  
flatmemoに関するご要望ご質問は [https://econosys-system.com/contact.php]()
までお願いいたします。
 

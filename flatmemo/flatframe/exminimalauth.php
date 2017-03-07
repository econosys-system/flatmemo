<?php

// 2016 econosys system     http://econosys-system.com/
// Version 0.1
// Version 0.2 細かいbug-fix

// 使用するCDN
// https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js
// https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha256.js

/* Usage
■ 認証を入れたいメソッドの中で下記のコードを実行
require_once "exminimalauth.php";
$ma = new exminimalauth( array(
  'admin_password' => $this->_ff_config['admin_password'] ,
));
$ma->login( "{$this->q['_program_uri']}?cmd=login_submit", (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );


■ からのメソッドを作成しておく
public function exminimalauth_login_submit(){}

*/


class exminimalauth
{
    public $exminimalauth_login_flag = false;
    public $admin_password = '';

    public function __construct($option=array())
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($option['admin_password'])) {
            $this->admin_password = $option['admin_password'];
      // $this->dump( $this->admin_password );
        }
    }

    public function exminimalauth_view_login($flag='')
    {
        $err_mess = '';
        if (strcmp($flag, 'error')==0) {
            $err_mess = 'ログインエラーです。';
        }
        print <<< DOC_END
<html>
<head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha256.js"></script>
<script>
function make_hidden(e,n,d){var m=document.createElement("input");m.type="hidden",m.name=e,m.value=n,d?document.forms[d].appendChild(m):document.forms[0].appendChild(m)}
</script>
<style type="text/css">
*{
  font-size:12px;
  font-family: serif;
}
</style>
</head>
<body>
{$err_mess}
<form method="post" action="{$_SESSION['exminimalauth_login_url']}" onsubmit="return false;" >
<h1>パスワードを入力してください。</h1>
<input type="password" id="in_text" name="in_text">
<input type="button" value="送信" onclick="var v = document.getElementById('in_text').value; v = CryptoJS.SHA256(v); make_hidden('apb',v); document.getElementById('in_text').value=''; this.form.submit();">
</form>
</body>
</html>
DOC_END;
    }



    public function exminimalauth_login_submit()
    {
        $password_sha256 = hash('sha256', $this->admin_password);
    // $this->dump( $this->admin_password );
    // $this->dump( $password_sha256 );
    // $this->dump( $_POST['apb']  );

    if (strcmp($password_sha256, $_POST['apb'])==0 && (strcmp($_POST['apb'], '') != 0)) {
        $_SESSION['exminimalauth_login_flag']  = true;
        header("Location: {$_SESSION['exminimalauth_jump_to']}");
        exit();
    } else {
        $this->exminimalauth_view_login('error');
        exit();
    }
    }



    public function exminimalauth_login($login_url, $jump_to)
    {
        if (@$_SESSION['exminimalauth_login_flag']  == true) {
            return;
        } elseif (preg_match('/exminimalauth_login_submit/', $_SERVER["REQUEST_URI"])) {
            $this->exminimalauth_login_submit();
        } else {
            $_SESSION['exminimalauth_login_url']  = $login_url;
            $_SESSION['exminimalauth_jump_to']    = $jump_to;
            $_SESSION['exminimalauth_login_flag'] = false;
            $this->exminimalauth_view_login($login_url);
            exit();
        }
    }


    public function dump($data)
    {
        print "\n".'<pre style="text-align:left;">'."\n";
        print_r($data);
        print "</pre>\n";
    }
}

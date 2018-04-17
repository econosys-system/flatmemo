// flatmemo.js
// version 2.04

var accessing_id_name = 'accessing';		// デフォルトのid名前
var changeFlg         = false;

$(document).ready( function(){
	var u = location.href;
	if ( u.match(/memo_admin.php\?cmd=login/) ){ $('#password').focus(); }
	else if( u.match(/memo_admin.php\?cmd=data_(add|edit)_input/) ){
		$('#title_name').focus();
	}
	else{ $('#q').focus(); }

    $(window).on('beforeunload', function() {
        if (changeFlg) {
          return "メモ入力画面を閉じようとしています。\n入力中の情報がありますがよろしいですか？";
        }
    });

    //フォームの内容が変更されたらフラグを立てる
    $('form[name=FM] input[name=title_name]').change(function() {
        changeFlg = true;
    });
    $('form[name=FM] textarea[name=text_name]').change(function() {
        changeFlg = true;
    });

    // フォーム送信時はアラートOFF
    $('form[name=FM]').submit(function(){
        changeFlg = false;
    });

});



function change_watch_cheme( theme ){
	alert(theme);
	$("head").append("<link>");
	css = $("head").children(":last");
	css.attr({
			rel: "stylesheet",
			type: "text/css",
			href: './bootstrap/bootstrap.' + theme + '.min.css'
	});
}



function _closet(){
    var doc_root = _doc_root();
	location.href = doc_root + 'memo.php/closet';
}



function _draft(){
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=draft';
}



function _cat_e(){
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=category_edit_input'
}



function _tag_e(){
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=tag_edit_input'
}



function _doc_root(){
  var u = location.href;
  result = new Array();
  result = u.match(/^http.+?memo(_admin)?\.php/);
  if (result){
	  doc_root = result[0].replace(/memo(_admin)?\.php/,'');
	  return doc_root;
  }
  else if(u.match(/^http.+?data\/archives/)){
	  result = u.match(/^http.+?data\/archives/);
	  doc_root = result[0].replace(/data\/archives/,'');
	  return doc_root;
  } else{
      doc_root = memo_adi_uri.replace(/memo(_admin)?\.php/,'');
      return doc_root;
  }

//  alert(doc_root);
  return doc_root;
}



function _a(category_id){
	var back_uri = document.URL;
	var e_uri = encodeURIComponent(back_uri);
    var doc_root = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=data_add_input&category_id=' + category_id + '&back_uri='+e_uri
}



function _e(id){
	var back_uri = document.URL;
	var e_uri = encodeURIComponent(back_uri);
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=data_edit_input&data_id='+id+'&back_uri='+e_uri
}
function _d(id){
	var back_uri = document.URL;
		var e_uri = encodeURIComponent(back_uri);
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo_admin.php?cmd=data_delete_confirm&data_id='+id+'&back_uri='+e_uri
}
function _t(id){
	var a = document.getElementById('h'+id).innerText;
	a = a.replace(/\r\n/g, "\n");
	a = a.replace(/\n/g, "");
	var t = encodeURIComponent(a + '｜made in japan');
	var p = encodeURIComponent('http://memo.flatsystems.net/memo/memo.php'+'/archive/'+id);
	window.open('http://twitter.com/share?url='+p+'&text='+t, "", "width=550,height=410,location=no").focus();
}



function _f(id,file){
    var doc_root  = _doc_root();
	location.href = doc_root + 'memo.php?cmd=download&data_id='+id+'&file='+file;
}



function _v_atch(id){
	$('#'+id).slideToggle();
}



function _v_menu(id){
	$('#'+id).slideToggle();
	}

//alert( location.href );


eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 4=0;3 6=0;c d(){3 2=0;8=e f();2=8.g()/h;2=i.j(2);9(4==0){4=1;6=2;7 a}b 9((2-6)>=5){7 a}b{7 k}}',21,21,'||nowtime|var|sent||senttime|return|myD|if|true|else|function|notrepeat|new|Date|getTime|1000|Math|floor|false'.split('|'),0,{}))


// event-unload
jQuery(window).unload(function(){
    hide_accessing();
});



// accessing
function accessing(id_name){

	if ( id_name ){ accessing_id_name=id_name; }

	if (! jQuery('#'+accessing_id_name) ){ alert('accessing: not found id ['+accessing_id_name+']'); return false;}

	rt_array = EcoGetVersion();
	useos      = rt_array[0];
	usebrowser = rt_array[1];

	if ( useos=='iPhone'){
    alert('ihpne');
		setTimeout( '_accessing('+accessing_id_name+')', 1 );
    }
	else if ( usebrowser=='Safari_IE' || usebrowser=='Opera_IE' || usebrowser=='Firefox_NN'){
		jQuery('#'+accessing_id_name).show();
	}
	else{
		setTimeout( '_accessing('+accessing_id_name+')', 10 );
	}
}

//
function _accessing(){
	jQuery('#'+accessing_id_name).show();
}

//
function hide_accessing(){
	if (jQuery('#'+accessing_id_name)){
		jQuery('#'+accessing_id_name).hide();
	}
}

//  EcoGetVersion
function EcoGetVersion(){
	//OS
	if (navigator.userAgent.indexOf('Mac',0) != -1){ useos='Mac';}
	else if (navigator.userAgent.indexOf('Win',0) != -1){ useos='Win';}
	else if (navigator.userAgent.indexOf('iPhone',0) != -1){ useos='iPhone';}
	else {useos='other'; }

	//browser
	if (navigator.userAgent.indexOf('MSIE 3.',0) != -1){usebrowser='IE3';}
	else if (navigator.userAgent.indexOf('MSIE 4.',0) != -1){usebrowser='IE4';}
	else if (navigator.userAgent.indexOf('MSIE 5.',0) != -1){usebrowser='IE5';}
	else if (navigator.userAgent.indexOf('MSIE 6.',0) != -1){usebrowser='IE6';}
	else if (navigator.userAgent.indexOf('MSIE 7.',0) != -1){usebrowser='IE7';}
	else if (navigator.userAgent.indexOf('Netscape/7.',0) != -1){usebrowser='NN7';}
	else if (navigator.userAgent.indexOf('Netscape/6.',0) != -1){usebrowser='NN6';}
	else if (navigator.userAgent.indexOf('Mozilla/4.',0) != -1){usebrowser='NN4';}
	else if (navigator.userAgent.indexOf('Safari',0) != -1){usebrowser='Safari_IE';}
	else if (navigator.userAgent.indexOf('Firefox',0) != -1){usebrowser='Firefox_NN';}
	else if (navigator.userAgent.indexOf('Gecko',0) != -1){usebrowser='other_Gecko_NN';}
	else if (navigator.userAgent.indexOf('Opera',0) != -1){usebrowser='Opera_IE';}
	else {usebrowser='other';}

	return [useos,usebrowser];
}

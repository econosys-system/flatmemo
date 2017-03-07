$(document).ready(function() {

$('#image_drop_area').dropzone({
	url                          : './js/dropzone_upload.php',
	paramName                    : 'file',
	maxFilesize                  : 999 , //MB
	addRemoveLinks               : true ,
	previewsContainer            : '#preview_area' ,
	thumbnailWidth               : 50 , //px
	thumbnailHeight              : 50 , //px
	dictRemoveFile               :'[×]' ,
	dictCancelUpload             :'キャンセル' ,
	dictCancelUploadConfirmation : 'アップロードをキャンセルします。よろしいですか？' ,
		uploadprogress:function(file, progress, size){
		file.previewElement.querySelector("[data-dz-uploadprogress]").style.width = "" + progress + "%";
	},
	success:function(file, rt, xml){
		file.previewElement.classList.add("dz-success");
		make_hidden('dropzone_files[]', file.xhr.response, 'FM');
		 $(file.previewElement).find('.dz-success-mark').show();
	},
	processing: function(){
		$('#form_submit').attr("value", 'ファイルアップロード中 ... ');
		$('#form_submit').attr("disabled", true);
	} ,
	queuecomplete: function(){
		$('#form_submit').attr("value", '送信');
		$('#form_submit').attr("disabled", false);
		if ( $("#auto_submit_flag").prop('checked')) {
			document.FM.submit();
		}
	} ,
	dragover: function( arg ){
		$('#' + arg.srcElement.id).addClass('dragover');
	} ,
	dragleave: function( arg ){
		$('#' + arg.srcElement.id).removeClass('dragover');
	} ,
	drop: function( arg ){
		$('#' + arg.srcElement.id).removeClass('dragover');
	} ,
	error:function(file, _error_msg){
		var ref;
		(ref = file.previewElement) != null ? ref.parentNode.removeChild(file.previewElement) : void 0;
	},
	removedfile:function(file){
		delete_hidden('dropzone_files[]',file.xhr.response);
		var ref;
		(ref = file.previewElement) != null ? ref.parentNode.removeChild(file.previewElement) : void 0;
	} ,
	canceled:function(arg){
	} ,
	previewTemplate : "\
	<div class=\"dz-preview dz-file-preview\">\n\
	  <div class=\"dz-details\">\n\
	    <div class=\"clearfix\">\n\
	      <img class=\"dz-thumbnail\" data-dz-thumbnail>\n\
	      <div class=\"dz-success-mark\" style=\"display:none;\"><i class=\"fa fa-2x fa-check-circle\"></i></div>\n\
	    </div>\n\
	    <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n\
	    <div>\n\
	      <div class=\"dz-filename\"><span data-dz-name></span></div>\n\
	      <div class=\"dz-my-separator\"> / </div>\n\
	      <div class=\"dz-size\" data-dz-size></div>\n\
	      <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n\
	    </div>\n\
	  </div>\n\
	</div>\n\
	"


});

});



// make_hidden : hiddenを作成する : Version 1.1
function make_hidden( name, value, formname ){
    var q = document.createElement('input');
    q.type = 'hidden';
    q.name = name;
    q.value = value;
    if (formname){ document.forms[formname].appendChild(q); }
    else{ document.forms[0].appendChild(q); }
}

// delete_hidden : hiddenを削除する : Version 1.1
function delete_hidden( name, value, formname ){
	var dom_obj_array = window.document.getElementsByName(name);
	for (var i=0; i<dom_obj_array.length; i++){
		if ( dom_obj_array[i].value === value ){
			element = dom_obj_array[i];
			element.parentNode.removeChild(element);
		}
	}
}

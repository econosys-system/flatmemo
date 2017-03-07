<?php

// flatmemo
// copyright (c) econosys system     http://econosys-system.com/
// version 2.00 Bootswatch導入
// version 2.01 細かいbug-fix
// version 2.03 Dropzone.js導入
// version 2.04 minimalauth導入
// version 2.10 PHP7対応

require_once "./flatframe.php";
require_once "./flatframe/textdb.php";
require_once "vendor/autoload.php";

class ff_memo_admin extends flatframe
{


    //==========
    public function __construct($configfile)
    {
        $this->_ff_configfile=$configfile;
    }



    //==========
    public function setup()
    {
        $this->rootdir    = '.';
        $this->run_modes  = array(
            'login'                 => 'do_login' ,
            'login_confirm'         => 'do_login_confirm' ,

            'draft'                 => 'do_draft' ,

            'category_add_submit'    => 'do_category_add_submit' ,
            'category_edit_input'    => 'do_category_edit_input' ,
            'category_edit_submit'   => 'do_category_edit_submit' ,
            'category_delete_submit' => 'do_category_delete_submit' ,
            'category_turn_input'    => 'do_category_turn_input' ,
            'category_turn_submit'   => 'do_category_turn_submit' ,

            'tag_edit_input'     => 'do_tag_edit_input' ,
            'tag_edit_submit'    => 'do_tag_edit_submit' ,
            'tag_delete_submit'  => 'do_tag_delete_submit' ,

            'data_add_input'        => 'do_data_add_input' ,
            'data_add_submit'       => 'do_data_add_submit' ,
            'data_edit_input'       => 'do_data_edit_input' ,
            'data_edit_submit'      => 'do_data_edit_submit' ,
            'data_delete_confirm'   => 'do_data_delete_confirm' ,
            'data_delete_submit'    => 'do_data_delete_submit' ,

            'write_process'          => 'do_write_process' ,
            'rewrite_all_process'    => 'do_rewrite_all_process' ,

            'appearance_input'           => 'do_appearance_input' ,
            'appearance_submit'          => 'do_appearance_submit' ,
            'smarty_cache_delete_submit' => 'do_smarty_cache_delete_submit' ,

        );
    }



    //========== app_prerun
    public function app_prerun()
    {
        $this->_set_session_param();
        if (@$this->_ff_config['flatmemo_all_password']) {
            require_once "exminimalauth.php";
            $ma = new exminimalauth(array(
                'admin_password' => $this->_ff_config['admin_password'] ,
            ));
            $ma->exminimalauth_login("{$this->q['_program_uri']}?cmd=exminimalauth_login_submit", (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        }

        $this->_ff_config['root_uri'] = $this->_make_uri($this->q['_program_uri'], './');
        $this->_ff_config['root_uri'] = preg_replace("/\/$/", '', $this->_ff_config['root_uri']);
        if (! defined('__DIR__')) {
            define('__DIR__', dirname(__FILE__));
        }
        $this->_get_appearance();

        if (strcmp($this->q['cmd'], 'login')==0) {
        } elseif (strcmp($this->q['cmd'], 'login_confirm')==0) {
        } elseif ($this->_ff_config['admin_use_password']==1) {
            $this->_session_check();
        }

        $this->_ff_config['memo_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['memo_file']);
        $this->_ff_config['admin_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['admin_file']);
        // $this->_ff_config['image_uri'] = $this->_make_uri($this->q['_program_uri'],$this->_ff_config['image_dir']);
        $this->_ff_config['data_file_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['data_file_dir']);
        $this->_ff_config['data_file_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['data_file_dir']);
        // $this->_ff_config['script_uri'] = $this->_make_uri($this->q['_program_uri'],$this->_ff_config['script_dir']);
        $this->_ff_config['icon_uri']  = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['icon_dir']);
        $this->template->assign(array("config" => $this->_ff_config));
    }


    //========== exminimalauth_login_submit
    public function exminimalauth_login_submit()
    {
    }



    //========== app_prerun
    public function app_postrun()
    {
    }



    //========== do_draft
    public function do_draft()
    {

        // テンプレート
        $template_file = 'temp_category.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'draft_flag' => 'on'
        ), 0, 99999);

        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(
        ), 0, 9999);
        $this->template->assign(array("tag_master_loop" => $tag_loop));

        // config
        $this->_ff_config['memo_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['memo_file']);
        $this->_ff_config['admin_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['admin_file']);
        $this->_ff_config['image_uri']   = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['image_dir']);
        $this->_ff_config['data_file_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['data_file_dir']);
        $this->_ff_config['script_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['script_dir']);
        $this->_ff_config['icon_uri']  = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['icon_dir']);
        $this->_ff_config['image_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['image_dir']);
        $this->_ff_config['admin_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['admin_file']);
        $this->template->assign(array("config" => $this->_ff_config));

        $this->template->assign($this->q);
        $this->template->assign(array("data_loop" => $data_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== do_category_add_submit
    public function do_category_add_submit()
    {

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_dt->insert(array(
            'category_name' => $this->q['category_name'] ,
        ));

        // dropbox
        if ($this->_ff_config['backup_on_dropbox']==1) {
            $this->_backup_to_dropbox('category');
        }

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();
        header("Location:{$this->_ff_config['admin_uri']}?cmd=category_edit_input");
    }


    //========== do_category_edit_input
    public function do_category_edit_input()
    {

        // テンプレート
        $template_file = 'temp_admin_category_input.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
        ), 0, 9999);

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== do_category_edit_submit
    public function do_category_edit_submit()
    {
        require_once "./flatframe/exfileupload2.php";
        $file = new exfileupload2($this->_ff_config['upload_tmp_dir']);
        $file->delete_tmp();
        $filelist = $file->move();

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_hash = $category_dt->select_one(array(
            'category_id'    => $this->q['category_id'] ,
        ));

        $icon_file_name = $category_hash['icon_file_name'];
        if (isset($filelist['icon_attach'])) {
            umask(0000);
            copy($filelist['icon_attach']['uploaded_name'], "{$this->_ff_config['icon_dir']}/{$this->q['category_id']}{$filelist['icon_attach']['ext']}");
            $icon_file_name = "{$this->q['category_id']}{$filelist['icon_attach']['ext']}";
        }

        if (! isset($this->q['view_mode'])) {
            $this->q['view_mode']='';
        }

        $category_loop = $category_dt->update(array(
            'category_id'    => $this->q['category_id'] ,
            'category_name'  => $this->q['new_category_name'] ,
            'icon_file_name' => $icon_file_name ,
            'view_mode'      => $this->q['view_mode'] ,
            'desc_name'      => $this->q['desc_name'] ,
        ));

        // dropbox
        if ($this->_ff_config['backup_on_dropbox']==1) {
            $this->_backup_to_dropbox('category');
        }

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();

        // html archive
        if (@$this->_ff_config['use_html_archive']==1 && @$this->_ff_config['recreate_html_archive']==1) {
            $this->_create_archive_jump('category');
            exit();
        }

        header("Location:{$this->_ff_config['admin_uri']}?cmd=category_edit_input");
    }



    //========== do_category_delete_submit
    public function do_category_delete_submit()
    {

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');

        $category_hash = $category_dt->select_one(array(
            'category_id'   => $this->q['category_id'] ,
        ));

        if ($category_hash['icon_file_name']) {
            unlink("{$this->_ff_config['icon_dir']}/{$category_hash['icon_file_name']}");
        }

        $category_dt->delete(array(
            'category_id'   => $this->q['category_id'] ,
        ));

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->delete(array(
            'category_id'   => $this->q['category_id'] ,
        ));

        // dropbox
        if ($this->_ff_config['backup_on_dropbox']==1) {
            $this->_backup_to_dropbox('category');
        }

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();

        // html archive
        if (@$this->_ff_config['use_html_archive']==1) {
            $this->_create_archive_jump('category');
            exit();
        }

        header("Location:{$this->_ff_config['admin_uri']}?cmd=category_edit_input");
    }



    //========== do_category_turn_input
    public function do_category_turn_input()
    {

        // テンプレート
        $template_file = 'temp_admin_category_turn_input.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
            'view_mode' => ''
        ), 0, 9999);
        $category_loop2 = $category_dt->select(array(
            'view_mode' => 'off'
        ), 0, 9999);

        $category_loop = array_merge($category_loop, $category_loop2);

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== do_category_turn_submit
    public function do_category_turn_submit()
    {
        if (! isset($this->q['list'])) {
            header("Location:{$this->q['_program_uri']}?cmd=category_turn_input");
        }
        if (! is_array($this->q['list'])) {
            header("Location:{$this->q['_program_uri']}?cmd=category_turn_input");
        }

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');

        foreach (array_reverse($this->q['list']) as $v) {
            $category_dt->move_top(array(
                'category_id' => $v ,
            ));
        }
        header("Location:{$this->q['_program_uri']}?cmd=category_turn_input");
    }


    //========== do_tag_edit_input
    public function do_tag_edit_input()
    {

        // テンプレート
        $template_file = 'temp_admin_tag_input.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(
        ), 0, 9999);

        // data
        $tag_count_loop = array();

        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
        ), 0, 99999);
        foreach ($data_loop as $k => $v) {
            if (isset($v['tag_id_loop'])) {
                foreach ($v['tag_id_loop'] as $tv) {
                    if (isset($tag_count_loop[$tv])) {
                        $tag_count_loop[$tv]++;
                    } else {
                        $tag_count_loop[$tv]=1;
                    }
                }
            }
        }
        $this->template->assign($this->q);
        $this->template->assign(array("tag_loop" => $tag_loop));
        $this->template->assign(array("tag_count_loop" => $tag_count_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== do_tag_edit_submit
    public function do_tag_edit_submit()
    {
        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->update(array(
            'tag_id'   => $this->q['tag_id'] ,
            'tag_name' => $this->q['new_tag_name'] ,
        ));
        header("Location:{$this->q['_program_uri']}?cmd=tag_edit_input");
    }



    //========== do_tag_delete_submit
    public function do_tag_delete_submit()
    {

        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->delete(array(
            'tag_id'   => $this->q['tag_id'] ,
        ));

        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
        ), 0, 99999);
        foreach ($data_loop as $k => $v) {
            if (isset($v['tag_id_loop'])) {
                if (in_array($this->q['tag_id'], $v['tag_id_loop'])) {
                    // $this->dump($v);
                    $new_tag_loop = array();
                    foreach ($v['tag_id_loop'] as $tv) {
                        if ($tv==$this->q['tag_id']) {
                        } else {
                            array_push($new_tag_loop, $tv);
                        }
                    }
                    $new_tag_id = join(',', $new_tag_loop);

                    $data_dt->update(array(
                        'data_id'   => $v['data_id'] ,
                        'tag_id'    => $new_tag_id ,
                    ));
                }
            }
        }

        header("Location:{$this->q['_program_uri']}?cmd=tag_edit_input");
    }



    //========== do_data_add_input
    public function do_data_add_input()
    {

        // テンプレート
        $template_file = 'temp_admin_data_input.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        $data_hash = array();
        $data_hash['category_id'] = $this->q['category_id'];
        $data_hash['kihou_flag'] = 'autolink';

        if (isset($this->_ff_config['default_kihou'])) {
            $data_hash['kihou_flag'] = $this->_ff_config['default_kihou'];
        }

        // uploadify用の ランダム文字列, ファイル番号 生成
        $rnd_key = md5(time());
        $file_start_no = 1;
        $this->template->assign(array("rnd_key" => $rnd_key));

        $this->q['hidden'] = $this->make_hidden();
        $this->q['hidden'] .= <<< DOC_END
<input type="hidden" name="cmd" value="data_add_submit" />
<input type="hidden" name="rnd_key" value="{$rnd_key}" />
<input type="hidden" name="file_start_no" value="{$file_start_no}" />
DOC_END;


        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
        ), 0, 9999);
        $this->template->assign(array("category_master_loop" => $category_loop));

        // データにcategory_name追加
        $category_hash = $category_dt->select_one(array(
            'category_id' => $this->q['category_id'] ,
        ));
        $this->q['category_name'] = $category_hash['category_name'];
        $this->q['icon_file_name'] = $category_hash['icon_file_name'];

        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(), 0, 9999);
        $this->template->assign(array("tag_master_loop" => $tag_loop));

        // this_category_tag_loop
        $this_category_tag_loop = $this->_get_this_category_tag($this->q['category_id']);
        $this->template->assign(array("this_category_tag_loop" => $this_category_tag_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $html = $this->template->fetch($template_file, $template_id);

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        // fillin
        require_once './flatframe/FillInForm.class.php';
        $fill = new HTML_FillInForm();
        $output =$fill->fill(array(
           'scalar' => $html,
           'fdat'   => $data_hash ,
        ));
        // 出力
        print $output;
    }



    //========== do_data_add_submit
    public function do_data_add_submit()
    {
        $ajax_filelist = $this->_check_attach_file($this->q['rnd_key']);

        require_once "./flatframe/exfileupload2.php";
        $file = new exfileupload2($this->_ff_config['upload_tmp_dir']);
        $file->delete_tmp();

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');

        $this->q['tag_id']       = $this->_create_tag_id_from_name($this->q['tag_name']);
        require_once "./flatframe/exdate.php";
        $t = new exdate();
        list($year, $month, $day, $hour, $min, $sec) = $t->now();
        $this->q['touroku_date'] = "$year$month$day$hour$min$sec";
        $this->q['koushin_date'] = "$year$month$day$hour$min$sec";

        $this->q['autolink_flag'] = $this->_create_autolink_flag($this->q['kihou_flag']);
        $this->q['hatena_flag']   = $this->_create_hatena_flag($this->q['kihou_flag']);
        $this->q['markdown_flag'] = $this->_create_markdown_flag($this->q['kihou_flag']);
        if (! isset($this->q['draft_flag'])) {
            $this->q['draft_flag'] = '';
        }

        // とりあえずから文字で作成
        $this->q['attach_en'] = '';
        $this->q['attach_jp'] = '';

        $this->q['user_no'] = '';
        $this->q['comment_user_name_off'] = '';
        $this->q['comment_text_name_off'] = '';


        $inserted_id = $data_dt->insert(array(
            'title_name'        => $this->q['title_name'],
            'text_name'         => $this->q['text_name'],
            'touroku_date'      => $this->q['touroku_date'],
            'koushin_date'      => $this->q['koushin_date'],
            'autolink_flag'     => $this->q['autolink_flag'],
            'category_id'       => $this->q['category_id'],
            'user_no'           => $this->q['user_no'],
            'tag_id'            => $this->q['tag_id'],
            'attach_en'         => $this->q['attach_en'],
            'attach_jp'         => $this->q['attach_jp'],
            'comment_user_name_off' => $this->q['comment_user_name_off'],
            'comment_text_name_off' => $this->q['comment_text_name_off'],
            'hatena_flag'       => $this->q['hatena_flag'],
            'markdown_flag'     => $this->q['markdown_flag'],
            'draft_flag'        => $this->q['draft_flag'],
        ));

//		// 1. 削除対象のファイルを削除
//		$this->_attach_file_delete();

        // 2. 添付ファイルの移動
//OLD		$this->_attach_file_move_uploadify($inserted_id, $this->q['file_start_no'], $ajax_filelist);
        $dropzone_filelist = array();
        if (isset($this->q['dropzone_files'])) {
            $dropzone_filelist = $this->q['dropzone_files'];
        }
        $this->_attach_file_move_dropzone($inserted_id, $this->q['file_start_no'], $dropzone_filelist);

        // 3. 添付ファイルリスト作成
        $this->q['attach_jp'] = $this->q['attach_jp_dropzone'];
        $this->q['attach_en'] = $this->q['attach_en_dropzone'];

        if (@$this->_ff_config['view_attach_image']==1) {
            if (isset($this->q['attach_en_dropzone'])) {
                $tmp_loop = preg_split('/,/', $this->q['attach_en_dropzone']);
                foreach ($tmp_loop as $k => $v) {
                    if (preg_match('/\.jpg/', $v) || preg_match('/\.jpeg/', $v) || preg_match('/\.png/', $v) || preg_match('/\.gif/', $v)) {
                        $f = $this->_make_apath($this->q['_program_uri'], "data/filedir/{$v}");
                        $this->q['text_name'] .= "\n".'<a href="'.$f.'"><img src="'.$f.'"></a>';
                    }
                }
            }
        }

        $inserted_id = $data_dt->update(array(
            'data_id'           => $inserted_id,
            'text_name'         => $this->q['text_name'],
            'attach_en'         => $this->q['attach_en'],
            'attach_jp'         => $this->q['attach_jp'],
        ));

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();

        // tweet_write()
        if (strcmp($this->q['draft_flag'], '')==0) {
            $this->_tweet_write($inserted_id, 'add');
        }

        // dropbox : data
        if ($this->_ff_config['backup_on_dropbox']==1) {
            $this->_backup_to_dropbox('data');
            if (strcmp($this->q['attach_jp'], '')!=0) {
                $this->_backup_to_dropbox('attachfile');
            }
        }

        // html archive
        if (@$this->_ff_config['use_html_archive']==1) {
            $this->_create_archive_jump('add', $inserted_id);
            exit();
        }

        // カテゴリトップに移動
        header("Location:{$this->_ff_config['memo_uri']}/category/{$this->q['category_id']}");
    }



    //========== do_data_edit_input
    public function do_data_edit_input()
    {

        // テンプレート
        $template_file = 'temp_admin_data_input.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_hash = $data_dt->select_one(array(
            'data_id' => $this->q['data_id'] ,
        ));

        // uploadify用の ランダム文字列, ファイル番号 生成
        $rnd_key = md5(time());

        //$this->dump( $data_hash );

        $file_start_no = 1;
        $max_no = 0;
        if (isset($data_hash['attach_en_loop'])) {
            foreach ($data_hash['attach_en_loop'] as $k => $v) {
                if (preg_match('/.+?_([0-9])+\..+/', $v, $r)) {
                    //$this->dump( $r );
                    if ($max_no < $r[1]) {
                        $max_no=$r[1];
                    }
                }
            }
            $file_start_no = $max_no + 1;
        }
//$this->dump( $file_start_no );

        $this->template->assign(array("rnd_key" => $rnd_key));

        $this->q['hidden'] = $this->make_hidden();
        $this->q['hidden'] .= <<< DOC_END
<input type="hidden" name="cmd" value="data_edit_submit" />
<input type="hidden" name="touroku_date" value="{$data_hash['touroku_date']}" />
<input type="hidden" name="rnd_key" value="{$rnd_key}" />
<input type="hidden" name="file_start_no" value="{$file_start_no}" />
DOC_END;

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_hash = $category_dt->select_one(array(
            'category_id' => $data_hash['category_id'] ,
        ));
        $this->template->assign(array("category_name" => $category_hash['category_name']));
        $this->template->assign(array("desc_name" => $category_hash['desc_name']));


        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
        ), 0, 9999);
        $this->template->assign(array("category_master_loop" => $category_loop));

        // データにcategory_name追加
        $category_hash = $category_dt->select_one(array(
            'category_id' => $data_hash['category_id'] ,
        ));
        $data_hash['category_name'] = $category_hash['category_name'];
        $data_hash['icon_file_name'] = $category_hash['icon_file_name'];

        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(), 0, 9999);
        $this->template->assign(array("tag_master_loop" => $tag_loop));

        // this_category_tag_loop
        $this_category_tag_loop = $this->_get_this_category_tag($data_hash['category_id']);
        $this->template->assign(array("this_category_tag_loop" => $this_category_tag_loop));

        $this->template->assign($data_hash);
        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $html = $this->template->fetch($template_file, $template_id);

        // fillin
        require_once './flatframe/FillInForm.class.php';
        $fill = new HTML_FillInForm();
        $output =$fill->fill(array(
           'scalar' => $html,
           'fdat'   => $data_hash ,
        ));
        // 出力
        print $output;
    }



    //========== do_data_edit_submit
    public function do_data_edit_submit()
    {

// $this->dump( $this->q ); die;

        $ajax_filelist = $this->_check_attach_file($this->q['rnd_key']);

//		$this->dump( $ajax_filelist );
//		$this->dump( $this->q ); die;


        require_once "./flatframe/exfileupload2.php";
        $file = new exfileupload2($this->_ff_config['upload_tmp_dir']);
        $file->delete_tmp();


        // 1. 削除対象のファイルを削除
        $this->_attach_file_delete();

        $dropzone_filelist = array();
        if (isset($this->q['dropzone_files'])) {
            $dropzone_filelist = $this->q['dropzone_files'];
        }
        // 2. 添付ファイルの移動 （IDがわかっているので先に移動する）
        // $this->_attach_file_move_uploadify($this->q['data_id'], $this->q['file_start_no'], $ajax_filelist);
        $this->_attach_file_move_dropzone($this->q['data_id'], $this->q['file_start_no'], $dropzone_filelist);


//		$this->dump( $this->q ); die;

        // 3. 添付ファイルリスト作成
        $separator = '';
        if (strcmp($this->q['attach_jp_already'], '')!=0 && strcmp($this->q['attach_jp_dropzone'], '')!=0) {
            $separator = ',';
        }
        $this->q['attach_jp'] = $this->q['attach_jp_already'].$separator.$this->q['attach_jp_dropzone'];
        $this->q['attach_en'] = $this->q['attach_en_already'].$separator.$this->q['attach_en_dropzone'];

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');

        $this->q['tag_id']       = $this->_create_tag_id_from_name($this->q['tag_name']);
        require_once "./flatframe/exdate.php";
        $t = new exdate();
        list($year, $month, $day, $hour, $min, $sec) = $t->now();
        $this->q['koushin_date'] = "$year$month$day$hour$min$sec";

        $this->q['autolink_flag'] = $this->_create_autolink_flag($this->q['kihou_flag']);
        $this->q['hatena_flag']   = $this->_create_hatena_flag($this->q['kihou_flag']);
        $this->q['markdown_flag'] = $this->_create_markdown_flag($this->q['kihou_flag']);

        $this->q['user_no'] = '';
        $this->q['comment_user_name_off'] = '';
        $this->q['comment_text_name_off'] = '';

        if (! isset($this->q['draft_flag'])) {
            $this->q['draft_flag']='';
        }

        $data_dt->update(array(
            'data_id'           => $this->q['data_id'],
            'title_name'        => $this->q['title_name'],
            'text_name'         => $this->q['text_name'],
            'touroku_date'      => $this->q['touroku_date'],
            'koushin_date'      => $this->q['koushin_date'],
            'autolink_flag'     => $this->q['autolink_flag'],
            'category_id'       => $this->q['category_id'],
            'user_no'           => $this->q['user_no'],
            'tag_id'            => $this->q['tag_id'],
            'attach_en'         => $this->q['attach_en'],
            'attach_jp'         => $this->q['attach_jp'],
            'comment_user_name_off' => $this->q['comment_user_name_off'],
            'comment_text_name_off' => $this->q['comment_text_name_off'],
            'hatena_flag'       => $this->q['hatena_flag'],
            'markdown_flag'     => $this->q['markdown_flag'],
            'draft_flag'        => $this->q['draft_flag'],
        ));

        if (isset($this->q['movetop_flag'])) {
            if (strcmp($this->q['movetop_flag'], 'on')==0) {
                $data_dt->move_top(array(
                    'data_id'           => $this->q['data_id'],
                ));
            }
        }

        // tweet_write()
        if (strcmp($this->q['draft_flag'], '')==0) {
            $this->_tweet_write($this->q['data_id'], 'edit');
        }
        // dropbox
        if ($this->_ff_config['backup_on_dropbox']==1) {
            $this->_backup_to_dropbox('data');
            if (count($ajax_filelist)>0) {
                $this->_backup_to_dropbox('attachfile');
            }
        }

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();

        // html archive
        if (@$this->_ff_config['use_html_archive']==1) {
            $this->_create_archive_jump('edit', $this->q['data_id']);
            exit();
        }

        header("Location:{$this->q['back_uri']}");
    }



    //========== _create_archive_jump
    public function _create_archive_jump($flag='', $data_id=0)
    {
        if (@$this->_ff_config['recreate_html_archive']==1 || strcmp($flag, 'category')==0) {
            $url = "{$this->q['_program_name']}?cmd=rewrite_all_process&start=0&limit=50&back_uri=" . urlencode($this->q['back_uri']);
            header("Location:{$url}");
        } else {
            if (strcmp($flag, '')==0) {
                return false;
            }
            if ($data_id == 0) {
                return false;
            }
            $url = "{$this->q['_program_name']}?cmd=write_process&flag={$flag}&data_id={$data_id}&back_uri=" . urlencode($this->q['back_uri']);
            header("Location:{$url}");
        }
    }



    //========== do_data_delete_confirm
    public function do_data_delete_confirm()
    {

        // テンプレート
        $template_file = 'temp_admin_data_delete.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_hash = $data_dt->select_one(array(
            'data_id' => $this->q['data_id'] ,
        ));

        // データにcategory_name追加
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_hash = $category_dt->select_one(array(
            'category_id' => $data_hash['category_id'] ,
        ));
        $data_hash['category_name'] = $category_hash['category_name'];
        $data_hash['icon_file_name'] = $category_hash['icon_file_name'];


        $this->q['hidden'] = $this->make_hidden();
        $this->q['hidden'] .= <<< DOC_END
<input type="hidden" name="cmd" value="data_delete_submit" />
DOC_END;

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        $this->template->assign($this->q);
        $this->template->assign($data_hash);
        $this->template->display($template_file, $template_id);
    }



    //========== do_data_delete_submit
    public function do_data_delete_submit()
    {

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_hash = $data_dt->select_one(array(
            'data_id' => $this->q['data_id'] ,
        ));

        if (isset($data_hash['attach_en_loop'])) {
            foreach ($data_hash['attach_en_loop'] as $v) {
                unlink("{$this->_ff_config['data_file_dir']}/{$v}");
            }
        }

        $data_dt->delete(array(
            'data_id' => $this->q['data_id'] ,
        ));

        // キャッシュ削除
        $this->template->caching = 1;
        $this->template->cache_dir = $this->_ff_config['smarty_cache_dir'];
        $this->template->clear_all_cache();

        // html archive
        if (@$this->_ff_config['use_html_archive']==1) {
            $this->_create_archive_jump('delete', $this->q['data_id']);
            exit();
        }

        header("Location:{$this->q['back_uri']}");
    }






    //========== do_login
    public function do_login()
    {
        // $this->_set_session_param();
        $this->_session_start();
        $this->q['hidden'] = $this->make_hidden();
        $this->template->assign($this->q);
        $this->template->display("temp_admin_login.html");
    }



    //========== do_login_confirm
    public function do_login_confirm()
    {
        if (strcmp($this->q['password'], $this->_ff_config['admin_pass'])==0) {

            // $this->_set_session_param();
            $this->_session_start();
            $_SESSION['_fc_admin_login'] = array();
            $_SESSION['_fc_admin_login']['registered']=1;

            $back_uri = '';
            if (isset($this->q['back_uri'])) {
                $back_uri = $this->q['back_uri'];
            }
            $e_uri = urlencode($back_uri);
            // cookie
            $value = md5($this->_ff_config['admin_pass']);
            setcookie('is_admin', $value, time()+30*24*3600, '/');        //30日で期限切れ

            header("Location:{$this->q['login_next_uri']}&back_uri={$e_uri}");
            exit();
        } else {
            $this->template->assign(array("error_message" => 'パスワードが違います'));
            $this->q['hidden'] = $this->make_hidden();
            $this->template->assign($this->q);
            $this->template->display("temp_admin_login.html");
        }
    }



    //========== do_write_process
    public function do_write_process()
    {
        if (strcmp($this->q['flag'], 'add')==0 || strcmp($this->q['flag'], 'edit')==0) {
            $data = $this->_create_archive($this->q['data_id']);
            $filename = "{$this->_ff_config['archive_dir']}/{$this->q['data_id']}.html";
            $fp = fopen($filename, 'w');
            fwrite($fp, $data);
            fclose($fp);
        } elseif (strcmp($this->q['flag'], 'delete')==0) {
            $this->_delete_file_regex($this->_ff_config['archive_dir'], $regex_name = "/^{$this->q['data_id']}.html$/");
        }
        header("Location:{$this->q['back_uri']}");
    }



    //========== do_rewrite_all_process
    public function do_rewrite_all_process()
    {
        @set_time_limit(300);

        if ($this->q['start']==0) {
            $this->_delete_file_regex($this->_ff_config['archive_dir'], $regex_name = '/[0-9]+.html/');
        }

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'draft_flag' => '' ,
        ), $this->q['start'], $this->q['limit']);
        foreach ($data_loop as $k => $v) {
            $data = $this->_create_archive($v['data_id']);
            $filename = "{$this->_ff_config['archive_dir']}/{$v['data_id']}.html";
            $fp = fopen($filename, 'w');
            fwrite($fp, $data);
            fclose($fp);
        }

        if (count($data_loop) < $this->q['limit']) {
            header("Location:{$this->q['back_uri']}");
        } else {
            $this->q['start'] = $this->q['start'] + $this->q['limit'];
            $url = "{$this->_ff_config['admin_uri']}?cmd=rewrite_all_process&start={$this->q['start']}&limit={$this->q['limit']}&back_uri=" . urlencode($this->q['back_uri']);
            header("Location:{$url}");
        }
    }




    //========== do_appearance_input
    public function do_appearance_input()
    {

//bootswatch
        $bootswatch_loop = array();
        $dir = dir('./bootstrap/bootswatch');
        while (($file=$dir->read()) !== false) {
            if (preg_match('/^\./', $file)) {
                continue;
            }    // skip dir , skip hidden file
            else {
                if (is_dir("./bootstrap/bootswatch/{$file}")) {
                    array_push($bootswatch_loop, $file);
                        //$this->dump( $file );
                }
            }
        }
        sort($bootswatch_loop, SORT_STRING);
        $this->template->assign(array("bootswatch_loop" => $bootswatch_loop));

        // appearance
        $appearance_dt = new textdb("./textdb.yml", "appearance", $this->_ff_config['data_dir'], 'cgi');
        $hash = $appearance_dt->select_one(array(
            'appearance_id'   => 1 ,
        ));
//$this->dump( $hash );
        if ($hash['navbar_name']==='') {
            $hash['navbar_name'] = 'default';
        }
        $this->template->assign(array("appearance" => $hash));

        // smarty_cache数
        $dir = './smarty_cache';
        $jpeg_files = glob($dir . DIRECTORY_SEPARATOR . '*.*');
        $this->q['smarty_cache_count'] = count($jpeg_files);

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        // テンプレート
        $template_file = 'temp_admin_appearance_input.html';
//		$template_id   = md5($_SERVER['REQUEST_URI']);
        $this->template->assign($this->q);
        $html = $this->template->fetch($template_file);


        // fillin
        require_once './flatframe/FillInForm.class.php';
        $fill = new HTML_FillInForm();
        $output =$fill->fill(array(
           'scalar' => $html,
           'fdat'   => $hash ,
        ));
        // 出力
        print $output;
    }



    //========== do_appearance_submit
    public function do_appearance_submit()
    {

        // appearance
        $appearance_dt = new textdb("./textdb.yml", "appearance", $this->_ff_config['data_dir'], 'cgi');
//		$appearance_dt->help(); die;
        $appearance_dt->update(array(
            'appearance_id'           => 1 ,
            'bootswatch_name'         => $this->q['bootswatch_name'],
            'navbar_name'             => $this->q['navbar_name'],
            'fm_t_id_kbn'             => $this->q['fm_t_id_kbn'],
            'fm_t_id_icon_class_name' => $this->q['fm_t_id_icon_class_name'],
            'view_attach_in_data_flg' => $this->q['view_attach_in_data_flg'],
            'hide_edit_menu_flg'      => $this->q['hide_edit_menu_flg'],

        ));

        $deleted_list  = $this->_delete_allfile('smarty_cache');

        $url = "{$this->q['_program_name']}?cmd=appearance_input";
        header("Location:{$url}");
    }



    //========== do_smarty_cache_delete_submit
    public function do_smarty_cache_delete_submit()
    {
        $deleted_list  = $this->_delete_allfile('smarty_cache');
        $url = "{$this->q['_program_name']}?cmd=appearance_input";
        header("Location:{$url}");
    }



    //========== _session_start
    public function _session_start()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }



    //========== _create_archive
    public function _create_archive($archive_no)
    {

        // テンプレート
        $template_file = 'temp_category.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // back_uri
        $arg = preg_replace("|{$this->q['_program_name']}|", "", $_SERVER['REQUEST_URI']);
        $back_uri = $this->q['_program_uri'].$arg;

        // data
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'data_id' => $archive_no ,
            'draft_flag'  => '' ,
        ), 0, 1);

        $category_hash = array();
        $now_category_tag_loop = array();
        $related_loop = array();
        if (isset($data_loop[0])) {
            // category
            $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
            $category_hash = $category_dt->select_one(array(
                'category_id' => $data_loop[0]['category_id'] ,
            ));
            $now_category_tag_loop = $this->_get_now_category_tag_loop_quicklink($data_loop[0]['category_id']);

            // _get_related_entry
            $related_loop = array();
            if (isset($data_loop[0]['tag_id_loop'])) {
                $related_loop = $this->_get_related_entry($archive_no, $data_loop[0]['tag_id_loop']);
            }
        }

        $this->_ff_config['image_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['image_dir']);
        $this->_ff_config['admin_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['admin_file']);
        $this->_ff_config['data_apath'] = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['data_dir']);
        $this->template->assign(array("config" => $this->_ff_config));
//$this->dump( $this->_ff_config ); die;
        $this->q['_program_uri'] = preg_replace('/_admin/', '', $this->q['_program_uri']);
        $this->q['_program_name'] = preg_replace('/_admin/', '', $this->q['_program_name']);

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));
        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));


        $this->template->assign($this->q);
        $this->template->assign(array("back_uri" => $back_uri));
        $this->template->assign($category_hash);
        $this->template->assign(array("data_loop" => $data_loop));
        $this->template->assign(array("related_loop" => $related_loop));
        $this->template->assign(array("now_category_tag_loop" => $now_category_tag_loop));
        $html = $this->template->fetch($template_file, $template_id);
        return $html;
    }


    //==================== ：_get_header_recent_entry
    public function _get_header_recent_entry()
    {
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'draft_flag' => '',
            'ORDER_BY'   => 'koushin_date DESC'
        ), 0, 25);
        return $data_loop;
    }



    //==================== ：_get_header_category
    public function _get_header_category()
    {
        require_once "./flatframe/textdb.php";
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
            'view_mode' => ''
        ), 0, 9999);
        return $category_loop;
    }



    //========== _get_related_entry
    public function _get_related_entry($arg_id, $tag_id_loop)
    {
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
        ), 0, 99999);

        $out_loop = array();
        foreach ($data_loop as $k => $v) {
            if (isset($v['tag_id_loop'])) {
                foreach ($tag_id_loop as $tid) {
                    if (in_array($tid, $v['tag_id_loop'])) {
                        if ($arg_id!=$v['data_id']) {
                            array_push($out_loop, $v);
                        }
                        break;
                    }
                }
            }
        }

        // ランダムソート
        $ids = array();
        foreach ($out_loop as $value) {
            array_push($ids, $value['data_id']);
        }
        shuffle($ids);
        array_multisort($ids, SORT_DESC, $out_loop);

        return array_slice($out_loop, 0, 5);
    }



    //==========_get_now_category_tag_loop_quicklink
    public function _get_now_category_tag_loop_quicklink($category_id)
    {

//		$now_category_tag_loop = array();

        // クイックリンク
        // data
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'category_id' => $category_id ,
        ), 0, 9999);

        $quicklink_loop = array();
        foreach ($data_loop as $k => $v) {
            if ($category_id==$v['category_id']) {
                $tmp = array();
                $tmp['data_id'] = $v['data_id'];
                $tmp['title_name'] = $v['title_name'];
                array_push($quicklink_loop, $tmp);
            }
        }

        $this->template->assign(array("quicklink_loop" => $quicklink_loop));

        // tag
        $now_category_tag_loop = array();
        $now_category_tag_loop2 = array();

        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(
        ), 0, 9999);
        $this->template->assign(array("tag_master_loop" => $tag_loop));

        $tag_id_loop = array();
        foreach ($data_loop as $k => $v) {
            if (isset($v['tag_id_loop'])) {
                foreach ($v['tag_id_loop'] as $value) {
                    if ($value != 0) {
                        $tag_id_loop[] = $value;    // array_push();
                    }
                }
            }
        }
        foreach ($tag_id_loop as $value) {
            $hit_flag = 0;
            for ($i=0;$i<count($now_category_tag_loop2);$i++) {
                if ($now_category_tag_loop2[$i]['tag_id']==$value) {
                    $now_category_tag_loop2[$i]['tag_count_no']++;
                    $hit_flag = 1;
                    break;
                }
            }
            if ($hit_flag==0) {
                foreach ($tag_loop as $tk => $tv) {
                    if ($value==$tv['tag_id']) {
                        $tv['tag_count_no']=1;
                        array_push($now_category_tag_loop2, $tv);
                        break;
                    }
                }
            }
        }

        return $now_category_tag_loop2;
    }




    //========== _delete_file_regex
    public function _delete_file_regex($dirpath = '', $regex_name = '')
    {
        if (strcmp($dirpath, '') == 0) {
            die('_delete_file_regex : error : please set dir_name');
        }
        if (strcmp($regex_name, '') == 0) {
            die('_delete_file_regex : error : please set regex_name');
        }
        $deleted_list = array();
        $dir = dir($dirpath);
        while (($file = $dir->read()) !== false) {
            if (preg_match('/^\./', $file)) {
                continue;
            } elseif (preg_match("{$regex_name}", $file)) {
                array_push($deleted_list, $file);
                if (!unlink("$dirpath/$file")) {
                    die("delete_allfile : error : can not delete file [{$dirpath}/{$file}]");
                }
            }
        }
        return $deleted_list;
    }



    //========== _attach_file_move_dropzone
    public function _attach_file_move_dropzone($id='', $file_start_no=1, $dropzone_filelist=array())
    {
        if ($id=='') {
            die("idをセットして下さい");
        }

        $this->q['attach_jp_dropzone'] = '';
        $this->q['attach_en_dropzone'] = '';

        umask(0);

        $aray_jp = array();
        $aray_en = array();
        /* 例
        [dropzone_files] => Array
        (
            [0] => phpFK8MBU.png	スクリーンショット 2015-11-13 17.48.35.png
            [1] => phpg6VQZg.png	スクリーンショット 2015-11-13 17.53.26.png
            [2] => phpAHM0gS.png	スクリーンショット 2015-11-24 12.21.13.png
        )
        */
        foreach ($dropzone_filelist as $v) {
            // en_name jp_name
            list($tmp_name, $jp_name) = preg_split("/\t/", $v);
            // ext
            $p   = pathinfo($tmp_name);
            $ext = '.'.$p['extension'];
            $en_name = "{$id}_{$file_start_no}{$ext}";
            array_push($aray_jp, $jp_name);
            array_push($aray_en, $en_name);
            $this->_move("{$this->_ff_config['upload_tmp_dir']}//{$tmp_name}", "{$this->_ff_config['data_file_dir']}/{$en_name}");
            $file_start_no++;
        }
        // this->qに登録
        $this->q['attach_jp_dropzone'] = join(",", $aray_jp);
        $this->q['attach_en_dropzone'] = join(",", $aray_en);
    }



    //========== _attach_file_delete：添付ファイル（すでに登録されているファイル）の削除 Version 3.0
    public function _attach_file_delete()
    {

        // すでにある（削除してない）ファイルリストを作成
        $aray_jp_already = array();
        $aray_en_already = array();

        foreach ($this->q as $k => $v) {
            if (preg_match("{attach_edit_flag([0-9]+)}", $k, $r)) {
                if (strcmp($v, 'delete')==0) {
                    unlink($this->_ff_config['data_file_dir'].'/'.$this->q['attach_en'.$r[1]]);
                } else {
                    array_push($aray_jp_already, $this->q['attach_jp'.$r[1]]);
                    array_push($aray_en_already, $this->q['attach_en'.$r[1]]);
                }
            }
        }
        // this->qに登録
        if (count($aray_jp_already)>0) {
            $this->q['attach_jp_already'] = join(",", $aray_jp_already);
            $this->q['attach_en_already'] = join(",", $aray_en_already);
        } else {
            $this->q['attach_jp_already'] = '';
            $this->q['attach_en_already'] = '';
        }
    }



    //========== _move：ファイル移動関数
    public function _move($from, $to)
    {
        if (copy($from, $to)) {
            unlink($from);
            return true;
        } else {
            die("cannot move file $from  --->   $to");
            return false;
        }
    }



    //========== _check_attach_file
    public function _check_attach_file($rnd_key)
    {
        $key_file_name = './data/tmp/'.$rnd_key.'.txt';
        $no  = 0;
        $buf = @file($key_file_name);

        $out = array();
        if ($buf) {
            foreach ($buf as $e) {
                $no++;
                $key = "uploadify".$no;
                $out[$key] = unserialize($e);
            }
        }
        return $out;
    }



    //========== _check_dropzone_file
    public function _check_dropzone_file()
    {
        $key_file_name = './data/tmp/'.$rnd_key.'.txt';

        foreach ($this->q['dropzone_files'] as $v) {
            $no++;
            $key = "uploadify".$no;
            $out[$key] = unserialize($e);
        }
        return $out;
    }




    //========== _create_tag_id_from_name
    public function _create_tag_id_from_name($tag_name='')
    {
        if (strcmp($tag_name, '')==0) {
            return '';
        }
        $out_array = array();

        $tag_name = preg_replace("{　}u", " ", $tag_name);
        $tag_name = preg_replace("{^\s}", "", $tag_name);
        $tag_name = preg_replace("{\s$}", "", $tag_name);
        $ar = preg_split("{ }", $tag_name);

        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');

        foreach ($ar as $k => $v) {
            $tag_id = $tag_dt->find_or_create(array(
                'tag_name' => $v ,
            ));
            array_push($out_array, $tag_id);
        }
        return join(',', $out_array);
    }



    //========== _create_autolink_flag
    public function _create_autolink_flag($kihou_flag='')
    {
        if (strcmp($kihou_flag, 'autolink')==0) {
            return 'on';
        }
    }



    //========== _create_hatena_flag
    public function _create_hatena_flag($kihou_flag='')
    {
        if (strcmp($kihou_flag, 'hatena')==0) {
            return 'on';
        }
    }



    //========== _create_markdown_flag
    public function _create_markdown_flag($kihou_flag='')
    {
        if (strcmp($kihou_flag, 'markdown')==0) {
            return 'on';
        }
    }



    //========== _make_uri：version 1.3
    public function _make_uri($base='', $rel_path='')
    {
        $base = preg_replace('/\/[^\/]+$/', '/', $base);
        $parse = parse_url($base);
        if (preg_match('/^https\:\/\//', $rel_path)) {
            return $rel_path;
        } elseif (preg_match('/^\/.+/', $rel_path)) {
            $out = $parse['scheme'].'://'.$parse['host'].$rel_path;
            return $out;
        }
        $tmp = array();
        $a = array();
        $b = array();
        $tmp = explode('/', $parse['path']);
        foreach ($tmp as $v) {
            if ($v) {
                array_push($a, $v);
            }
        }
        $b = explode('/', $rel_path);
        foreach ($b as $v) {
            if (strcmp($v, '')==0) {
                continue;
            } elseif ($v=='.') {
            } elseif ($v=='..') {
                array_pop($a);
            } else {
                array_push($a, $v);
            }
        }
        $path = join('/', $a);
        $out = $parse['scheme'].'://'.$parse['host'].'/'.$path;
        return $out;
    }



    //========== _make_apath：version 1.0
    public function _make_apath($base='', $rel_path='')
    {
        $base = preg_replace('/\/[^\/]+$/', '/', $base);
        $parse = parse_url($base);
        if (preg_match('/^https\:\/\//', $rel_path)) {
            return $rel_path;
        } elseif (preg_match('/^\/.+/', $rel_path)) {
            $out = $parse['scheme'].'://'.$parse['host'].$rel_path;
            return $out;
        }
        $tmp = array();
        $a = array();
        $b = array();
        $tmp = explode('/', $parse['path']);
        foreach ($tmp as $v) {
            if ($v) {
                array_push($a, $v);
            }
        }
        $b = explode('/', $rel_path);
        foreach ($b as $v) {
            if (strcmp($v, '')==0) {
                continue;
            } elseif ($v=='.') {
            } elseif ($v=='..') {
                array_pop($a);
            } else {
                array_push($a, $v);
            }
        }
        $path = join('/', $a);
        return '/'.$path;
    }



    //========== _get_this_category_tag
    public function _get_this_category_tag($category_id)
    {
        $tag_array = array();

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_loop = $data_dt->select(array(
            'category_id' => $category_id ,
        ), 0, 9999);
        foreach ($data_loop as $k => $v) {
            if (isset($v['tag_id_loop'])) {
                foreach ($v['tag_id_loop'] as $tk => $tv) {
                    if (! in_array($tv, $tag_array)) {
                        array_push($tag_array, $tv);
                    }
                }
            }
        }
        // tag
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(
        ), 0, 9999);

        $this_category_tag_loop = array();
        foreach ($tag_loop as $k => $v) {
            if (in_array($v['tag_id'], $tag_array)) {
                array_push($this_category_tag_loop, $v);
            }
        }
        return $this_category_tag_loop;
    }



    //========== _admin_session_check：セッションの自動チェック（セッション変数を直接調べる）
    public function _session_check()
    {

        // $this->_set_session_param();
        $this->_session_start();
        $session_flag=0;        // 0：未ログイン 1：ログイン済

        if (! isset($_SESSION['_fc_admin_login'])) {
            $session_flag=0;
        } elseif (! isset($_SESSION['_fc_admin_login']['registered'])) {
            $session_flag=0;
        }
        // OFF else if (! isset($_SESSION['_fc_admin_login']['sessionip']) ) { $session_flag=0; }

        // OFF else if ($_SESSION['_fc_admin_login']['registered']==1 and $_SESSION['_fc_admin_login']['sessionip']){
        elseif ($_SESSION['_fc_admin_login']['registered']==1) {
            $session_flag=1;
        }

        if ($session_flag==0) {
            // back_uri
            $arg = preg_replace("|{$this->q['_program_name']}|", "", $_SERVER['REQUEST_URI']);
            $login_next_uri = urlencode($this->q['_program_uri'].$arg);
            $back_uri = '';
            if (isset($this->q['back_uri'])) {
                $back_uri = urlencode($this->q['back_uri']);
            }
            header("Location:{$this->q['_program_uri']}?cmd=login&back_uri={$back_uri}&login_next_uri={$login_next_uri}");
            exit();
        }
    }



    //========== _set_session_param
    public function _set_session_param()
    {
        if (@$this->_ff_config['session_save_path']) {
            session_save_path($this->_ff_config['session_save_path']);
        }
        ini_set("session.use_cookies", 1);
        ini_set("session.cookie_lifetime", 2592000);    // セッション有効時間 60*60*24*30（30日間）
        ini_set('session.gc_maxlifetime', 2592000);
        ini_set('session.gc_probability', 1);            // gc_probability/gc_divisor の確率でガベージコレクション
        ini_set('session.gc_divisor', 100);
    }



    //========== _get_appearance
    public function _get_appearance()
    {
        // appearance
        require_once "./flatframe/textdb.php";
        $appearance_dt = new textdb("./textdb.yml", "appearance", $this->_ff_config['data_dir'], 'cgi');
        $hash = $appearance_dt->select_one(array(
        ));
        if (! isset($hash['bootswatch_name'])) {
            $hash['bootswatch_name'] = 'default';
        }
        if (! isset($hash['navbar_name'])) {
            $hash['navbar_name'] = 'default';
        }
        $this->template->assign(array("appearance" => $hash));
    }



    //========== _tweet_write
    public function _tweet_write($data_id, $flag='')
    {
        if (! $this->_ff_config['tweet_new_entry']) {
            return;
        }

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $data_hash = $data_dt->select_one(array(
            'data_id' => $data_id ,
        ));
        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_hash = $category_dt->select_one(array(
            'category_id' => $data_hash['category_id'] ,
        ));

        $long_url ="{$this->_ff_config['memo_uri']}/archive/{$data_hash['data_id']}";
        $tiny_url = $this->_get_tiny_url($long_url);

        $flag_text = '';
        if ($flag=='add') {
            $flag_text = '新規';
        }
        if ($flag=='edit') {
            $flag_text = '更新';
        }
        $tweet_text = "【{$flag_text}】{$data_hash['title_name']} | {$category_hash['category_name']} {$tiny_url}";

        // Twitter
        require_once 'tmhOAuth.php';
        $twitter = new tmhOAuth(
            array(
                'consumer_key'        => $this->_ff_config['twitter_consumer_key'] ,
                'consumer_secret'     => $this->_ff_config['twitter_consumer_secret'] ,
                'token'               => $this->_ff_config['twitter_oauth_token'] ,
                'secret'              => $this->_ff_config['twitter_oauth_verifier'] ,
                'curl_ssl_verifypeer' => false ,
                'timezone'            => 'Asia/Tokyo' ,
            )
        );
        $r = $twitter->request('POST', $twitter->url('1.1/statuses/update'), array(
            'status' => $tweet_text
        ), true, false);
        if ($r = 200) {
        } else {
            die('tweet ERROR code : '.$r);
        }
    }



    //========== _get_tiny_url
    public function _get_tiny_url($long_url='')
    {
        $api_url = 'https://www.googleapis.com/urlshortener/v1/url';
        $api_key = 'AIzaSyANbyM3JGcrKDC2_bkoxxvulndT5B1lBYw';
        $curl = curl_init("$api_url?key=$api_key");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"longUrl":"' . $long_url . '"}');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($res);
        $tiny_url = $json->id;
        return $tiny_url;
    }



    //========== _backup_to_dropbox
    public function _backup_to_dropbox($flag='')
    {
        if (! $flag) {
            return;
        }
        include_once 'Dropbox/autoload.php';

        $oauth = new Dropbox_OAuth_PEAR($this->_ff_config['dropbox_consumer_key'], $this->_ff_config['dropbox_consumer_secret']);
        $dropbox = new Dropbox_API($oauth);

        // session_start();
        $tokens = $dropbox->getToken($this->_ff_config['dropbox_id'], $this->_ff_config['dropbox_pass']);
        $oauth->setToken($tokens);


        if (! $dropbox->is_exists($this->_ff_config['dropbox_base_dir'])) {
            $dropbox->createFolder($this->_ff_config['dropbox_base_dir']);
            $dropbox->createFolder("{$this->_ff_config['dropbox_base_dir']}/data");
            $dropbox->createFolder("{$this->_ff_config['dropbox_base_dir']}/data/filedir");
        }

        if (strcmp($flag, 'data')==0) {
            //$md = $dropbox->getMetaData("{$this->_ff_config['dropbox_base_dir']}/data/{$v}");
            $v='data.cgi';
            if ($dropbox->need_upload("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}")) {
                $dropbox->putFile("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}");
            }
            $v = 'tag.cgi';
            if ($dropbox->need_upload("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}")) {
                $dropbox->putFile("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}");
            }
        } elseif (strcmp($flag, 'category')==0) {
            $v='category.cgi';
            if ($dropbox->need_upload("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}")) {
                $dropbox->putFile("{$this->_ff_config['dropbox_base_dir']}/data/{$v}", "{$this->_ff_config['data_dir']}/{$v}");
            }
        } elseif (strcmp($flag, 'attachfile')==0) {
            $dir = dir($this->_ff_config['data_file_dir']);
            while (($file=$dir->read()) !== false) {
                if (preg_match('/^\./', $file)) {
                    continue;
                } else {
                    if ($dropbox->need_upload("{$this->_ff_config['dropbox_base_dir']}/data/filedir/{$file}", "{$this->_ff_config['data_file_dir']}/{$file}")) {
                        $dropbox->putFile("{$this->_ff_config['dropbox_base_dir']}/data/filedir/{$file}", "{$this->_ff_config['data_file_dir']}/{$file}");
                    }
                }
            }
        }
    }



    public function _delete_allfile($dirpath = '')
    {
        if (strcmp($dirpath, '')==0) {
            die('delete_allfile : error : please set dir_name');
        }
        $deleted_list = array();
        $dir = dir($dirpath);
        while (($file=$dir->read()) !== false) {
            if (preg_match('/^\./', $file)) {
                continue;
            }    // skip dir , skip hidden file
            else {
                array_push($deleted_list, $file);
                if (! unlink("$dirpath/$file")) {
                    die("delete_allfile : error : can not delete file [{$dirpath}/{$file}]");
                }
            }
        }
        return $deleted_list;
    }
}


$controller = new ff_memo_admin("config.cgi");
$controller->run();

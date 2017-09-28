<?php

// flatmemo
// copyright (c) econosys system : https://econosys-system.com/
// version 2.00 Bootswatch導入
// version 2.01 細かいbug-fix
// version 2.02 最近のメモ取得のbug-fix
// version 2.04 minimalauth導入
// version 2.10 PHP7対応
// version 2.11 emoji対応
// version 2.13 [fix] minimalauth判別ロジックの修正
// version 2.14 [add] whitelist追加

require_once "./flatframe.php";
require_once "vendor/autoload.php";

class ff_memo extends flatframe
{


    //==========
    public function __construct($configfile)
    {
        $this->_ff_configfile = $configfile;
    }



    //==========
    public function setup()
    {
        $this->rootdir    = '.';
        $this->run_modes  = array(
            'default'        => 'do_view_index' ,
            'setup_flatmemo' => 'do_setup_flatmemo' ,
            'closet'         => 'do_view_closet' ,
            'category'       => 'do_view_category' ,
            'archive'        => 'do_view_archive' ,
            'search'         => 'do_search' ,
            'rss'            => 'do_rss' ,
            'download'       => 'do_download' ,
            'about'          => 'do_about' ,
        );
    }



    //========== app_prerun
    public function app_prerun()
    {
        if ( ! $this->_check_whitelist() ){ die("IP ({$_SERVER['REMOTE_ADDR']}) が whitelistと適合しません");}
        $this->_set_session_param();
        if (@$this->_ff_config['flatmemo_all_password']) {
            require_once "exminimalauth.php";
            $ma = new exminimalauth(array(
                'admin_password' => $this->_ff_config['flatmemo_all_password'] ,
            ));
            $ma->exminimalauth_login("{$this->q['_program_uri']}?cmd=exminimalauth_login_submit", (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        }

        $this->_ff_config['root_uri'] = $this->_make_uri($this->q['_program_uri'], './');
        $this->_ff_config['root_uri'] = preg_replace("/\/$/", '', $this->_ff_config['root_uri']);
        header('X-Powered-By:');
        if (! defined('__DIR__')) {
            define('__DIR__', dirname(__FILE__));
        }
        $this->_ff_config['memo_uri']      = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['memo_file']);
        $this->_ff_config['admin_uri']     = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['admin_file']);
        $this->_ff_config['data_file_uri'] = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['data_file_dir']);
        $this->_ff_config['icon_uri']      = $this->_make_uri($this->q['_program_uri'], $this->_ff_config['icon_dir']);
        $this->_ff_config['data_apath']    = $this->_make_apath($this->q['_program_uri'], $this->_ff_config['data_dir']);
        $this->_ff_config['base_path']     = $this->_make_apath($this->q['_program_uri'], '.');
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



    //========== _set_smarty_cache_dir：【Smartyキャッシュ：ディレクトリ指定、時間指定】
    public function _set_smarty_cache_dir($template_file, $template_id, $cache_dir='', $lifetime=0)
    {
        if (! $cache_dir) {
            print "_set_smarty_cache_dir : ERROR : Please set cache dir.";
            return false;
        }

        $this->template->caching = 2;
        $this->template->cache_dir = $cache_dir;
        if ($lifetime==0) {
            $lifetime = 7;
        }
        $smarty_lifetime = $lifetime*24*60*60;
        $this->template->cache_lifetime = $smarty_lifetime;    // 7 * 24時間 * 60 * 60	// 7日間 (default)

        if ($this->template->is_cached($template_file, $template_id)) {
            $this->template->display($template_file, $template_id);
            $this->dumpmem2();
            print "<!-- cached (dir)[{$lifetime}][{$smarty_lifetime}] -->";

            // 10回に1回キャッシュをクリア
            if (rand(1, 10)==1) {
                $this->template->clear_all_cache($smarty_lifetime);
                print "<!-- clear [{$smarty_lifetime}] -->";
            }
            exit;
        }
    }



    //========== do_view_index
    public function do_view_index()
    {
        if (! file_exists("{$this->_ff_config['data_dir']}/data.cgi")) {
            header("Location:{$this->_ff_config['memo_uri']}?cmd=setup_flatmemo");
            exit;
        }

        $this->_get_appearance();

        // テンプレート
        $template_file = 'temp_index.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // smarty cache
        if ($this->_ff_config['use_smarty_cache']==1) {
            $this->_set_smarty_cache_dir($template_file, $template_id, dirname(__FILE__).'/smarty_cache/', 1);
        }

        require_once "./flatframe/textdb.php";
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');

        $category_loop = $category_dt->select(array(
            'view_mode' => ''
        ), 0, 9999);

        $closet_loop = $category_dt->select(array(
            'view_mode' => 'off'
        ), 0, 9999);

        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));
        //$header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $category_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $this->template->assign(array("closet_loop" => $closet_loop));
        $this->template->display($template_file, $template_id);
    }




    //========== do_view_closet
    public function do_view_closet()
    {
        $this->_get_appearance();

        // テンプレート
        $template_file = 'temp_index.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // smarty cache
        if ($this->_ff_config['use_smarty_cache']==1) {
            $this->_set_smarty_cache_dir($template_file, $template_id, dirname(__FILE__).'/smarty_cache/', 1);
        }

        require_once "./flatframe/textdb.php";
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');

        $category_loop = $category_dt->select(array(
            'view_mode' => 'off'
        ), 0, 9999);

        $recent_loop = $this->_get_header_recent_entry();

        $this->template->assign($this->q);
        $this->template->assign(array("recent_loop" => $recent_loop));
        $this->template->assign(array("category_loop" => $category_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== do_view_category
    public function do_view_category()
    {
        $this->_get_appearance();

        // テンプレート
        $template_file = 'temp_category.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // smarty cache
        if ($this->_ff_config['use_smarty_cache']==1) {
            $this->_set_smarty_cache_dir($template_file, $template_id, dirname(__FILE__).'/smarty_cache/', 1);
        }

        // back_uri
        $arg = preg_replace("|{$this->q['_program_name']}|", "", $_SERVER['REQUEST_URI']);
        $back_uri = $this->q['_program_uri'].$arg;


        $category_id = $this->q['path_info_arg'][0];

        $tag_id=false;
        require_once "./flatframe/textdb.php";
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        if (isset($this->q['path_info_arg'][2])) {
            $tag_name = $this->q['path_info_arg'][2];
            $tag_hash = $tag_dt->select_one(array(
                'tag_name' => $tag_name
            ));
            $tag_id = $tag_hash['tag_id'];
            $this->q['tag_name'] = $tag_name;
        }

        // category
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');

        $category_hash = $category_dt->select_one(array(
            'category_id' => $category_id ,
        ));

        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');

        // ドラフト以外をselect
        $data_loop = $data_dt->select(array(
            'category_id' => $category_id ,
            'draft_flag'  => '' ,
        ), 0, 9999);

        if ($tag_id) {
            $data_loop2 = array();
            foreach ($data_loop as $k => $v) {
                $tag_array = preg_split("{,}", $v['tag_id']);
                if (in_array($tag_id, $tag_array)) {
                    array_push($data_loop2, $v);
                }
            }
            $data_loop = $data_loop2;
        }
        $recent_loop = $this->_get_header_recent_entry();
        $now_category_tag_loop = $this->_get_now_category_tag_loop_quicklink($category_id);
        $this->template->assign($this->q);
        $this->template->assign($category_hash);

        $this->template->assign(array("back_uri" => $back_uri));
        $this->template->assign(array("data_loop" => $data_loop));
        $this->template->assign(array("recent_loop" => $recent_loop));
        $this->template->assign(array("now_category_tag_loop" => $now_category_tag_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== ：do_view_archive
    public function do_view_archive()
    {
        if (@$this->_ff_config['use_html_archive'] == 1) {
            header("Location:{$this->_ff_config['data_apath']}/archives/{$this->q['path_info_arg'][0]}.html");
            exit;
        }

        $this->_get_appearance();

        // テンプレート
        $template_file = 'temp_category.html';
        $template_id   = md5($_SERVER['REQUEST_URI']);

        // smarty cache
        if ($this->_ff_config['use_smarty_cache']==1) {
            $this->_set_smarty_cache_dir($template_file, $template_id, dirname(__FILE__).'/smarty_cache/', 1);
        }

        // back_uri
        $arg = preg_replace("|{$this->q['_program_name']}|", "", $_SERVER['REQUEST_URI']);
        $back_uri = $this->q['_program_uri'].$arg;

        $archive_no = $this->q['path_info_arg'][0];

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

            $header_category_loop = $category_dt->select(array(
                'view_mode' => ''
            ), 0, 9999);
            $this->template->assign(array("header_category_loop" => $header_category_loop));

            // $now_category_tag_loop
            $now_category_tag_loop = $this->_get_now_category_tag_loop_quicklink($data_loop[0]['category_id']);

            // _get_related_entry
            $related_loop = array();
            if (isset($data_loop[0]['tag_id_loop'])) {
                $related_loop = $this->_get_related_entry($archive_no, $data_loop[0]['tag_id_loop']);
            }
//			$this->dump( $related_loop );
        }

        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("back_uri" => $back_uri));
        $this->template->assign($category_hash);
        $this->template->assign(array("data_loop" => $data_loop));
        $this->template->assign(array("related_loop" => $related_loop));
        $this->template->assign(array("now_category_tag_loop" => $now_category_tag_loop));
        $this->template->display($template_file, $template_id);
    }



    //========== ：_get_related_entry
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



    //========== ：_get_header_recent_entry
    public function _get_header_recent_entry()
    {
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
//		$data_dt->dump_mode();
        $data_loop = $data_dt->select(array(
            'draft_flag' => '',
            'ORDER_BY'   => 'koushin_date DESC'
        ), 0, 25);
        return $data_loop;
    }



    //========== ：_get_header_category
    public function _get_header_category()
    {
        require_once "./flatframe/textdb.php";
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_loop = $category_dt->select(array(
            'view_mode' => ''
        ), 0, 9999);
        return $category_loop;
    }



    //========== ：_get_now_category_tag_loop_quicklink
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
//OFF$ar = array();
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



    //========== ：do_search
    public function do_search()
    {
        $this->_get_appearance();

        require_once "./flatframe/textdb.php";

        // category
        $category_loop = array();
        if ((! isset($this->q['category_id'])) && $this->q['q']) {
            $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
            $all_loop = $category_dt->select(array(
            ), 0, 9999);
            foreach ($all_loop as $k => $v) {
                if ($this->_listmatch($this->q['q'], $v['category_name'])) {
                    array_push($category_loop, $v);
                }
            }
        }

        // data
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');
        $all_loop = $data_dt->select(array(
            'draft_flag'  => '' ,
        ), 0, 9999);
        $data_loop = array();
        foreach ($all_loop as $k => $v) {
            if (isset($this->q['category_id'])) {
                if ($this->q['category_id'] !=$v['category_id']) {
                    continue;
                }
            }

            if ($this->_listmatch($this->q['q'], $v['title_name']) || $this->_listmatch($this->q['q'], $v['text_name'])) {
                array_push($data_loop, $v);
            }
        }

        // タグ名で検索
        $matched_tag_id_loop = array();
        $matched_tag_name_loop = array();
        $tag_dt = new textdb("./textdb.yml", "tag", $this->_ff_config['data_dir'], 'cgi');
        $tag_loop = $tag_dt->select(array(
        ), 0, 9999);
        foreach ($tag_loop as $k => $v) {
            if ($this->_listmatch($this->q['q'], $v['tag_name'])) {
                $matched_tag_id_loop[] = $v['tag_id'];
                $matched_tag_name_loop[] = $v['tag_name'];
            }
        }
        $data_loop_tag = array();
        foreach ($all_loop as $k => $v) {
            //
            $tag_match_flag = 0;
            if (isset($v['tag_id_loop'])) {
                foreach ($v['tag_id_loop'] as $tid) {
                    if (in_array($tid, $matched_tag_id_loop)>0) {
                        $tag_match_flag = 1;
                        break;
                    }
                }
            }
            // すでに $data_loop にあるやつは除外する
            if ((! $this->_listmatch($this->q['q'], $v['title_name'])) && (! $this->_listmatch($this->q['q'], $v['text_name'])) && $tag_match_flag==1) {
                array_push($data_loop_tag, $v);
            }
        }

        $recent_loop = $this->_get_header_recent_entry();
        $this->template->assign(array("recent_loop" => $recent_loop));
        $header_category_loop = $this->_get_header_category();
        $this->template->assign(array("header_category_loop" => $header_category_loop));

        $this->template->assign($this->q);
        $this->template->assign(array("category_loop" => $category_loop));
        $this->template->assign(array("data_loop" => $data_loop));
        $this->template->assign(array("data_loop_tag" => $data_loop_tag));
        $this->template->assign(array("matched_tag_name_loop" => $matched_tag_name_loop));
        $this->template->display("temp_searchresult.html");
    }



    //========== do_rss
    public function do_rss()
    {
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');

        $data_loop = $data_dt->select(array(
            'draft_flag'  => '' ,
        ), 0, 99999);

        if ($this->_ff_config['view_only_add_feed']==1) {
            $data_loop2 = array_slice($data_loop, 0, 50);
            $this->template->assign(array("data_loop" => $data_loop2));
        } else {
            $ids = array();
            foreach ($data_loop as $value) {
                array_push($ids, $value['koushin_date']);
            }
            array_multisort($ids, SORT_DESC, $data_loop);
            $data_loop2 = array_slice($data_loop, 0, 50);
            $this->template->assign(array("data_loop" => $data_loop2));
        }

        $this->template->assign($this->q);
        $this->template->display("temp_rss.html");
    }



    //========== do_download
    public function do_download()
    {

        // data
        require_once "./flatframe/textdb.php";
        $data_dt = new textdb("./textdb.yml", "data", $this->_ff_config['data_dir'], 'cgi');

        $hash = $data_dt->select_one(array(
            'data_id' => $this->q['data_id'] ,
        ));

        $no = array_search($this->q['file'], $hash['attach_en_loop']);

        $file_name = $hash['attach_jp_loop'][$no];
        $file_path = $this->_ff_config['data_file_dir'].'/'.$this->q['file'];

        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($file_path));
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        readfile($file_path);
    }



    //========== do_setup_flatmemo
    public function do_setup_flatmemo()
    {
        umask(0000);

        $f = fileperms("{$this->_ff_config['data_dir']}/");
        $ff = sprintf('%o', $f);
        // $this->dump($ff);

        if (! file_exists("{$this->_ff_config['data_dir']}/data.cgi")) {
            touch("{$this->_ff_config['data_dir']}/data.cgi") or die("ディレクトリ：[{$this->_ff_config['data_dir']}]の権限を0777にしてください。");
        }
        touch("{$this->_ff_config['data_dir']}/lockfile") or die("エラー：[{$this->_ff_config['data_dir']}/lockfile]が作成できません。");
        touch("{$this->_ff_config['data_dir']}/category.cgi") or die("エラー：[{$this->_ff_config['data_dir']}/category.cgi]が作成できません。");
        touch("{$this->_ff_config['data_dir']}/tag.cgi") or die("エラー：[{$this->_ff_config['data_dir']}/tag.cgi]が作成できません。");
        touch("{$this->_ff_config['data_dir']}/appearance.cgi") or die("エラー：[{$this->_ff_config['data_dir']}/appearance.cgi]が作成できません。");

        // appearanceのデフォルト値をセット
        require_once "./flatframe/textdb.php";
        $appearance_dt = new textdb("./textdb.yml", "appearance", $this->_ff_config['data_dir'], 'cgi');
        $appearance_dt->insert(array(
            'appearance_id'           => 1,
            'bootswatch_name'         => 'cerulean',
            'navbar_name'             => 'default',
            'fm_t_id_kbn'             => 'number' ,
            'fm_t_id_icon_class_name' => 'fa fa-laptop' ,
            'view_attach_in_data_flg' => 1 ,
            'hide_edit_menu_flg'      => 0 ,
        ));

        // テストカテゴリをセット
        require_once "./flatframe/textdb.php";
        $category_dt = new textdb("./textdb.yml", "category", $this->_ff_config['data_dir'], 'cgi');
        $category_dt->insert(array(
            'category_id'   => 1,
            'category_name' => 'テストカテゴリ',
        ));


        @mkdir($this->_ff_config['icon_dir']);
        copy("{$this->_ff_config['css_dir']}/noicon.jpg", "{$this->_ff_config['icon_dir']}/noicon.jpg");

        @mkdir($this->_ff_config['archive_dir']);
        @mkdir($this->_ff_config['data_file_dir']);
        @mkdir($this->_ff_config['upload_tmp_dir']);

        header("Location:{$this->_ff_config['memo_uri']}");
    }



    //========== do_about
    public function do_about()
    {
        $this->_get_appearance();
        $this->template->assign($this->q);
        $this->template->display("temp_about.html");
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



    //========== _make_apath : version 1.0
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



    //========== _listmatch
    public function _listmatch($search_str_not_separated, $data)
    {

//$this->dump( $search_str_not_separated );
//$this->dump( $data );

        $search_str_not_separated = mb_convert_kana($search_str_not_separated, 's');
        $search_str_not_separated = preg_replace("{^ +}", '', $search_str_not_separated);
        $search_str_not_separated = preg_replace("{ +$}", '', $search_str_not_separated);

        $search_list = preg_split("{\s}", $search_str_not_separated);

        $flag=1;
        foreach ($search_list as $s) {
            $s = preg_quote($s, '/');
            if (! preg_match("/$s/iu", $data)) {
                $flag=0;
                return false;
            }
        }
        if ($flag==1) {
            return true;
        } else {
            return false;
        }
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
        //$this->dump( $hash );
        $this->template->assign(array("appearance" => $hash));
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



    //========== _check_whitelist
    public function _check_whitelist()
    {
        if ( @$this->_ff_config['whitelist'] ) {
            $checker = new Whitelist\Check();
            try {
                $checker->whitelist( $this->_ff_config['whitelist'] );
            }
            catch (InvalidArgumentException $e) {
                // thrown when an invalid definition is encountered
                return false;
            }

            if ( ! @$_SERVER['REMOTE_ADDR'] ){ return false; }
            $c = $checker->check($_SERVER['REMOTE_ADDR']);
            // $this->dump( var_export($c) );
            return $c;
        } else {
            return true;
        }
    }







}


$controller = new ff_memo("config.cgi");
$controller->run();

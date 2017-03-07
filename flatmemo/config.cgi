#===================================================================
# flatmemo設定ファイル（記述フォーマット：YAML）
# =================================================================== システム設定（変更しないでください）↓
version                : 'Ver 2.04 minimalauth導入<br><a href="http://flatsystems.net/econosys_system/" target="_blank">&copy;2016 econosys system</a>'
dbAutoConnect          : false
dbDefaultCharacterSet  : utf8
script_encoding        : UTF-8
output_encoding        : UTF-8
httpheader_encoding    : UTF-8
timerMode              : 0

errorReporting         : -1

use_path_info          : 1
use_config_cache       : 0
memo_file              : './memo.php'
admin_file             : './memo_admin.php'
css_dir                : './css'
icon_dir               : './data/icondir'
archive_dir            : './data/archives'
smarty_cache_dir       : './smarty_cache/'
data_dir               : './data'
data_file_dir          : './data/filedir'
upload_tmp_dir         : './data/tmp'
dropbox_consumer_key   : 'np21ch5ghn17ejv'
dropbox_consumer_secret: '5qh7pd3w7ig4o5o'
# =================================================================== システム設定（変更しないでください）↑



# =================================================================== 以下アプリケーション設定
# フラットメモ全体にパスワード認証をかけるときは設定してください。
flatmemo_all_password:

# Smarty（テンプレート）キャッシュを使用するか？使用すると表示が高速になります。（ 【1】時使用する    【0】使用しない ）
use_smarty_cache: 0

# htmlを生成して動作を高速化するか？ （ 【1】使用する    【0】使用しない ）
use_html_archive: 0

# use_html_archive:1 の時 すべてのhtmlを生成し直すか？（ 【1】生成する    【0】生成しない ）
recreate_html_archive: 0


# 管理者モード【1】の時データの登録、編集、削除時にパスワードを要求します
admin_use_password: 1

# 管理者パスワード（  管理者モード = 【1】の時有効 ）
admin_pass: 'password'

# 外部RSSフィードを使用する場合指定（通常は指定しません。）
rss_feed: ''

# 新規登録データのみRSSフィードに表示する（【1】新規登録のみ　【0】新規登録・編集どちらも）
view_only_add_feed: 1

# Tweetボタンを表示する【1】表示する　【0】表示しない
view_tweet_button: 1

# プライベートモード（リンク先のページにどこから来たか（REFERRER）を残さないようにする）【1】プライベートモードにする　【0】しない
private_link_mode: 0

# デフォルトの記法（autolink:標準記法   hatena:はてな記法   markdown:マークダウン記法）
default_kihou: 'markdown'

# 新規投稿時にTwitterにもつぶやく【1】つぶやく　【0】つぶやかない
tweet_new_entry         : 0
twitter_id              : ''
twitter_pass            : ''
twitter_consumer_key    : ''
twitter_consumer_secret : ''
twitter_oauth_token     : ''
twitter_oauth_verifier  : ''

# データ投稿時に画像ファイルのアップロードがあった場合一番下に自動表示する【1】自動表示する　【0】自動表示しない
view_attach_image       : 1

# DropBoxにデータのバックアップを作成する【1】作成する　【0】作成しない　　　【dropbox_base_dir】はDropbox上に作成するフォルダ名
# https://www.dropbox.com/developers/apply
backup_on_dropbox       : 0
dropbox_base_dir        : 'flatmemo'
# dropbox_id , dropbox_pass にあなたのDropboxの id ,password を入れてください
dropbox_id              : ''
dropbox_pass            : ''

# ページタイトル
page_title: 'フラットメモ'

# このサイトについて
site_about: 'シンプル軽量なメモシステム。flat memo'

# アクセス解析
access_analyzer :

# 広告（インデックスページ）
ad_index :

# 広告（コンテンツ）
ad_contents :

# 広告（クイックリンク下）
ad_quicklink :

# 広告（検索結果）
ad_search :

# セッション保存フォルダ（通常はデフォルトでOK）
session_save_path :

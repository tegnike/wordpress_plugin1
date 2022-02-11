<?php
/*
  Plugin Name: CustomIndexBanner
  Plugin URI:
  Description: indexページに表示可能なバナーの設定
  Version: 1.0.0
  Author: Tomoaki TANAKA
  Author URI: https://github.com/TomoakiTANAKA/CustomIndexBanner
  License: GPLv2
 */

add_action('init', 'CustomIndexBanner::init');

class CustomIndexBanner
{
    const VERSION           = '1.0.0';
    const PLUGIN_ID         = 'custom-index-banner';
    const CREDENTIAL_ACTION = self::PLUGIN_ID . '-nonce-action';
    const CREDENTIAL_NAME   = self::PLUGIN_ID . '-nonce-key';
    const PLUGIN_DB_PREFIX  = self::PLUGIN_ID . '_';
    const CONFIG_MENU_SLUG  = self::PLUGIN_ID . '-config';

    static function init()
    {
        return new self();
    }

    function __construct()
    {
        if (is_admin() && is_user_logged_in()) {
            // メニュー追加
            add_action('admin_menu', [$this, 'set_plugin_menu']);
            add_action('admin_menu', [$this, 'set_plugin_sub_menu']);
            add_action('admin_init', [$this, 'save_config']);
        }
    }

    function set_plugin_menu()
    {
        add_menu_page(
            'カスタムバナー',           /* ページタイトル*/
            'カスタムバナー',           /* メニュータイトル */
            'manage_options',         /* 権限 */
            'custom-index-banner',    /* ページを開いたときのURL */
            [$this, 'show_about_plugin'],       /* メニューに紐づく画面を描画するcallback関数 */
            'dashicons-format-gallery', /* アイコン see: https://developer.wordpress.org/resource/dashicons/#awards */
            99                          /* 表示位置のオフセット */
        );
    }

    function set_plugin_sub_menu() {
        add_submenu_page(
            'custom-index-banner',  /* 親メニューのslug */
            '設定',           /* ページタイトル*/
            '設定',           /* メニュータイトル */
            'manage_options',         /* 権限 */
            'custom-index-banner-config',    /* ページを開いたときのURL */
            [$this, 'show_config_form']       /* メニューに紐づく画面を描画するcallback関数 */
        );
    }

    function show_about_plugin() {
        $html = "<h1>カスタムバナー</h1>";
        $html .= "<p>トップページに表示するバナーを指定できます</p>";
  
        echo $html;
    }

    /** 設定画面の表示 */
    function show_config_form() {
        // wp_optionsのデータをひっぱってくる
        $title = get_option(self::PLUGIN_DB_PREFIX . "_title");
?>
        <div class="wrap">
            <h1>カスタムバナーの設定</h1>
    
            <form action="" method='post' id="my-submenu-form">
                <?php // nonceの設定 ?>
                <?php wp_nonce_field(self::CREDENTIAL_ACTION, self::CREDENTIAL_NAME) ?>
    
                <p>
                <label for="title">タイトル：</label>
                <input type="text" name="title" value="<?= $title ?>"/>
                </p>
    
                <p><input type='submit' value='保存' class='button button-primary button-large'></p>
            </form>
        </div>
<?php
    }

    /** 設定画面の項目データベースに保存する */
    function save_config() {
        // nonceで設定したcredentialのチェック 
        if (isset($_POST[self::CREDENTIAL_NAME]) && $_POST[self::CREDENTIAL_NAME]) {
            if (check_admin_referer(self::CREDENTIAL_ACTION, self::CREDENTIAL_NAME)) {

                // 保存処理
                $key   = 'title';
                $title = $_POST['title'] ? $_POST['title'] : "";

                update_option(self::PLUGIN_DB_PREFIX . $key, $title);
                $completed_text = "設定の保存が完了しました。管理画面にログインした状態で、トップページにアクセスし変更が正しく反映されたか確認してください。";

                // 保存が完了したら、wordpressの機構を使って、一度だけメッセージを表示する
                set_transient('aaa', $completed_text, 5);

                // 設定画面にリダイレクト
                $url = menu_page_url(self::CONFIG_MENU_SLUG);
                wp_safe_redirect($url);
            }
        }
    }
} // end of class

?>
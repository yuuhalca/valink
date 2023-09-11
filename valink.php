<?php

/**
 * 
 * Plugin Name: Valink
 * Version: 1.0.0
 * 
 */

defined('ABSPATH') || exit;

class ArmsTest
{
    static function init()
    {
        return new self();
    }
    function __construct()
    {
        add_action('admin_menu', [$this, 'setMenus']);
        add_action('admin_init', [$this, 'save']); // 追加
    }

    function setMenus()
    {
        add_menu_page('Valink', 'Valink', 'manage_options', 'arms-test', null, 'dashicons-list-view',  50);
        add_submenu_page('arms-test', '登録', '登録', 'manage_options', 'arms-test', [$this, 'add']);
    }
    
    function add()
    {
        include_once 'views/arms-test-add.php';
        exit;
    }
    function save()
    {
        // 保存処理
        if (!empty($_GET['action']) && $_GET['action'] == 'save') {
            // nonceのチェック
            if (!wp_verify_nonce($_POST['name_of_nonce_field'], 'arms-test-save')) {
                exit;
            }

            $args = array(
                'post_type'     =>  'product_variation',
                'meta_query'    =>  array(
                    array(
                        'key' => '_sku',
                        'value' =>  $_POST["hoge"]
                    )
                )
            );
            $the_query = new WP_Query($args);
            $id = $the_query->post->ID;
            $link = get_the_permalink($id);


            // メッセージ表示設定
            set_transient('arms-test', $link, 5);

            // リダイレクト
            wp_redirect(menu_page_url('arms-test', false));
            exit;
        }
    }
}
add_action('init', 'ArmsTest::init');
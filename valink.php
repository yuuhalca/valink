<?php

/**
 * Plugin Name:Valink
 * Plugin URI: プラグインのURL
 * Description:バリエーション商品単品のパーマリンクを取得する（任意のオプションが選択された状態のURLが取得できる）
 * Version: 1.5.2
 * Author: Yu Ishiga
 * Author URI: https://backcountry-works.com
 */

defined('ABSPATH') || exit;

class Valink
{
    static function init()
    {
        return new self();
    }
    function __construct()
    {
        add_action('admin_menu', [$this, 'setMenus']);
        add_action('admin_init', [$this, 'save']);
    }

    function setMenus()
    {
        add_menu_page('Valink', 'Valink', 'manage_options', 'Valink', null, 'dashicons-list-view',  50);
        add_submenu_page('Valink', '登録', '登録', 'manage_options', 'Valink', [$this, 'add']);
    }

    function add()
    {
        include_once 'views/Valink-get.php';
        exit;
    }
    function save()
    {
        if (!empty($_GET['action']) && $_GET['action'] == 'save') {
            if (!wp_verify_nonce($_POST['name_of_nonce_field'], 'Valink-save')) {
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

            set_transient('Valink', $link, 5);

            wp_redirect(menu_page_url('Valink', false));
            exit;
        }
    }
}
add_action('init', 'Valink::init');
<?php

/**
 * Plugin Name:Valink
 * Plugin URI: https://github.com/yuuhalca/valink.git
 * Description:バリエーション商品単品のパーマリンクを取得する（任意のオプションが選択された状態のURLが取得できる）
 * Version: 1.5.7
 * Author: Yu Ishiga
 * Author URI: https://backcountry-works.com
 */

defined('ABSPATH') || exit;

class VL_main_class
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
        
        if (!empty($_GET['action']) && sanitize_text_field($_GET['action']) == 'save') {
            if(!isset($_POST['name_of_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['name_of_nonce_field'])), 'Valink-save')) {
                exit;
            }

            $args = array(
                'post_type'     =>  'product_variation',
                'meta_query'    =>  array(
                    array(
                        'key' => '_sku',
                        'value' =>  sanitize_text_field($_POST["sku"])
                    )
                )
            );
            $the_query = new WP_Query($args);
            $id = $the_query->post->ID;
            $link = get_the_permalink($id);
            if ($link == ""){
                $args = array(
                    'post_type'     =>  'product',
                    'meta_query'    =>  array(
                        array(
                            'key' => '_sku',
                            'value' =>  sanitize_text_field($_POST["sku"])
                        )
                    )
                );
                $the_query = new WP_Query($args);
                $id = $the_query->post->ID;
                $link = get_the_permalink($id);
            }
            
            set_transient('VL_data_trans', $link, 5);

            wp_redirect(menu_page_url('Valink', false));
            exit;
        }
    }
}
add_action('init', 'VL_main_class::init');
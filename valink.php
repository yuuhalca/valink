<?php

/**
 * Plugin Name: Valink
 * Plugin URI: https://github.com/yuuhalca/valink.git
 * Description: バリエーション商品単品のパーマリンクを取得する（任意のオプションが選択された状態のURLが取得できる）
 * Version: 1.6.0
 * Author: Yu Ishiga
 * Author URI: https://backcountry-works.com
 * Text Domain: valink
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

// WooCommerceがアクティブであるかを確認
function valink_check_woocommerce_active() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'valink_woocommerce_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

// WooCommerceがアクティブでない場合の通知メッセージ
function valink_woocommerce_missing_notice() {
    echo '<div class="error"><p>' . esc_html__('Valink requires WooCommerce to be installed and active.', 'valink') . '</p></div>';
}

add_action('plugins_loaded', 'valink_check_woocommerce_active');

//翻訳ファイルの有効化
function valink_load_textdomain() {
    load_plugin_textdomain('valink', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'valink_load_textdomain');

function valink_enqueue_scripts($hook) {
    // 管理画面の「Valink」ページでのみ読み込む
    if ('toplevel_page_valink' === $hook) {
        wp_enqueue_script('valink-ajax', plugin_dir_url(__FILE__) . 'js/valink-ajax.js', ['jquery'], null, true);
        
        // admin-ajax.phpのURLをJavaScriptで利用できるようにローカライズ
        wp_localize_script('valink-ajax', 'valinkAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('admin_enqueue_scripts', 'valink_enqueue_scripts');

class VL_Main_Class
{
    public static function init()
    {
        return new self();
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'vl_set_menus']);
    }

    public function vl_set_menus()
    {
        add_menu_page(
            __('Valink', 'valink'),
            __('Valink', 'valink'),
            'manage_options',
            'valink',
            [$this, 'vl_add'],
            'dashicons-list-view',
            50
        );
    }

    public function vl_add()
    {
        include_once plugin_dir_path(__FILE__) . 'views/Valink-get.php';
    }

    
}

add_action('init', ['VL_Main_Class', 'init']);

// AJAX処理
function valink_get_link_ajax() {
    if (isset($_POST['sku']) && !empty($_POST['sku'])) {
        $sku = sanitize_text_field($_POST['sku']);
        $args = [
            'post_type' => 'product_variation',
            'meta_query' => [
                [
                    'key' => '_sku',
                    'value' => $sku
                ]
            ]
        ];

        $the_query = new WP_Query($args);
        $link = '';

        if ($the_query->have_posts()) {
            $the_query->the_post();
            $link = get_the_permalink(get_the_ID());
        } else {
            $args['post_type'] = 'product';
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) {
                $the_query->the_post();
                $link = get_the_permalink(get_the_ID());
            }
        }

        wp_reset_postdata();

        if ($link) {
            wp_send_json_success(['link' => $link]);  // 成功した場合
        } else {
            wp_send_json_error(['message' => __('Link could not be retrieved', 'valink')]);  // リンクが取得できなかった場合
        }
    } else {
        wp_send_json_error(['message' => __('SKU is required', 'valink')]);  // SKUが空の場合
    }

    wp_die(); // 必須
}

add_action('wp_ajax_valink_get_link', 'valink_get_link_ajax');
add_action('wp_ajax_nopriv_valink_get_link', 'valink_get_link_ajax');
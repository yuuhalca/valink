<?php

/**
 * Plugin Name: Valink
 * Plugin URI: https://github.com/yuuhalca/valink.git
 * Description: バリエーション商品単品のパーマリンクを取得する（任意のオプションが選択された状態のURLが取得できる）
 * Version: 1.6.3
 * Author: Yu Ishiga
 * Author URI: https://backcountry-works.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: valink
 * Domain Path: /languages
 * Requires at least: 6.3
 * Tested up to: 6.7
 * Requires PHP: 7.0
 * Requires Plugins: woocommerce
 */

defined('ABSPATH') || exit;

// WooCommerceがアクティブであるかを確認
function bcw_valink_check_woocommerce_active() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'bcw_valink_woocommerce_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

// WooCommerceがアクティブでない場合の通知メッセージ
function bcw_valink_woocommerce_missing_notice() {
    echo '<div class="error"><p>' . esc_html__('Valink requires WooCommerce to be installed and active.', 'valink') . '</p></div>';
}

add_action('plugins_loaded', 'bcw_valink_check_woocommerce_active');

//翻訳ファイルの有効化
function bcw_valink_load_textdomain() {
    load_plugin_textdomain('valink', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'bcw_valink_load_textdomain');

function bcw_valink_enqueue_scripts($hook) {
    // 管理画面の「Valink」ページでのみ読み込む
    if ('toplevel_page_bcw_valink' === $hook) {
        wp_enqueue_script('bcw-valink-ajax', plugin_dir_url(__FILE__) . 'js/valink-ajax.js', ['jquery'], null, true);

        // admin-ajax.phpのURLをJavaScriptで利用できるようにローカライズ
        wp_localize_script('bcw-valink-ajax', 'bcwValinkAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('admin_enqueue_scripts', 'bcw_valink_enqueue_scripts');

// 管理画面用にCSSを読み込む場合
function bcw_valink_admin_enqueue_styles() {
    wp_enqueue_style(
        'bcw-valink-styles', // ハンドル名
        plugin_dir_url(__FILE__) . 'css/valink-styles.css', // CSSファイルのURL
        array(), // 依存関係
        '1.0.0', // バージョン
        'all' // メディアタイプ
    );
}
add_action('admin_enqueue_scripts', 'bcw_valink_admin_enqueue_styles');

class BCW_Valink_Main_Class
{
    public static function init()
    {
        return new self();
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'bcw_valink_set_menus']);
    }

    public function bcw_valink_set_menus()
    {
        add_menu_page(
            __('Valink', 'valink'),
            __('Valink', 'valink'),
            'manage_options',
            'bcw_valink',
            [$this, 'bcw_valink_add'],
            'dashicons-list-view',
            50
        );
    }

    public function bcw_valink_add()
    {
        include_once plugin_dir_path(__FILE__) . 'views/Valink-get.php';
    }
}

add_action('init', ['BCW_Valink_Main_Class', 'init']);

// AJAX処理
function bcw_valink_get_link_ajax() {
    // Nonce検証
    if (isset($_POST['valink_nonce_field'])) {
        $nonce = sanitize_text_field(wp_unslash($_POST['valink_nonce_field'])); // スラッシュを取り除く
        // Nonceが正しいかを検証
        if (wp_verify_nonce($nonce, 'valink_nonce_action')) {
            if (isset($_POST['sku']) && !empty($_POST['sku'])) {
                // SKUのサニタイズ
                $sku = sanitize_text_field(wp_unslash($_POST['sku']));

                // キャッシュを使用するキーを作成
                $cache_key = 'valink_sku_' . md5($sku);
                $cached_link = get_transient($cache_key);

                if ($cached_link !== false) {
                    wp_send_json_success(['link' => $cached_link]); // キャッシュがあればそれを返す
                    return;
                }

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

                        // キャッシュにリンクを保存
                        set_transient($cache_key, $link, 12 * HOUR_IN_SECONDS); // 12時間キャッシ
                } else {
                    $args['post_type'] = 'product';
                    $the_query = new WP_Query($args);
                    if ($the_query->have_posts()) {
                        $the_query->the_post();
                        $link = get_the_permalink(get_the_ID());
                        // キャッシュにリンクを保存
                        set_transient($cache_key, $link, 12 * HOUR_IN_SECONDS); // 12時間キャッシ
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
        } else {
            wp_send_json_error(['message' => __('Invalid nonce.', 'valink')]);  // Nonceが無効
        }
    } else {
        wp_send_json_error(['message' => __('Nonce field is missing.', 'valink')]);  // Nonceが存在しない場合
    }

    wp_die(); // 必須
}

add_action('wp_ajax_bcw_valink_get_link', 'bcw_valink_get_link_ajax');
add_action('wp_ajax_nopriv_bcw_valink_get_link', 'bcw_valink_get_link_ajax');

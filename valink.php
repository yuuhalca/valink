<?php

/**
 * Plugin Name: Valink
 * Plugin URI: https://github.com/yuuhalca/valink.git
 * Description: バリエーション商品単品のパーマリンクを取得する（任意のオプションが選択された状態のURLが取得できる）
 * Version: 1.5.8
 * Author: Yu Ishiga
 * Author URI: https://backcountry-works.com
 * Text Domain: valink
 * Domain Path: /languages
 * Requires Plugins: WooCommerce
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

class VL_Main_Class
{
    public static function init()
    {
        return new self();
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'vl_set_menus']);
        add_action('admin_init', [$this, 'vl_save']);
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

    public function vl_save()
    {
        if (!empty($_GET['action']) && sanitize_text_field($_GET['action']) == 'save') {
            if (!isset($_POST['name_of_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['name_of_nonce_field'])), 'valink-save')) {
                wp_die(__('Nonce verification failed', 'valink'));
            }

            $sku = isset($_POST['sku']) ? sanitize_text_field($_POST['sku']) : '';
            if (empty($sku)) {
                wp_die(__('SKU is required', 'valink'));
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
                set_transient('VL_data_trans', $link, 5);
            } else {
                wp_die(__('Link could not be retrieved', 'valink'));
            }

            wp_safe_redirect(menu_page_url('valink', false));
            exit;
        }
    }
}

add_action('init', ['VL_Main_Class', 'init']);
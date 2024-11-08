<?php
if (!defined('ABSPATH')) exit;

$sku = isset($_POST["sku"]) ? esc_attr($_POST["sku"]) : '';
?>

<div class="wrap">
    <style>
        p.link-field {
            display: flex;
            background: white;
            padding: 10px;
            word-break: break-all;
            cursor: pointer;
            border-radius: 10px;
            box-shadow: 3px 3px 5px rgba(0, 0, 0, .3);
        }

        p.link-field:active {
            box-shadow: 1px 1px 5px rgba(0, 0, 0, .3);
        }

        p.link-field:hover {
            background: #eaeaea;
        }
    </style>
    <h1><?php esc_html_e('リンク取得', 'valink'); ?></h1>
    <p><?php esc_html_e('入力欄に品番を入れて商品のバリエーションへの直リンクを取得できます', 'valink'); ?></p>

    <form method="post" action="<?php echo esc_url(menu_page_url('valink', false) . '&action=save'); ?>">
        <?php wp_nonce_field('valink-save', 'name_of_nonce_field'); ?>
        <input type="text" name="sku" value="<?php echo esc_attr($sku); ?>" />
        <button class="button button-primary"><?php esc_html_e('取得', 'valink'); ?></button>
    </form>
    <?php if ($link = get_transient('VL_data_trans')) : ?>
        <p class="link-field"><?php echo esc_html($link); ?></p>
        <p class="copy"></p>
    <?php endif; ?>
    <script>
        jQuery(document).ready(function($) {
            $(".link-field").on("click", function() {
                let text = $(this).text();
                $(".copy").text("<?php esc_html_e('コピーしました', 'valink'); ?>");

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).catch(function() {
                        alert("<?php esc_html_e('クリップボードにコピーできませんでした', 'valink'); ?>");
                    });
                } else if (window.clipboardData) {
                    window.clipboardData.setData("Text", text);
                }
            });
        });
    </script>
</div>
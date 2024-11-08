<?php
if (!defined('ABSPATH')) exit;
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

    <form id="valink-form">
        <input type="text" id="sku" name="sku" />
        <button type="submit" class="button button-primary"><?php esc_html_e('取得', 'valink'); ?></button>
    </form>

    <div id="result"></div>
    <div class="copy"></div>
</div>
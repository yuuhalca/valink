<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1><?php esc_html_e('リンク取得', 'valink'); ?></h1>
    <p><?php esc_html_e('入力欄に品番を入れて商品のバリエーションへの直リンクを取得できます', 'valink'); ?></p>

    <form id="valink-form">
        <input type="text" id="sku" name="sku" />
        <button type="submit" class="button button-primary"><?php esc_html_e('取得', 'valink'); ?></button>
    </form>

    <div id="result"></div>
    <div class="copy" style="display:none"></div>
</div>
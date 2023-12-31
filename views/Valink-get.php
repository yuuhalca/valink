<?php
if (!defined('ABSPATH')) exit;

if(isset($_POST["sku"])){
    $sku = esc_html($_POST["sku"]);
}else{
    $sku = "";
}
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
    <h1>リンク取得</h1>
    <p>入力欄に品番を入れて商品のバリエーションへの直リンクを取得できます</p>

    <form method="post" action="<?php echo esc_url(menu_page_url('Valink', false)) . '&action=save'; ?>">
        <?php wp_nonce_field('Valink-save', 'name_of_nonce_field'); ?>
        <input type="text" name="sku" value="<?=$sku?>" />
        <button class="button button-primary">取得</button>
    </form>
    <?php if (get_transient('VL_data_trans')) : ?>
        <p class="link-field"><?php echo esc_html(get_transient('VL_data_trans')); ?></p>
        <p class="copy"></p>
    <?php endif; ?>
    <script>
        jQuery(".link-field").on("click", function() {
            let text = jQuery(".link-field").text();
            jQuery(".copy").text("コピーしました");

            if (navigator.clipboard == undefined) {
                window.clipboardData.setData("Text", text);
            } else {
                navigator.clipboard.writeText(text);
            }
        });
    </script>
</div>
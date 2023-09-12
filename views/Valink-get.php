<div class="wrap">
    <h1>登録</h1>

    <form method="post" action="<?php echo menu_page_url('Valink', false) . '&action=save'; ?>">
        <?php wp_nonce_field('Valink-save', 'name_of_nonce_field'); ?>
        <input type="text" name="hoge" value="">
        <button class="button button-primary">取得</button>
    </form>
    <?php if (get_transient('Valink')) : ?>
        <p class="link-field"><?php echo get_transient('Valink'); ?></p>
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
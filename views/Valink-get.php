<div class="wrap">
    <h1>登録</h1>
    <?php if (get_transient('Valink')) : ?>
        <p><?php echo get_transient('Valink'); ?></p>
    <?php endif; ?>
    <form method="post" action="<?php echo menu_page_url('Valink', false) . '&action=save'; ?>">
        <?php wp_nonce_field('Valink-save', 'name_of_nonce_field'); ?>
        <input type="text" name="hoge" value="">
        <button class="button button-primary">登録</button>
    </form>
</div>
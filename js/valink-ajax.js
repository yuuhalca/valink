jQuery(document).ready(function($) {
    $("#valink-form").on("submit", function(e) {
        e.preventDefault();

        var sku = $("#sku").val();

        if (sku) {
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "valink_get_link",
                    sku: sku
                },
                success: function(response) {
                    if (response) {
                        $("#result").html('<p class="link-field">' + response + '</p>');
                    } else {
                        $("#result").html('<p><?php esc_html_e("Link could not be retrieved", "valink"); ?></p>');
                    }
                },
                error: function() {
                    $("#result").html('<p><?php esc_html_e("Error occurred", "valink"); ?></p>');
                }
            });
        } else {
            $("#result").html('<p><?php esc_html_e("SKU is required", "valink"); ?></p>');
        }
    });

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
jQuery(document).ready(function($) {
    // フォームが送信されるときの処理
    $("#valink-form").on("submit", function(e) {
        e.preventDefault();

        var sku = $("#sku").val(); // SKUの値を取得

        if (sku) {
            $.ajax({
                url: bcwbcwValinkAjax.ajaxurl, // WordPress の admin-ajax.php にアクセス
                type: "POST",
                data: {
                    action: "valink_get_link", // PHPで定義されたアクション名
                    sku: sku, // フォームから送信された SKU
                    security: bcwValinkAjax.nonce // Nonce セキュリティチェック
                },
                success: function(response) {
                    // 成功時に取得したリンクを表示
                    if (response.success) {
                        $("#result").html('<p class="link-field">' + response.data.link + '</p>');
                    } else {
                        $("#result").html('<p>' + response.data.message + '</p>');
                    }
                },
                error: function() {
                    // エラー発生時にエラーメッセージを表示
                    $("#result").html('<p>' + bcwValinkAjax.error_message + '</p>');
                }
            });
        } else {
            // SKUが空の場合のメッセージ
            $("#result").html('<p>' + bcwValinkAjax.sku_required + '</p>');
        }
    });

    // リンクをクリックしてコピーする機能
    $("#result").on("click", function() {
        let text = $(this).text();
        $(".copy").text("コピーしました").fadeIn().delay(3000).fadeOut(); // コピー成功メッセージを表示

        // クリップボードにコピー
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).catch(function() {
                alert("コピーできませんでした"); // コピー失敗時のエラーメッセージ
            });
        } else if (window.clipboardData) {
            window.clipboardData.setData("Text", text);
        }
    });
});
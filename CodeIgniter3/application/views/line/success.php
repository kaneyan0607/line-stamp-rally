<!-- データ送信成功ページ -->

<html>

<head>
    <title>送信成功</title>
</head>

<body>
    <h3>おめでとうございます！あなたのアンケートはアップロードされました。</h3>

    <!-- URLヘルパーのachor()メソッドでリンクを作成 -->
    <p><?php echo anchor('posts/create', '入力を続ける'); ?></p>
</body>

</html>
<h3><?php echo 'POSTで受け取ったlineのid:' . $users['line_id'] . ' / 名前:' . htmlspecialchars($users['line_name']); ?></h3>
<p><?php echo 'アンケート結果:' . htmlspecialchars($users['answer']) . ' / スタンプの数:' . $users['cnt']; ?></p>
<?php echo 'データーベース登録日 : ' . $users['created_at']; ?>
<div class="main">
    <p><?php echo 'JSONENCODEした結果:' . json_encode($status); ?></p>
    <p><?php echo anchor('posts/search', '続けて検索をする'); ?></p>
</div>
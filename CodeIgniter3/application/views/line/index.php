<h3><?php echo '引数と照合したlineのid:' . $users['line_id'] . ' / 名前:' . $users['line_name']; ?></h3>
<p><?php echo 'アンケート結果:' . $users['answer'] . ' / スタンプ数:' . $users['stamp_result']; ?></p>
<div class="main">
    <?php echo $users['created_at']; ?>
</div>
<h2><?php echo $title; ?></h2>

<?php foreach ($users as $user) : ?>

    <h3><?php echo 'line_id:' . $user['line_id'] . ' / ' . $user['line_name']; ?></h3>
    <div class="main">
        <?php echo $user['created_at']; ?>
    </div>

<?php endforeach; ?>
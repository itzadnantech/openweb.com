<h3>Manage ShowMax subscriptions</h3>

<?php
if (isset($messages['success_message'])
    && trim($messages['success_message']) != '' ) {
    $m = $messages['success_message'];
    echo "<div class='alert alert-success'>$m</div>";
}

$topup_options = array (
    /*'create_showmax_subscription' => array (
        'title' => 'Create new ShowMax subscription',
        'function' => 'create_showmax_subscription',
        'description' =>
            'description sentence'
    ), */
    'showmax_subscriptions' => array (
        'title' => 'All ShowMax subscriptions',
        'function' => 'showmax_subscriptions',
        'description' =>
            'description sentence'
    ),

);
?>
<ul>
    <?php
    if (!empty($topup_options)) {
        foreach ($topup_options as $p=>$o) {
            $t = $o['title'];
            $f = $o['function'];
            $d = $o['description'];
            echo '<dl>';
            echo "<dt><a href='$p' >$t</a></dt>";
            echo "<dd>$d</dd>";
            echo '</dl>';
        }
    }
    ?>
</ul>
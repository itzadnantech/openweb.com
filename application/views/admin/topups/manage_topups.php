<h3>Manage TopUp's</h3>

<?php
if (isset($messages['success_message'])
    && trim($messages['success_message']) != '' ) {
    $m = $messages['success_message'];
    echo "<div class='alert alert-success'>$m</div>";
}

$topup_options = array (
    'create_topup' => array (
        'title' => 'Create a New TopUp configuration',
        'function' => 'create_topup',
        'description' =>
            'Create TopUp configurations which can be assigned to products'
    ),
    'all_topup' => array (
        'title' => 'List Existing TopUp configurations',
        'function' => 'all_topup',
        'description' =>
            'You can select a TopUp and edit or delete it'
    ),
    'topup_reports' => array (
        'title' => 'All TopUp orders',
        'function' => 'topup_reports',
        'description' =>
            'List of all TopUp orders which was made by users'
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
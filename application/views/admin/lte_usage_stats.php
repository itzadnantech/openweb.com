<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<h3>LTE Usage Stats</h3>
<div class="row">
    <div class="col-lg-10">
        <h5>Updated at: <span id="date"><?php echo $dateUpdate ?></span></h5>
    </div>
    <div class="col-lg-2">
        <button class="btn btn-primary" id="update">Update <span id="spiner" style="display: none"><i class="fas fa-sync fa-spin"></i></span></button>
    </div>

</div>

<div>
<?php
$tmpl = array (
    'table_open'  => '<table class="table">'
);
$this->table->set_template($tmpl);
$this->table->set_heading(array('Number',
    'Username',
    '<a href="/index.php/admin/lte_usage_stats/'.$uriFilter.'">Total Usage (MB)</a>'.$usageArrow));

foreach ($stats as $stat) {
    $this->table->add_row( array($stat['id'], $stat['username'], $stat['usage']));
}

echo $this->table->generate();
echo "<div class='pull-right'>$pages</div>";
?>
</div>

<script>
    $(document).ready(function () {
        $('#update').click(function () {
            $('#spiner').show();
            $('#update').prop( "disabled", true);
            $.ajax({
                url: '/index.php/admin/updateLteUsageStats'
            }).done(function (data) {
                if(data == 'ok')
                    document.location.reload(true);
            })
        });
    })
</script>
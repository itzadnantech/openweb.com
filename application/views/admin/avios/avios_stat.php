<script type="text/javascript" language="javascript">
    function check() {
        var r = $('#select-r').val();
        var s = $('#select-s').val();
        if (r || s) {
            $("#form_filter_award").submit();
        } else {
            return false;
        }
    }

    function goLink(key) {

        if(key == 'Prepared')
            window.location.href = '/index.php/admin/avios_stat';

        if(key == 'Sent')
            window.location.href = '/index.php/admin/avios_sent';

        if(key == 'summary')
            window.location.href = '/index.php/admin/avios_summary';
    }

</script>

<fieldset>
    <?php if(isset($er_message)) { ?>

        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('mes') ?>
        </div>
    <?php } ?>
<legend>Avios Awards</legend>
    <!-- Tab links -->
    <div class="tab">
        <button class="tablinks active" id="prep_tab" onclick="goLink('Prepared')">Prepared</button>
        <button class="tablinks" id="sent_tab" onclick="goLink('Sent')">Sent</button>
        <button class="tablinks" id="sum_tab" onclick="goLink('summary')">Summary</button>
    </div>

    <!-- Tab content -->
    <div id="Prepared" class="tabcontent">
        <?php include "prepared.php" ?>
    </div>
</fieldset>
<style>
    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons that are used to open the tab content */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: block;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }


</style>

<script type="text/javascript">
    window.onload = activeTab();
</script>
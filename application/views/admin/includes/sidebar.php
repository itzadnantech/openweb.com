<?php
$this->load->helper('sidebar');
$sidebar_details = get_sidebar();

$username = $this->session->userdata('username');
$first_name = $this->site_data['first_name'];

$role = $this->session->userdata('role');
$role_data = get_role_id($role);
?>
<div class="container-fluid">
    <div style="margin-bottom:10px;">
     <!--<div class="lead"><?php echo "Welcome, $first_name"; ?>.</div>--> 
    </div>
    <div class="well">
        <ul class="nav nav-pills nav-stacked">
            <?php 
            foreach($sidebar_details as $parent_role){ 
                $parent_accessor_ids = explode(',',$parent_role->allowed_access); 
                if(in_array($role_data['role_code'], $parent_accessor_ids)){
                ?>
                <li class="main-menu">
                    <a href="<?php echo base_url()."index.php/admin".$parent_role->url; ?>"><?php echo $parent_role->name; ?>
                    <?php if($parent_role->slug=='avios_bonus'){?><span class="badge badge-danger" id="new"></span><?php }?>
                    <b class="caret"></b></a>
                    <?php if(!empty($parent_role->sub)){ ?> 
                        <ul class="nav nav-pills nav-stacked" style="display: none;">
                            <?php foreach ($parent_role->sub as $child_role) { 
                            $child_accessor_ids = explode(',',$child_role->allowed_access); 
                            if(in_array($role_data['role_code'], $child_accessor_ids)){
                            ?>
                                <li class="sub-menu">
                                    <a href="<?php echo base_url()."index.php/admin".$child_role->url; ?>"><?php echo $child_role->name; ?></a>
                                </li>
                            <?php }} ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php }} ?>
            <li><a href="<?php echo base_url()."user/logout"; ?>">Log Out</a></li>
        </ul>
    </div>
</div>
<script>
    $(document).ready(function(){
        var url = window.location.pathname, 
        urlRegExp = new RegExp(url.replace(/\/$/,'') + "$");
        $('.main-menu a').each(function(){
            if(urlRegExp.test(this.href.replace(/\/$/,''))){
                $(this).parent('li').addClass('active');
                $(this).next('ul').css('display','block');
            }
        });
        $('.sub-menu a').each(function(){
            if(urlRegExp.test(this.href.replace(/\/$/,''))){
                $(this).parent('li').addClass('active');
                $(this).parent('li').parent('ul').css('display','block');
            }
        });
    });
    $(function () {
        $.ajax({
            type: "POST",
            url: "https://home.openweb.co.za//admin/get_nonbilling_count",
            xhrFields: {
                withCredentials: true
            }
        }).done(function (resp) {
            var num = "?";
         
            if(resp.hasOwnProperty('num')){
                num = resp['num'];
            }
         
            $("#new").html(num);
        });
    });
</script>
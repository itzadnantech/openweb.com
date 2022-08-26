<div class="alert role-success alert-success" role="alert" style="display:none;"> Data has been updated <strong>Successfully!</strong></div>
<button id="save" class="btn btn-primary" style="float: right !important;">Save Changes</button>
<h3>User Roles and Rights</h3>
<?php 
$pages = array();
foreach ( $parent as $value ) { 
    $pages[$value['tlm_id']] = $value['tlm_name'];
}
$subpages = array();
foreach ( $child as $value ) { 
    if(!isset($subpages[$value['tlm_id']])){
        $subpages[$value['tlm_id']][$value['cl_description']]=$value['cl_name'];
    }
    else {
        $subpages[$value['tlm_id']][$value['cl_description']]=$value['cl_name'];
    }
}
?>
<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">#</th>
            <!--<th scope="col">super_administrator</th>-->
            <th scope="col">Super Admin</th>
            <th scope="col">Admin</th>
            <th scope="col">Staff</th>
        </tr>
    </thead>
   <tbody>
        <?php foreach($roles_rights as $parent_role){ 
        $parent_accessor_ids = explode(',',$parent_role->allowed_access); ?>
            <tr>
                <th><?php echo $parent_role->name; ?></th>
                <!--<th><input type="checkbox" class="parent" value="<?php echo $parent_role->slug; ?>" par_accessor="7000" data-id="<?php echo $parent_role->id; ?>" checked disabled/></th>-->
                <th><input type="checkbox" class="parent" value="<?php echo $parent_role->slug; ?>" par_accessor="7001" data-id="<?php echo $parent_role->id; ?>" <?php if(in_array(7001, $parent_accessor_ids)){echo "checked";}?>/></th>
                <th><input type="checkbox" class="parent" value="<?php echo $parent_role->slug; ?>" par_accessor="7002" data-id="<?php echo $parent_role->id; ?>" <?php if(in_array(7002, $parent_accessor_ids)){echo "checked";}?>/></th>
                <th><input type="checkbox" class="parent" value="<?php echo $parent_role->slug; ?>" par_accessor="7003" data-id="<?php echo $parent_role->id; ?>" <?php if(in_array(7003, $parent_accessor_ids)){echo "checked";}?>/></th>
            </tr>
            <?php if(!empty($parent_role->sub)){
            foreach ($parent_role->sub as $child_role) { 
                $child_accessor_ids = explode(',',$child_role->allowed_access);?>        
                <tr>
                    <td><?php echo $child_role->name; ?></td>
                    <!--<td><input type="checkbox" class="child" value="<?php echo $child_role->slug; ?>" data-id="<?php echo $child_role->id; ?>" ch_accessor="7000" parent_id='<?php echo $child_role->parent_id; ?>' checked disabled/></td>-->
                    <td><input type="checkbox" class="child" value="<?php echo $child_role->slug; ?>" data-id="<?php echo $child_role->id; ?>" ch_accessor="7001" parent_id='<?php echo $child_role->parent_id; ?>' <?php if(in_array(7001, $child_accessor_ids)){echo "checked";}?>/></td>
                    <td><input type="checkbox" class="child" value="<?php echo $child_role->slug; ?>" data-id="<?php echo $child_role->id; ?>" ch_accessor="7002" parent_id='<?php echo $child_role->parent_id; ?>' <?php if(in_array(7002, $child_accessor_ids)){echo "checked";}?>/></td>
                    <td><input type="checkbox" class="child" value="<?php echo $child_role->slug; ?>" data-id="<?php echo $child_role->id; ?>" ch_accessor="7003" parent_id='<?php echo $child_role->parent_id; ?>' <?php if(in_array(7003, $child_accessor_ids)){echo "checked";}?>/></td>
                </tr>
            <?php }} ?>
        <?php } ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        var items = $('.child');
        $(".parent").click(function () {
            var par_accessor  = $(this).attr('par_accessor');
            var parent_id = $(this).attr('data-id');    
            if($(this).is(":checked")){
                $( ".child" ).each(function() {
                    if($(this).attr('ch_accessor') == par_accessor && $(this).attr('parent_id') == parent_id){
                        $(this).prop('checked', true);
                    }
                });
            }else{
                $( ".child" ).each(function() {
                    if($(this).attr('ch_accessor') == par_accessor && $(this).attr('parent_id') == parent_id){
                        $(this).prop('checked', false);
                    }
                });  
            }
        });
    });
    $(document).ready(function(){
        $('#save').click(function(){
            var myArray = [];
            $(".child").each(function(){
                myArray.push({
                    checked: $(this).is(":checked"),
                    role: $(this).val(),
                    role_id: $(this).attr('data-id'),
                    accessor_id: $(this).attr('ch_accessor'),
                });
            });
            $(".parent").each(function(){
                myArray.push({
                    checked: $(this).is(":checked"),
                    role: $(this).val(),
                    role_id: $(this).attr('data-id'),
                    accessor_id: $(this).attr('par_accessor'),
                });
            });
           
            $.ajax({
               type: "POST",
               url: "https://home.openweb.co.za/index.php/admin/save_user_roles",
               data: {"roles" : myArray},
              success: function(data)
              {
                    $('.role-success').fadeOut().fadeIn();
                    location.reload()
              }
            });
        })
    });
</script>
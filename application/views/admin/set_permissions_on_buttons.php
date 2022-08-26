<div class="alert role-success alert-success" role="alert" style="display:none;"> Data has been updated <strong>Successfully!</strong></div>
<button id="save" class="btn btn-primary" style="float: right !important;">Save Changes</button>
<h3>Button Roles and Rights</h3>
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
              <th scope="col">Page Name</th>
            <th scope="col">Button Name</th>
            <th scope="col">Super Admin</th>
            <th scope="col">Admin</th>
            <th scope="col">Staff</th>
        </tr>
    </thead>
   <tbody>
<?php foreach($roles_rights as $parent_role){ 
        $parent_accessor_ids = explode(',',$parent_role->access_granted); ?>
            <tr>
                <td><?php echo $parent_role->page_name; ?></td>
                <td><?php echo $parent_role->button_name; ?></td>
                <th><input type="checkbox" class="parent"  par_accessor="7001" data-id="<?php echo $parent_role->button_id; ?>" <?php if(in_array(7001, $parent_accessor_ids)){echo "checked";}?>/></th>
                <th><input type="checkbox" class="parent"  par_accessor="7002" data-id="<?php echo $parent_role->button_id; ?>" <?php if(in_array(7002, $parent_accessor_ids)){echo "checked";}?>/></th>
                <th><input type="checkbox" class="parent"  par_accessor="7003" data-id="<?php echo $parent_role->button_id; ?>" <?php if(in_array(7003, $parent_accessor_ids)){echo "checked";}?>/></th>
            </tr>
         
        <?php } ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        $('#save').click(function(){
            var myArray = [];
            $(".parent").each(function(){
                myArray.push({
                    checked: $(this).is(":checked"),
                    role_id: $(this).attr('data-id'),
                    accessor_id: $(this).attr('par_accessor'),
                });
            });
       
         
            $.ajax({
               type: "POST",
               url: "https://home.openweb.co.za/index.php/admin/button_permissions_roles",
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
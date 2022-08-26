<h3>Button Log</h3>
<div class="table-responsive">
<table class="table" id="example" style="width:100%">
    <thead>
        <tr>
            <th>Button Name</th>
            <th>Action By</th>
            <th>Role</th>
            <th>Action Time</th>
            <th>Other Information</th>
        </tr>
    </thead>
   <tbody>
    <?php foreach($logs as $log):?>
    <tr>
        <td><?= $log->button_name; ?></td>
          <td><?= $log->action_by; ?></td>
            <td><?= $log->role; ?></td>
              <td><?= $log->date; ?></td>
                <td><?= $log->other_details; ?></td>
    </tr>
        <?php endforeach;?>
    </tbody>
</table>    
    
</div>
<script>
$(document).ready(function() {
    $('#example').DataTable({
    "order": [[ 3, "desc" ]]    
    });
} );    
</script>

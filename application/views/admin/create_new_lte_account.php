<script language="javascript" type="text/javascript">
    $(function() {
        $.ajax({
            url: "/admin/get_class_name",
            dataType: "json",
            data: {
                user: '@openwebmobile'
            },
            success: function(data) {
                console.log(data.data.Classes)
                $('#class_id').empty();
                for (let i = 0; i < data.data.Classes.length; i++) {
                    $('#class_id').append('<option value="' + data.data.Classes[i]['ClassID'] + '">' + data.data.Classes[i]['Description'] + '</option>');
                }
            }
        });

    });
</script>

<?php

if (isset($messages['success_message']) && trim($messages['success_message']) != '') {
    $m = $messages['success_message'];
    echo "<div class='alert alert-success'>$m</div>";
}

if (isset($messages['error_message']) && trim($messages['error_message']) != '') {
    $error_message = $messages['error_message'];
    echo "<div class='alert alert-danger'>$error_message </div>";
}
?>
<h3 style="margin-bottom:30px;">Create new LTE Account with MTN SIM</h3>
<div class="container">
    <div class="row">
        <?php
        echo form_open('admin/submit_create_new_lte_account', array('class' => 'form-horizontal', 'id' => 'create_new_lte_account')) ?>
        <div class="col-lg-12">
            <!-- username -->
            <div class="form-group">
                <label class="col-lg-3">Username</label>
                <div class="col-lg-8">
                    <input class="form-control" type="text" name="username" id="username" required placeholder="username@realm">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3">Password</label>
                <div class="col-lg-8">
                    <input class="form-control" type="password" name="password" required id="password">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3">Email Address</label>
                <div class="col-lg-8">
                    <input class="form-control" type="text" name="email" id="email" required placeholder="Email Address from usage reports">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3">Class</label>
                <div class="col-lg-8">
                    <select class="form-control" name="class_id" id="class_id" required></select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3">SIM Number</label>
                <div class="col-lg-8">
                    <input class="form-control" name="sim" required id="sim" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3"> Device Serial Number</label>
                <div class="col-lg-8">
                    <input class="form-control" name="device_serial_no" required id="device_serial_no" placeholder="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="col-lg-3">ID Number</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="idnumber" required id="idnumber" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Contact Name</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="contact_name" required id="contact_name" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Contact Surname</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="contact_surname" required id="contact_surname" placeholder="">
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-lg-3">Tell Cell</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="tellcell" id="tellcell" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Address Type</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="addressType" id="addressType" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Building Type</label>
                    <div class="col-lg-8">
                        <select class="form-control" name="BuildingType" id="BuildingType">
                            <option value="Standing">Standing</option>
                            <option value="Semi-Detached">Semi-Detached</option>
                        </select>
                     </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Street Number</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="StreetNumber" id="StreetNumber" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Street</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="street" id="street" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Building</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="Building" id="Building" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Complex Name</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="ComplexName" id="ComplexName" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Unit Number</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="UnitNumber" id="UnitNumber" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Building Name</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="BuildingName" id="BuildingName" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Building Floor</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="BuildingFloor" id="BuildingFloor" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">Suburb</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="suburb" id="suburb" placeholder="">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-lg-3">City</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="city" id="city" placeholder="">
                    </div>
                </div>  
                <div class="form-group">
                    <label class="col-lg-3">Post Code</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="postcode" id="postcode" placeholder="">
                    </div>
                </div> 
                
                <div class="form-group">
                    <label class="col-lg-3">Latitude</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="latitude" id="latitude" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3">longitude</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="longitude" id="longitude" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align:center">
            <input type="submit" class="btn btn-primary btn-lg" value="Create new LTE account">
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
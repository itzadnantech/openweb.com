<div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3 class="col-md-offset-2">Order Form</h3>
        </div>
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="grid simple">
                    <div class="grid-title no-border">
                        <h4>Please, give us your information for order</h4>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                        <br>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <form action="/user/place_new_order" method="post" id="order_form">
                                    <div class="form-group" >
                                        <label class="form-label" >Name</label>
                                        <span class="help" ></span>
                                        <div class="controls" >
                                            <input type ="text" name="name" class="form-control" required>
                                        </div >
                                    </div>
                                    <div class="form-group" >
                                        <label class="form-label" >Surname</label>
                                        <span class="help" ></span>
                                        <div class="controls" >
                                            <input type = "text" name="second-name" class="form-control" required>
                                        </div >
                                    </div>
                                    <div class="form-group" >
                                        <label class="form-label" >Email</label>
                                        <span class="help" >mail@mail.com</span>
                                        <div class="controls" >
                                            <input type = "text" name="email" id="email" class="form-control" required>
                                        </div >
                                    </div>
                                    <div class="form-group" >
                                        <label class="form-label" >Phone</label>
                                        <span class="help" ></span>
                                        <div class="controls" >
                                            <input type = "text" name="phone" id="phone" class="form-control" required>
                                        </div >
                                    </div>
                                    <div class="form-group" >
                                        <label class="form-label" >Which package would you like to order?</label>
                                        <span class="help" ></span>
                                        <div class="controls" >
                                            <select name="package">
                                                <?php
                                                    foreach ($packages as $pack) {
                                                        echo '<option value="'.$pack['id'].'">'.$pack['name'].' - R'.$pack['price']." ".$pack['description'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div >
                                    </div>
                                    <?php
                                    foreach ($form_fields as $field) {
                                        $name = str_replace(' ', '', $field['name']);
                                        $name = str_replace('?', '', $name);

                                        if($field['val'] == 'num-let' || $field['val'] == 'let') {
                                        echo
                                        '<div class="form-group" >
                                            <label class="form-label" >'.$field['name'].'</label >
                                            <span class="help" >'.$field['desc'].'</span >
                                            <div class="controls" >
                                                <input type = "text" name="'.$name.'" class="form-control" required >
                                            </div >
                                        </div >';
                                        }
                                        if($field['val'] == 'num') {
                                            echo
                                                '<div class="form-group" >
                                            <label class="form-label" >'.$field['name'].'</label >
                                            <span class="help" >'.$field['desc'].'</span >
                                            <div class="controls" >
                                                <input type="text" name="'.$name.'" class="form-control auto" data-a-sep="," data-a-dec="." required>
                                            </div >
                                        </div >';
                                        }
                                        if($field['val'] == 'drop') {
                                            echo
                                                '<div class="form-group" >
                                            <label class="form-label" >'.$field['name'].'</label >
                                            
                                            <div class="controls" >
                                                <select name="'.$name.'">';

                                                foreach ($field['desc'] as $option) {
                                                    echo '<option >'.$option.'</option >';
                                                }

                                                echo '</select>
                                            </div >
                                        </div >';
                                        }
                                    }
                                    ?>
                                    <div class="form-group" >
                                        <div class="checkbox check-success 	">
                                            <input id="checkbox2" type="checkbox" value="1" name="mandate"  required>
                                            <label id="mandate" for="checkbox2">I agree with <a href="#">Electronic Debit Order / Credit Order Mandate</a></label>
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <div class="checkbox check-success ">
                                            <input id="checkbox1" type="checkbox" value="1" name="terms"  required>
                                            <label id="terms" for="checkbox1">I agree with <a href="http://openweb.co.za/terms-conditions/">Terms of Service</a></label>
                                        </div>
                                    </div>
                                    <input type="text" name="prod-id" value="<?php echo $prod_id ?>" hidden>
                                    <p id="check-err" style="color: red">You should check agree checkboxes</p>
                                    <div class="form-actions">
                                        <div class="pull-right">
                                            <button class="btn btn-success btn-cons" type="submit"  id="place" >Place Order</button>
                                            <a href="/user/dashboard"><button class="btn btn-white btn-cons"type="button">Cancel</button></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<script src="/js/jquery.form.min.js"></script>
<style>
    #add-link {
        font-size: 25px;
    }
</style>
<h3>Form Builder</h3>
<div class="">
<a href="/admin/manual_ordering_settings"><button class="btn btn-primary"><i class="fas fa-angle-left"></i> Return to Products Settings</button></a>
    <br><br>
</div>
    <?php
        echo form_open('#', array('class' => 'form-horizontal','id' => 'form_template'));
            echo form_fieldset();

            foreach ($base_form_fields as $field) {

                $data = [
                    'name'        => $field['name'],
                    'value'       => $field['value'],
                    'maxlength'   => '100',
                ];

                echo '<div class="form-group">';
                    echo '<label class="control-label col-lg-2">'.$field['title'].'</label>';
                    echo '<div class="col-lg-4">';
                        echo form_input($data, '', 'class="form-control" disabled');
                    echo '</div>';
                echo '</div>';
            }

            echo '<div class="form-group">';
                echo '<label class="control-label col-lg-2">Product Type</label>';
                echo '<div class="col-lg-4">';
                    echo form_dropdown('product_type', $type_options, $selected, 'class="form-control" id="product_type" disabled');
                echo '</div>';
            echo '</div>';

            echo '<div class="form-group">';
                echo '<label class="control-label col-lg-2">Packages</label>';
                echo '<div class="col-lg-4">';
                    echo form_dropdown('packages', $packages, '', 'class="form-control" id="packages"');
                echo '</div>';
            echo '</div>';

            echo form_fieldset_close();

        echo form_close();
        echo form_open('#', array('class' => 'form-horizontal','id' => 'form_fields'));
        echo "<hr>"; ?>

        <div class="col-lg-3">
            <h4>Field Name</h4>
        </div>
        <div class="col-lg-4">
            <h4>Field Placeholder</h4>
        </div>
        <div class="col-lg-5">
            <h4>Field Validation</h4>
        </div>
        <br><div id="sort"><hr id='divider'><br>
        <?php if(isset($form_fields)) {
            foreach ($form_fields as $key => $field) {
                echo '<div class="form-group add-group" id="' .$key.'">'
                .'<div class="col-lg-2">'
                .'<input type="text" name="n' .$key. '" value="'.$field['name'].'" maxlength="100" class="form-control f-input in-name">'
                .'</div>'
                .'<div class="col-lg-4">';

                if($field['val'] == 'drop') {
                    echo '<textarea class="form-control f-input in-desc">'.implode(", ", $field['desc']).'</textarea>';
                } else {
                    echo
                    '<input type="text" name="f' . $key . '" value="' . $field['desc'] . '" maxlength="100" class="form-control f-input in-desc">';
                }

                echo '</div>'
                .'<div class="col-lg-3">'
                .'<select name="val' .$key. '" class="form-control f-input in-val">'

                    .'<option value="num-let"'; if($field['val'] == 'num-let') {echo 'selected';} echo '>Numbers and Letters</option>'
                    .'<option value="let"'; if($field['val'] == 'let') {echo 'selected';} echo '>Only Letters</option>'
                    .'<option value="num"'; if($field['val'] == 'num') {echo 'selected';} echo '>Only Numbers</option>'
                    .'<option value="drop"'; if($field['val'] == 'drop') {echo 'selected';} echo '>Dropdown List</option>'
                .'</select>'
                .'</div>'
                .'<div class="col-lg-1">'
                .'<a href="#" class="del" id="' .$key. '"><i class="fa fa-times-circle"></i></a>'
                .'</div>'
                .'</div>';
            }
        }
        echo form_close();
        ?>
        </div>
        <div class="col-lg-12 text-center">
            <a href="#" id="add-link"><i class="fa fa-plus-circle"></i>Add Field</a>
        </div>

<div class="col-lg-12 text-center">
    <button class="btn btn-lg btn-success" id="save-btn">Save</button>

</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function () {

        $("#sort").sortable();
        $("#sort").disableSelection();

        var index = countFields();

        $("#add-link").click(function (e) {
            e.preventDefault();

            var field = '<div class="form-group add-group" id="'+index+'">' +
                    '<div class="col-lg-2">' +
                        '<input type="text" name="n'+index+'" value="" maxlength="100" class="form-control f-input in-name">' +
                    '</div>' +
                    '<div class="col-lg-4">' +
                        '<input type="text" name="f'+index+'" value="" maxlength="100" class="form-control f-input in-desc">' +
                    '</div>' +
                    '<div class="col-lg-3">' +
                        '<select name="val'+index+'" class="form-control f-input in-val">'+
                            '<option value="num-let">Numbers and Letters</option>'+
                            '<option value="let">Only Letters</option>'+
                            '<option value="num">Only Numbers</option>'+
                            '<option value="drop">Dropdown</option>'+
                        '</select>' +
                    '</div>' +
                    '<div class="col-lg-1">'+
                        '<a href="#" class="del" id="'+index+'"><i class="fa fa-times-circle"></i></a>'+
                    '</div>'+
                '</div>';

            if($("#"+(index-1)).length !== 0) {
                $("#" + (index - 1)).after(field);
            } else {
                $("#divider").after(field);
            }

            index++;

            //Remove row button(link)
            $(".del").on('click', function (ev) {
                ev.preventDefault();

                var ind = $(this).attr('id');

                $("#"+ind).remove();
            });
        });

        $("#form_fields").on('click', '.del', function (ev) {
            ev.preventDefault();

            var ind = $(this).attr('id');

            $("#"+ind).remove();
            $("#save-btn").removeClass('disabled');
            $("#save-btn").html('Save');
        });

        $("#save-btn").click(function () {

            var type = $("#product_type").val();
            var fields = [];

            $.each($(".add-group"), function (i, el) {

                var val = $(".in-val", el).val();
                var name = $(".in-name", el).val();
                var desc = $(".in-desc", el).val();
                if (desc.length == 0)
                    desc = $(".in-desc", el).html();


                if(name) {
                    var field = {name: name, desc: desc, val: val}; console.log(field);
                    fields.push(field);
                }

            });
            $.ajax({
                url: "saveForm",
                type: "post",
                data: {fields: fields, type: type},
                success: function (data) {
                   if(data === 'ok') {
                       $("#save-btn").addClass('disabled');
                       $("#save-btn").html('Saved');
                   }
                }
            })
        });

        $(".f-input").keyup(function () {
            $("#save-btn").removeClass('disabled');
            $("#save-btn").html('Save');
        });

        $("#form_fields").on('change', '.in-val', function () {
            $("#save-btn").removeClass('disabled');
            $("#save-btn").html('Save');

            var index = $(this).parent().parent().find(".in-name").attr("name").substr(1,2);
            var textarea = '<textarea class="form-control f-input in-desc" placeholder="Put your variants separated with \',\'"></textarea>';
            var input = '<input type="text" name="f'+index+'" value="" maxlength="100" class="form-control f-input in-desc">';

            if($(this).val() == 'drop') {
                $(this).parent().parent().find(".col-lg-4").html(textarea);
            } else {
                $(this).parent().parent().find(".col-lg-4").html(input);
            }
        });
    });

    function countFields() {
        var counter = 0;

        $(".add-group").each(function () {
            counter++;
        });

        if (counter === 0)
            return 0;

        return counter;
    }
</script>

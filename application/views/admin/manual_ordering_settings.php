<style>
  .group-header {
      background-color: #989ba373;
  }
</style>
<h3>Manual Ordering Settings</h3>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-5 group-tag">
            <h4 class="text-center">Service Type</h4>
            <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-default" id="add-type-button" data-toggle="modal" data-target="#prod-type-modal">Add</button>
                <button type="button" class="btn btn-danger delete-button disabled" id="delete-type-button">Delete</button>
                <button type="button" class="btn btn-warning disabled" id="edit-type-button">Edit</button>
                <button type="button" class="btn btn-success disabled" id="edit-form-button">Edit Form</button>

            </div>
            <br><br>
            <div class="list-group" id="type-list">
                <p class="list-group-item group-header" id="type-header">
                    Choose available type
                </p>
                <?php
                    foreach ($order_types as $type) {
                        echo '<a href="#" class="list-group-item type-button" id="t'.$type['id'].'">'.$type['name'].'</a>';
                    }
                ?>
            </div>
        </div>

        <div class="col-lg-7 group-tag">
            <h4 class="text-center">Available Products</h4>
            <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-default disabled" data-toggle="modal" data-target="#prod-modal" id="add-product-button">Add</button>
                <button type="button" class="btn btn-warning disabled" id="edit-product-button">Edit</button>
                <button type="button" class="btn btn-danger delete-button disabled" id="delete-product-button">Delete</button>
            </div>
            <br><br>
            <div class="list-group" id="product-list">
                <p class="list-group-item group-header" id="product-header">
                    <span id="order-type">Choose Type</span>
                </p>

            </div>
        </div>
    </div>
</div>
<!-- Type Modal -->
<div class="modal fade" id="prod-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Type</h4>
            </div>
            <div class="modal-body">
                <form id="type-form">
                    <div class="form-group ">
                        <label for="nam" class="group-label col-md-3">Name</label>
                        <div class="col-md-6">
                            <input type="text" name="name" id="nam" class="form-control">
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="db-name" class="group-label col-md-3">DB name</label>
                        <div class="col-md-6">
                            <input type="text" name="db_name" id="db-name" class="form-control">
                        </div>
                    </div>
                    <input type="text" name="type_id" class="type-id type-modal" hidden>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary save-button" id="save-type">Save</button>
                <button type="button" class="btn btn-primary save-button" id="edit-type-submit">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="prod-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Product</h4>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <div class="form-group ">
                        <label for="nam" class="group-label col-md-3">Name</label>
                        <div class="col-md-6">
                            <input type="text" name="name" id="prod-name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="db-name" class="group-label col-md-3">Description</label>
                        <div class="col-md-6">
                            <input type="text" name="description" id="description" class="form-control">
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="db-name" class="group-label col-md-3">Price</label>
                        <div class="col-md-6">
                            <input type="number" name="price" id="price" class="form-control">
                        </div>
                    </div>
                    <input type="number" name="type_id" id="type_id" hidden>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary save-button" id="save-product">Save</button>
                <button type="button" class="btn btn-primary save-button" id="edit-product-form">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p id="del-message">Are you sure want to delete?</p>
                <input type="text" id="data-del" value="0" hidden>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="del-cancel-button">Cancel</button>
                <button type="button" class="btn btn-danger" id="delete-button">Delete</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $("#type-list").on('click', '.type-button', function (e) {
            e.preventDefault();

            $("#edit-product-button").addClass('disabled');
            $("#delete-product-button").addClass('disabled');
            $("#add-product-button").removeClass('disabled');
            $("#delete-type-button").removeClass('disabled');
            $("#edit-type-button").removeClass('disabled');
            $("#edit-form-button").removeClass('disabled');

            unactiveAll(this.closest('.list-group'));
            $(this).addClass('active');
            var id = $(this).attr('id');
            var ind = id.substr(1, id.length);
            var typeName = $(this).html();

            $.ajax({
                url: 'getProducts',
                data: {id: ind},
                beforeSend: function () {
                    $("#order-type").html('Loading...')
                },
                success: function (data) {
                    var arr = JSON.parse(data);
                    $("#order-type").html(typeName+' Products');
                    $(".product-link").remove();

                    $.each(arr, function (i, el) {
                        $("#product-header").after('<a href="#" class="list-group-item product-link" id="p'+el['id']+'">'+el['name']+' - '+
                            el['description']+' R'+el['price']+'</a>');
                    });


                }
            })

        });

        $('#product-list').on('click', '.product-link', function (e) {
            e.preventDefault();

            unactiveAll(this.closest('.list-group'));
            $(this).addClass('active');

            $("#edit-product-button").removeClass('disabled');
            $("#delete-product-button").removeClass('disabled');
        });
        
        $("#add-product-button").click(function () {
            var selector = $(this).parents('.col-lg-12').find(".col-lg-5 .active");
            var idHtml = $(selector).attr('id');
            var id = idHtml.substr(1, idHtml.length);

            $(".err-mess").remove();

            $("#edit-product-form").hide();
            $("#save-product").show();

            $("#type_id").val(id);
        });

        $("#edit-product-button").click(function () {
            var selector = $(this).parents('.group-tag').find('.active');
            var idHtml = $(selector).attr('id');
            var id = idHtml.substr(1, idHtml.length);

            $(".err-mess").remove();

            $("#edit-product-form").show();
            $("#save-product").hide();

            $("#type_id").val(id);

            $.ajax({
                url: 'getProductFields',
                type: 'get',
                data: {id: id},
                success: function (resp) {
                    var name = JSON.parse(resp).name;
                    var desc = JSON.parse(resp).description;
                    var price = JSON.parse(resp).price;

                    $("#prod-name").val(name);
                    $("#description").val(desc);
                    $("#price").val(price);

                    $("#prod-modal").modal('show');
                }
            });

        });


        $("#add-type-button").click(function () {
            $(".err-mess").remove();
            $("#edit-type-submit").hide();
            $("#save-type").show();
        });


        $("#edit-type-button").click(function (e) {

            $("#save-type").hide();
            $("#edit-type-submit").show();
            $(".err-mess").remove();

            var selector = $(this).parents('.group-tag').find('.active');
            var idHtml = $(selector).attr('id');
            var id = idHtml.substr(1, idHtml.length);
            $(".type-id.type-modal").val(id);

            $.ajax({
                url: 'getTypeFields',
                type: 'get',
                data: {id: id},
                success: function (resp) {
                    var name = JSON.parse(resp).name;
                    var db_name = JSON.parse(resp).db_name;

                    $("#nam").val(name);
                    $("#db-name").val(db_name);

                    $("#prod-type-modal").modal('show');
                }
            });
        });

        $("#edit-type-submit").click(function () {

            var id = $(".type-id.type-modal").val();
            var name = $("#nam").val();
            var db_name = $("#db-name").val();

            $.ajax({
                url: 'editType',
                type: 'post',
                data: {id: id, name: name, db_name: db_name},
                success: function (resp) {
                    if(resp == 'ok') {
                        $(".err-mess").remove();
                        $("#type-form").before('<p class="err-mess" style="color: green"> Edited </p>');
                    } else {
                        $(".err-mess").remove();
                        $("#type-form").before('<p class="err-mess" style="color: red"> Please try again </p>');
                    }
                }
            });
        });

        $("#edit-product-form").click(function (e) {

            var id = $("#type_id").val();
            var name = $("#prod-name").val();
            var desc = $("#description").val();
            var price = $("#price").val();

            $.ajax({
                url: 'editProduct',
                type: 'post',
                data: {id: id, name: name, description: desc, price: price},
                success: function (resp) {
                    if(resp == 'ok') {
                        $(".err-mess").remove();
                        $("#product-form").before('<p class="err-mess" style="color: green"> Edited </p>');
                    } else {
                        $(".err-mess").remove();
                        $("#product-form").before('<p class="err-mess" style="color: red"> Try again </p>');
                    }
                }
            });
        });

        $(".modal-footer").on('click', '#edit-type-save', function () {

            var data = $("#type-form").serialize();
            console.log(data);
        });

        $("#save-type").click(function (e) {

            var name = $("#nam").val();
            var db_name = $("#db-name").val();

            if(name.length > 3 && db_name.length > 3) {
                $.ajax({
                    url: 'saveProductType',
                    type: 'post',
                    data: {name: name, db_name: db_name},
                    success: function (data) {
                        var resp = JSON.parse(data);

                        if (resp.status === 'ok') {

                            $("#type-form").before("<p class='text-center modal-message' id='suc-mess'>Success</p>");
                            $("#save-type").addClass('disabled');
                            $("#type-header").after('<a href="#" class="list-group-item type-button" id="'+resp.id+'">'+name+'</a>');

                        } else {
                            $("#type-form").html("</span><p class='text-center modal-message'>Some error. Try again</p>");
                        }
                    }
                });
            } else {
                $(".err-mess").remove();
                $("#nam").before('<p class="err-mess" style="color: red"> Name and DB name should be at least 4 characters</p>');
            }

        });

        $("#save-product").click(function (e) {

            var name = $("#prod-name").val();
            var description = $("#description").val();
            var price = $("#price").val();
            var type = $("#type_id").val();

            if(name.length > 3 && price > 0) {
                $.ajax({
                    url: 'saveProduct',
                    type: 'post',
                    data: {name: name, description: description, price: price, type_id: type},
                    success: function (data) {
                        var resp = JSON.parse(data);

                        if (resp.status === 'ok') {

                            $("#product-form").before("<p class='text-center modal-message' id='suc-mess'>Success</p>");
                            $("#save-product").addClass('disabled');
                            $("#product-header").after('<a href="#" class="list-group-item product-link" id="'+resp.id+'">'+name+'</a>');

                        } else {
                            $("#product-form").html("</span><p class='text-center modal-message'>Some error. Try again</p>");
                        }
                    }
                });
            } else {
                $(".err-mess").remove();
                $("#prod-name").before('<p class="err-mess" style="color: red"> Name should be at least 4 characters and price greater 0');
            }

        });

        $('.modal').on('hidden.bs.modal', function () {
            $(".save-button").removeClass('disabled');
            $(".modal :input").val('');
            $(".modal-message").remove();
        });

        $('#prod-modal').on('hidden.bs.modal', function () {

        });
        
        $(".delete-button").click(function () {
            $("#del-message").html('<p>Are you sure want to delete?</p>');
            $("#delete-button").show();
            $("#del-cancel-button").html("Cancel");

            var selector = $(this).parents('.group-tag').find('.active');
            var idHtml = $(selector).attr('id');
            var id = idHtml.substr(1, idHtml.length);
            var type = idHtml.substr(0, 1);
            var data = {type: type, id: id};

            $("#data-del").val(JSON.stringify(data));

            $('#delete-modal').modal({
                keyboard: false
            });
        });

        $("#delete-button").click(function () {
            var data = $("#data-del").val();
            var parsed = JSON.parse(data);
            var id = parsed['type']+parsed['id'];

            $.ajax({
                url: 'deleteSpecObject',
                data: parsed,
                success: function (resp) {
                    if(resp === '1') {

                        $("#del-message").html('<p>Deleted</p>');
                        $("#delete-button").hide();
                        $("#del-cancel-button").html("Ok");
                        $("#"+id).remove();
                    }
                }
            });
        });

        $("#edit-form-button").click(function () {
            var selector = $(this).parents('.group-tag').find('.active');
            var idHtml = $(selector).attr('id');
            var id = idHtml.substr(1, idHtml.length);

            window.location.href = "/admin/order_form_builder?id="+id;
        });
    });


    function unactiveAll(link) {

        $(link).find(".active").removeClass('active');
    }

</script>
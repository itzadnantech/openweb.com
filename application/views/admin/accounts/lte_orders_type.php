<h3>Prepared</h3>

<?php
if ($num_per_page > $num_account) {
    $num_per_page = $num_account;
}

$options = '<option value="0"></option>';
foreach ($lte_types as $type) {
    $options .= '<option value="'.$type['type'].'">'.$type['name'].'</option>';
}

if (!empty($orders)) {
    echo "<div class='pull-right'>$showing</div>";
    $tmpl = array ( 'table_open'  => '<table id="table" class="table">' );
    $this->table->set_template($tmpl);
    $this->table->set_heading(array('Fibre ID', 'Order ID', 'Username','Product Name' ,'LTE Type'));

    foreach ($orders as $order) {
        $order_id = $order['id'];
        $fibre_id = $order['order_id'];
        $username = $order['username'];
        $product = $order['product_name'];

        $list = '<select class="list" onchange="add_type('.$order_id.')" id="'.$order_id.'">'.$options.'</select>';

        $this->table->add_row( array( $order_id, $fibre_id, $username, $product, $list));
    }

    echo $this->table->generate();
    echo "<div class='pull-right'>$pages</div>";
}else{
    ?>
    <div class="alert alert-warning">
        <strong>Orders not found.</strong> <?php echo $priority_flag['message']; ?>
    </div>
    <?php
}
?>
<script type="text/javascript">
    //var tableData = parseId(); console.log(tableData[0]["Fibre ID"]);
    setInterval(update_data, 10000);

    assignID();

    function assignID() {

        $('#table tr').each(function()
        {
            var row = $(this).html();
            var tag = row.substring(1, 5);

            if(tag == "<td>") {
                var id = row.substring(5, row.indexOf("</td>"));

                $(this).attr("id", "tr_"+id);
            }

        });
    }

    function add_type(id) {

       var type = $("#"+id).val();

       $.ajax({
           url: "add_lte_type",
           data: {type : type, id: id}
       }).done(function (data) {

           data = JSON.parse(data);
           $("#tr_"+data.id).hide("slow");
       });
    }

    function update_data() {

        var tableData = parseId();

        $.ajax({
            url: "get_new_lte_orders"
        }).done(function (data) {

            var newData = JSON.parse(data);

            if(tableData.length != newData.length) {
                addNewRows(tableData, newData);
            }

        });

    }

    function addNewRows(tableData, newData) {

        newData.forEach(function (newElment) {
            var exists = false;
            tableData.forEach(function (oldElement) {
                if(newElment.id == oldElement["Fibre ID"]) {
                    exists = true;
                }
            })

            if(!exists) {

                $('#table tr:last').after('<tr id="tr_'+newElment.id+'">' +
                    '<td>'+newElment.id+'</td>'+
                    '<td>'+newElment.order_id+'</td>'+
                    '<td>'+newElment.username+'</td>'+
                    '<td>'+newElment.product_name+'</td>'+
                    '<td><select class="list" onchange="add_type('+newElment.id+')" id="'+newElment.id+'"><option value="0"></option><option value="rain">RAIN</option><option value="cell_c">Cell C</option></select></td>'+
                    '</tr>');
            }
        })
    }

    function parseId() {

        var table = document.querySelector("table");

        var headings = arrayify(table.tHead.rows[0].cells).map(function(heading) {
            return heading.innerText;
        });
        return arrayify(table.tBodies[0].rows).map(factory(headings));
    }

    function factory(headings) {
        return function (row) {
            return arrayify(row.cells).reduce(function (prev, curr, i) {
                prev[headings[i]] = curr.innerText;
                return prev;
            }, {});
        }
    }

    function arrayify(collection) {
        return Array.prototype.slice.call(collection);
    }

</script>

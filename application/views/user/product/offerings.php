<?php
 
 // There is no sidebar on this page, so it's got to look good and reasonably centered.
 
 if (isset($this->site_data['discount'])) {
	 $discount = $this->site_data['discount'];
 } else {
	 $discount = '';
 }
?>
<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">

<div class="container-fluid">
	<h3>Choose Your <span class="text-info"><?php echo $subcategory; ?></span> Solution</h3>
	
	<?php
	//print_r($products);
	
	if (!empty($products)) { $num = count($products);?>
			<table class="prod-table table table-striped">
				<thead class="products-head">
					<th style="background:white;"></th>
				<?php foreach ($products as $i=>$p) { 
				$n = $p['name']; ?>
					<th class="<?php if ($i == 0) { echo ' prod-first'; } else if ($i == ($num - 1)) { echo ' prod-last'; } ?>"><?php echo $n ?></th>
				<?php } ?>
				</thead>
				<tbody>
					<tr>
						<td class="prod-col-hd prod-col-first">Package Speed</td>
				<?php
					foreach ($products as $p) {
						$pr = $p['package_speed'];  ?>
						<td><?php echo $pr ?></td>
					<?php } ?>
					</tr>
					<tr>
						<td class="prod-col-hd">Service Level</td>
					<?php 
					foreach ($products as $p) { 
						$t = $p['service_level'];
						?><td><?php echo $t ?></td>
					<?php } ?>
					</tr>
					<tr>
						<td class="prod-col-hd">Recommended Use</td>
					<?php
					foreach ($products as $p) { 
						$f = $p['recommended_use']; ?>
						<td><?php echo $f ?></td>
					<?php } ?>
					</tr>
					<tr>
						<td class="prod-col-hd">Global Backbone</td>
					<?php
					foreach ($products as $p) { 
						$f = $p['global_backbone']; ?>
						<td><?php echo $f ?></td>
					<?php } ?>
					</tr>
					<tr>
						<td class="prod-col-hd">Billing Cycle</td>
					<?php
					foreach ($products as $p) {

						if (!empty($p['billing_occurs_on'])) {
							$ff = $p['billing_occurs_on'];
						} else {
							$ff = 'Information On Request';
						}
						?>
						<td><?php echo $ff ?></td>
					<?php } ?>
					</tr>
                    <tr>
                        <td class="prod-col-hd">About Product</td>
                        <?php
                        foreach ($products as $p) {

                            if (!empty($p['desc'])) {
                                $ff = $p['desc'];
                            } else {
                                $ff = '';
                            }
                            ?>
                            <td><?php echo $ff ?></td>
                        <?php } ?>
                    </tr>
					<tr>
						<td class="prod-col-hd prod-col-last">Cost</td>
					<?php
					foreach ($products as $p) { 
						$f = $p['price'];
						?>
						<td>R<?php echo $f ?></td>
					<?php } ?>
					</tr>
					<tr>
					<td style="background: white;"></td>
					<?php 
					foreach ($products as $p) {
						$i = $p['id']; ?>
						<td style="padding: 15px;text-align:center;background-color: #fbfbfb;">
						<?php
						echo anchor($p['form_link'], 'Order Now', 'class="btn btn-primary order-b"');
						?>
					<?php } ?>
					</tr>
				</tbody>
			</table>
			<?php
	}
	?>
    <div class="modal fade" tabindex="-1" id="error_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">We are sorry</h4>
                </div>
                <div class="modal-body">
                    <div class="grid simple">
                        <div class="grid-body no-border">
                            <p>We are currently redeveloping the order process. Please email <span class="semi-bold">admin@openweb.co.za</span> to place an order.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div>
</div>
</div>
</div>
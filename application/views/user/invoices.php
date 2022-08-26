<style type="text/css">
.ui-datepicker-calendar {
    display: none;
}
</style>
<div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
            <h3>All Tax Invoices for <?php echo $user_name;?></h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple ">
                    <div class="grid-title no-border">

                    </div>
                    <div class="grid-body no-border">
                        <?php if (isset($invoices) && !empty($invoices)) { ?>
                        <table class="table no-more-tables">
                            <thead>
                            <tr>
                                <th style="width:9%">Invoice ID</th>
                                <th style="width:22%">Invoice</th>
                                <th style="width:6%">Date Ordered</th>
                                <th style="width:10%">Download</th>
                                <th style="width:10%">Send Email</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($invoices as $or) {
                                ?>
                                <tr>
                                    <td class="v-align-middle"><?php echo $or['id'] ?></td>
                                    <td class="v-align-middle"><a href="<?php echo base_url().$or['pdf_path'];?>" target = "_black"><?php echo $or['invoice_name'] ?></a></td>
                                    <td><?php echo date('d/m/Y', strtotime($or['create_date'])) ?></td>
                                    <?php
                                    $seg = $this->uri->segment(2, 0);
                                    if($seg == 'invoices'){
                                        ?>
                                        <td class="v-align-middle"><?php echo anchor('user/down_pdf/'.$or['id'], 'Download', 'class="btn btn-default" target = "_black"');?></td>
                                        <td class="v-align-middle"><?php echo anchor('user/send_pdf/'.$or['id'], 'Send Email', 'class="btn btn-default" target = "_black"');?></td>
                                    <?php  } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php }else{
                            echo "<div class='alert alert-info'>There has no oders record this month.</div>";
                        } ?>
                        <div><?php echo INVOICE_VAT_ROW; ?></div>
                </div>

            </div>
        </div>
    </div>
</div>
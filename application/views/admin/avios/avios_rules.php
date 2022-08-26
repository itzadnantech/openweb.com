<?php if(isset($message)) { ?>
<div class="alert " id="error_mes">
    <?php echo $message ?>
</div>
<?php } ?>
<legend>Edit Rules For Avios Billing Codes</legend>
<fieldset>
    <?php echo form_open('admin/avios_rules', array('class' => 'form-horizontal','id' => 'form_filter_award')); ?>

    <div class="form-group">
        <label class="control-label col-lg-4">Fibre Monthly Rental (OPNZAFIBMR)</label>
        <div class="col-lg-4">
            1 avios per <input type="text" id="OPNZAFIBMR" name="OPNZAFIBMR" size="1" value="<?php echo $rules[4]['m_rule'] ?>"> R
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4">Uncapped ADSL Monthly Rental (OPNZAUBAMR)</label>
        <div class="col-lg-4">
            1 avios per <input type="text" id="OPNZAUBAMR" name="OPNZAUBAMR" size="1" value="<?php echo $rules[9]['m_rule'] ?>"> R
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4">Uncapped Fibre Monthly Rental (OPNZAUBFMR)</label>
        <div class="col-lg-4">
            1 avios per <input type="text" id="OPNZAUBFMR" name="OPNZAUBFMR" size="1" value="<?php echo $rules[10]['m_rule'] ?>"> R
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4">Mobile data package subscription (OPNZAMMDPS)</label>
        <div class="col-lg-4">
            1 avios per <input type="text" id="OPNZAMMDPS" name="OPNZAMMDPS" size="1" value="<?php echo $rules[6]['m_rule'] ?>"> R
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4">Fibre Router (OPNZAFIBRT)</label>
        <div class="col-lg-4">
            once <input type="text" id="OPNZAFIBRT" name="OPNZAFIBRT" value="<?php echo $rules[0]['once_points'] ?>"> avios
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4">ADSL Line Rental (OPNZALINRT)</label>
        <div class="col-lg-4">
            once <input type="text" id="OPNZALINRT" name="OPNZALINRT" value="<?php echo $rules[5]['once_points'] ?>"> avios
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-4"></label>
        <div class="col-lg-2">
            <input type="submit" class="btn btn-md btn-primary" value="Edit"/>
        </div>
    </div>

    <?php echo form_close();?>
</fieldset>

<div class="modal-header">
        <button type="button" class="close" onclick="close_model();">&times;</button>
</div>
<div class="modal-body">
<label>Are you wnat to import data from finas.k12.no for this project?</label>
<label>If you press yes, there will <?php echo count($list_invoices);?> Invoices imported , If you like to see which invoice. That will be done. Press LOG.</label>
<input type='hidden' id='status_show_log' value='0'>
<div class="row">
<div class="col-md-4"><a class="btn btn-sm btn-default" onclick='show_loglist_data()'>LOG</a></div>
<div class="col-md-7 text-right">
<input type="hidden" id="data_update" value='<?php echo json_encode($list_invoices);?>' ?>
    <a class="btn btn-sm btn-primary" onclick="update_data_invoice()">OK</a>
    <a class="btn btn-sm btn-warning" onclick="close_model();">Avbryt</a>
</div>
<div id="show_log_data" class="col-md-12 block-log-data" style="display: none">
    <table class="table table-bordered" width='100%'>
        <tr>
            <th>ID number</th>            
            <th>Status</th>
            <th>Beløp</th>
            <th>avd</th>
            <th>supplier</th>
        </tr>
    <?php if(count($list_invoices)> 0):
        foreach($list_invoices as $item):
    ?>
    <tr>
        <td><?php echo $item->upload_invoice_id;?></td>
        <td><?php echo $item->ispaid;?></td>
        <td><?php echo $item->amount;?></td>
        <td><?php echo $list_avd[$item->avd_id]["avd_name"];?></td>
        <td><?php echo (!empty($list_supplier[$item->supplierid]["supplier_name"]))?$list_supplier[$item->supplierid]["supplier_name"]:null;?></td>
    </tr>
    <?php
        endforeach;
    endif;
    ?>
    </table>
</div>
</div>

</div>
<script>
    function show_loglist_data(){
        var type = $("#status_show_log").val();
        //console.log(type);
        if(type == "0"){
            $("#show_log_data").show();
            $("#status_show_log").val("1");
        }else{
            $("#show_log_data").hide();
            $("#status_show_log").val("0");
        }
    }
</script>
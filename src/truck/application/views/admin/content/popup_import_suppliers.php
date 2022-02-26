<div class="modal-header">
        <button type="button" class="close" onclick="close_model();">&times;</button>
</div>
<div class="modal-body">
<label>Are you wnat to import suppliers from finas.k12.no?</label>
<label>If you press yes, there will <?php echo count($new_data_supplier_insert);?> new suppliers imported , If you like to see which suppliers. That will be done. Press LOG.</label>
<input type='hidden' id='status_show_log' value='0'>
<div class="row">
<div class="col-md-4"><a class="btn btn-sm btn-default" onclick='show_loglist_data()'>LOG</a></div>
<div class="col-md-7 text-right">
<input type="hidden" id="data_update_supplier" value='<?php echo json_encode($new_data_supplier_insert);?>' >
<input type="hidden" id="old_data_supplier" value='<?php echo json_encode($old_data_supplier);?>' >
<input type="hidden" id="data_update_supplier_project" value='<?php echo json_encode($data_supplier_project);?>' >
<input type="hidden" id="data_update_supplier_change" value='<?php echo json_encode($new_data_supplier_update);?>' >
    <a class="btn btn-sm btn-primary" onclick="update_data_suppliers()">OK</a>
    <a class="btn btn-sm btn-warning" onclick="close_model();">Avbryt</a>
</div>
<div id="show_log_data" class="col-md-12 block-log-data" style="display: none">
    New Supplier Data
    <table class="table table-bordered" width='100%'>
        <tr>
            <th>No.</th>            
            <th>Supplier name</th>
        </tr>
    <?php if(count($new_data_supplier_insert)> 0):
        $i=1;
        foreach($new_data_supplier_insert as $item):
    ?>
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $item->supplier_name;?></td>
    </tr>
    <?php
    $i++;
        endforeach;
    endif;
    ?>
    </table>
    Update Supplier Data
    <table class="table table-bordered" width='100%'>
        <tr>
            <th>No.</th>            
            <th>Supplier name</th>
        </tr>
    <?php if(count($new_data_supplier_update)> 0):
            $i=1;
        foreach($new_data_supplier_update as $item):
    ?>
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $item->supplier_name;?></td>
    </tr>
    <?php
    $i++;
        endforeach;
    endif;
    ?>
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
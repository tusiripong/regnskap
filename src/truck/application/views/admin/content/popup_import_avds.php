<div class="modal-header">
        <button type="button" class="close" onclick="close_model();">&times;</button>
</div>
<div class="modal-body">
<label>Are you wnat to import avds from finas.k12.no?</label>
<label>If you press yes, there will <?php echo count($new_data_avd_insert);?> new avds imported , If you like to see which avds. That will be done. Press LOG.</label>
<input type='hidden' id='status_show_log' value='0'>
<div class="row">
<div class="col-md-4"><a class="btn btn-sm btn-default" onclick='show_loglist_data()'>LOG</a></div>
<div class="col-md-7 text-right">
<input type="hidden" id="data_update_avd" value='<?php echo json_encode($new_data_avd_insert);?>' ?>
<input type="hidden" id="data_old_avd" value='<?php echo json_encode($old_data_avd);?>' ?>
<input type="hidden" id="data_update_avd_project" value='<?php echo json_encode($data_avd_project);?>' ?>
    <a class="btn btn-sm btn-primary" onclick="update_data_avds()">OK</a>
    <a class="btn btn-sm btn-warning" onclick="close_model();">Avbryt</a>
</div>
<div id="show_log_data" class="col-md-12 block-log-data" style="display: none">
    <table class="table table-bordered" width='100%'>
        <tr>
            <th>Avd id</th>            
            <th>Avd name</th>
        </tr>
    <?php if(count($new_data_avd_insert)> 0):
        foreach($new_data_avd_insert as $item):
    ?>
    <tr>
        <td><?php echo $item->avd_original_id;?></td>
        <td><?php echo $item->avd_name;?></td>
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
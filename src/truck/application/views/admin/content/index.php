
<div class="rows">
    <div class="col-md-12">
    <a class="btn btn-sm btn-primary" onclick="call_project_import('projects')">Import Projects <?php if($new_rows_project > 0){ echo '<small class="alert-number">'.$new_rows_project.'</small>'; }?></a>
    <a class="btn btn-sm btn-primary" onclick="call_project_import('suppliers')">Import Suppliers <?php if($new_rows_supplier > 0){ echo '<small class="alert-number">'.$new_rows_supplier.'</small>'; }?></a>
    <a class="btn btn-sm btn-primary" onclick="call_project_import('avds')">Import Departments <?php if($new_rows_avd > 0){ echo '<small class="alert-number">'.$new_rows_avd.'</small>'; }?></a>
    <a class="btn btn-sm btn-primary" onclick="call_project_import('bankaccount')">Import Bankaccount <?php if($new_rows_bankaccount > 0){ echo '<small class="alert-number">'.$new_rows_bankaccount.'</small>'; }?></a>
    <input type="hidden" id="check_import_data" value="<?php echo $new_rows_project+$new_rows_avd+$new_rows_supplier+$new_rows_bankaccount; ?>">
    </div>
    <div class="col-md-12 text-center">
    <input type="checkbox" id="p_invoice" <?php if(!empty($_GET['p_invoice']) && $_GET['p_invoice'] == 1){echo "checked='checked'";} ?>> Show only projects | 
    <input type="checkbox" id="show_with_out_unlock" <?php if(!empty($_GET['without_unlock']) && $_GET['without_unlock'] == 1){echo "checked='checked'";} ?>> Show projects with out unlock</div>
    
    <div id="index-form" class="col-md-12">
        <?php /* <div class="col-md-3 text-center font-bold">
            <div class="block-dashboard">
            <img src="/themes/admin/images/no_img_house-20150408123121.jpg" width="100%">
            <input type="radio" class="project_session" name="project_session" value="" <?php if(count($setting_user) == 0){ echo 'checked="checked"'; }?> onclick="set_project_session('')"> ALL Project
            </div>
        </div> */ ?>
        <?php /*
        foreach($listprojects as $item):
            if(!empty($_GET['without_unlock']) && $_GET['without_unlock'] == 1){
            if($list_data_unlock[$item->project_original_id]["transaction_unlock"] == 0 &&  $list_data_unlock[$item->project_original_id]["invoice_unlock"] == 0):
            $checked = ($setting_user[0]['value'] == $item->project_original_id)?"checked":"";        
        ?>
        <div id="block_project_<?php echo $item->project_original_id;?>" class="col-md-3 text-center font-bold ">
            <div class="block-dashboard">
                <p class="btn-hide-p" onclick="hide_project(<?php echo $item->project_original_id;?>)"><i class="icon-minus"></i></p>
            <img src="/themes/admin/images/no_img_house-20150408123121.jpg" width="100%">
            <?php 
            if(!empty($checked)){
            echo '<input type="radio" class="project_session" name="project_session" value="'.$item->project_original_id.'" onclick="set_project_session('.$item->project_original_id.')" checked="'.$checked.'"><span class="project_name" >'.$item->project_name.'</span>';
            }else{
            echo '<input type="radio" class="project_session" name="project_session" value="'.$item->project_original_id.'" onclick="set_project_session('.$item->project_original_id.')" ><span class="project_name" >'.$item->project_name.'</span>';
            }?>
            <br>
            <a href="/admin/content/bankaccount/detail?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["transaction_unlock"];?>) unlocked transactions <br></a>
            <a href="/admin/content/invoices?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["invoice_unlock"];?>) unlocked invoices</a>
            <br><a onclick="call_data_invoice(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new invoices</small></a>
            <br><a onclick="call_data_transaction(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new transactions</small></a>
            </div>
        </div>
            <?php endif;
            }else{
                 if($list_data_unlock[$item->project_original_id]["transaction_unlock"] > 0 || $list_data_unlock[$item->project_original_id]["invoice_unlock"] > 0):
                $checked = ($setting_user[0]['value'] == $item->project_original_id)?"checked":"";        
            ?>
            <div id="block_project_<?php echo $item->project_original_id;?>" class="col-md-3 text-center font-bold ">
                <div class="block-dashboard">
                <p class="btn-hide-p" onclick="hide_project(<?php echo $item->project_original_id;?>)"><i class="icon-minus"></i></p>
                <img src="/themes/admin/images/no_img_house-20150408123121.jpg" width="100%">
                <?php 
                if(!empty($checked)){
                echo '<input type="radio" class="project_session" name="project_session" value="'.$item->project_original_id.'" onclick="set_project_session('.$item->project_original_id.')" checked="'.$checked.'"><span class="project_name" >'.$item->project_name.'</span>';
                }else{
                echo '<input type="radio" class="project_session" name="project_session" value="'.$item->project_original_id.'" onclick="set_project_session('.$item->project_original_id.')" ><span class="project_name" >'.$item->project_name.'</span>';
                }?>
                <br>
                <a href="/admin/content/bankaccount/detail?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["transaction_unlock"];?>) unlocked transactions <br></a>
                <a href="/admin/content/invoices?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["invoice_unlock"];?>) unlocked invoices</a>
                <br><a onclick="call_data_invoice(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new invoices</small></a>
                <br><a onclick="call_data_transaction(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new transactions</small></a>
                </div>
            </div>
            <?php endif;
            }
        endforeach;
       */ ?>
    </div>
</div>
<div id="loading_invoice" class="loading_invoice" style="display: none">
    <img src="/themes/admin/images/loader1.gif" alt=""/>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" id="text-content">      
    </div>
  </div>
</div>
<script>

    load_data();
    function load_data(sorting){
        loading("show");
        var p_invoice = ($("#p_invoice").prop("checked"))?1:0;
        var without_unlock = ($("#show_with_out_unlock").prop("checked"))?1:0;
        $.get('/home/index_form',{p_invoice:p_invoice,without_unlock:without_unlock,sorting:sorting},function(data){
        //console.log(data);    
        $('#index-form').html(data);
        loading("hide");
        });
    }

    function loading(type){
        if(type == "show"){
            var height = $(document).height();
            $("#loading_invoice").css("height",height);
            $("#loading_invoice").show();
        }else{
            $("#loading_invoice").hide();
        }
    }

    $('#project_id').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'All selected!',
            onChange: function(option, checked) {
                    save_project();
            },
            onSelectAll: function() {
                    save_project();
            }
    });

    $("#show_with_out_unlock").change(function(){
        var p_invoice = ($("#p_invoice").prop("checked"))?1:0;
        var without_unlock = ($("#show_with_out_unlock").prop("checked"))?1:0;
        //console.log(without_unlock);
        load_data();
        //window.location.href = "?without_unlock="+without_unlock+"&p_invoice="+p_invoice;
    });

    $("#p_invoice").change(function(){
        var p_invoice = ($("#p_invoice").prop("checked"))?1:0;
        var without_unlock = ($("#show_with_out_unlock").prop("checked"))?1:0;
        //console.log(without_unlock);
        load_data();
        //window.location.href = "?without_unlock="+without_unlock+"&p_invoice="+p_invoice;
    });
            
    function save_project(){
        var project_id = $("#project_id").val();
    }
    
    function call_data_invoice(project_id){
        $("#loading_invoice").show();
        if($('#check_import_data').val() > 0){
            alert('The are items to import, you cannot make any update.');
            $("#loading_invoice").hide();
        }else{
            $.get('/home/call_data_invoice',{project_id:project_id},function (data){   
                if(data == 0){
                    if(confirm("Have a new items to import, Please click 'OK' to refresh page") == true){
                        location.reload();
                    }else{
                        $("#loading_invoice").hide();
                    }
                }else{                
                    $('#text-content').html(data);
                    $("#loading_invoice").hide();
                    $("#myModal").modal("toggle");
                    //$(".modal-dialog").css("width", "90%");
                }
            });
        }
    }

    function call_data_transaction(project_id){
        $("#loading_invoice").show();
        if($('#check_import_data').val() > 0){
            alert('The are items to import, you cannot make any update.');
            $("#loading_invoice").hide();
        }else{
            $.get('/home/call_data_transaction',{project_id:project_id},function (data){    
                if(data == 0){
                    if(confirm("Have a new items to import, Please click 'OK' to refresh page") == true){
                        location.reload();
                    }else{
                        $("#loading_invoice").hide();
                    }
                }else{               
                    $('#text-content').html(data);
                    $("#loading_invoice").hide();
                    $("#myModal").modal("toggle");
                    //$(".modal-dialog").css("width", "90%");
                }
            });
        }
    }
    
    function update_data_invoice(){
        $("#loading_invoice").show();
        var data_update = $("#data_update").val();
        $.post('/home/update_data_invoice_and_transactions',{data_update:data_update,type:"invoice"},function (data){                
                $("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
        });
    }

    function update_data_transactions(){
        $("#loading_invoice").show();
        var data_update = $("#data_update").val();
        var data_update_invoice = $("#data_update_invoice").val();
        $.post('/home/update_data_invoice_and_transactions',{data_update:data_update,data_update_invoice:data_update_invoice,type:"transaction"},function (data){                
                $("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
        });
    }
    
    function set_project_session(project_id){
         //console.log(project_id)
         $.get('/home/set_project_defualt',{project_id:project_id},function(data){
             
         });
    }

    function hide_project(project_id){
        $("#block_project_"+project_id).css("display","none");
        $.get('/home/hide_project',{project_id:project_id},function(data){
        });
    }

    function get_unlock_info_project(project_id){
        $("#loading_invoice").show();
        $.get('/home/popup_info_project',{project_id:project_id},function (data){
            $('#text-content').html(data);
            $("#loading_invoice").hide();
            $("#myModal").modal("toggle");
        });
    }

    function call_project_import(import_name){
        $("#loading_invoice").show();
            $.get('/home/import_data',{import_name:import_name},function (data){                   
                $('#text-content').html(data);
                $("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
            });
    }

    function update_data_project(){
        $("#loading_invoice").show();
        var data_update = $("#data_update").val();
        var data_old_data = $("#data_old_data").val();
        $.post('/home/save_data_projects',{data_update:data_update,data_old_data:data_old_data},function (data){                
                //$("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
                location.reload();
        });
    }

    function update_data_bankaccount(){
        $("#loading_invoice").show();
        var data_update = $("#data_update").val();
        var data_update_type = $("#data_update_type").val();
        var old_data_update = $("#old_data_update").val();
        $.post('/home/save_data_bankaccounts',{data_update:data_update,old_data_update:old_data_update,data_update_type:data_update_type},function (data){                
                //$("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
                location.reload();
        });
    }

    function update_data_suppliers(){
        $("#loading_invoice").show();
        var data_update_supplier = $("#data_update_supplier").val();
        var data_update_supplier_change = $("#data_update_supplier_change").val();
        var data_update_supplier_project = $("#data_update_supplier_project").val();
        var old_data_supplier = $("#old_data_supplier").val();
        $.post('/home/save_data_suppliers',{data_update_supplier_change:data_update_supplier_change,data_update_supplier:data_update_supplier,old_data_supplier:old_data_supplier,data_update_supplier_project:data_update_supplier_project},function (data){                
                //$("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
                location.reload();
        });
    }

    function update_data_avds(){
        $("#loading_invoice").show();
        var data_update_avd = $("#data_update_avd").val();
        var data_old_avd = $("#data_old_avd").val();
        var data_update_avd_project = $("#data_update_avd_project").val();
        $.post('/home/save_data_avds',{data_update_avd:data_update_avd,data_old_avd:data_old_avd,data_update_avd_project:data_update_avd_project},function (data){                
                //$("#loading_invoice").hide();
                $("#myModal").modal("toggle");
                //$(".modal-dialog").css("width", "90%");
                location.reload();
        });
    }
    
    function close_model(){              
    $("#myModal").modal("toggle");
    }
</script>

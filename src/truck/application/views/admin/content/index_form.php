<div class="col-md-12 text-center" id="sorting"></div>
<div class="col-md-12">
        <?php 
        foreach($listprojects as $item):
            if(!empty($_GET['without_unlock']) && $_GET['without_unlock'] == 1){
            if($list_data_unlock[$item->project_original_id]["transaction_unlock"] == 0 &&  $list_data_unlock[$item->project_original_id]["invoice_unlock"] == 0):
            $checked = (!empty($setting_user) && $setting_user[0]['value'] == $item->project_original_id)?"checked":"";        
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
            <br><a class="text-info" onclick="get_unlock_info_project(<?php echo $item->project_original_id;?>)">View Unlock</a>
            <?php /*<a href="/admin/content/bankaccount/detail?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["transaction_unlock"];?>) unlocked transactions <br></a>
            <a href="/admin/content/invoices?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["invoice_unlock"];?>) unlocked invoices</a> */ ?>
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
                <br><a class="text-info" onclick="get_unlock_info_project(<?php echo $item->project_original_id;?>)">View Unlock</a>
                <?php /*<a href="/admin/content/bankaccount/detail?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["transaction_unlock"];?>) unlocked transactions <br></a>
                <a href="/admin/content/invoices?project_id=<?php echo $item->project_original_id; ?>&show_lock=3">(<?php echo $list_data_unlock[$item->project_original_id]["invoice_unlock"];?>) unlocked invoices</a> */ ?>
                <br><a onclick="call_data_invoice(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new invoices</small></a>
                <br><a onclick="call_data_transaction(<?php echo $item->project_original_id;?>)"><small class="text-warning">update new transactions</small></a>
                </div>
            </div>
            <?php endif;
            }
        endforeach;
        ?>

</div>
<script>
    var letter = new Array();
    $('.project_name').each(function(){
        var sup_name = $(this).text();
        var letter_char = sup_name.charAt(0).toUpperCase();
        if(letter.indexOf(letter_char) < 0)
        {
            letter.push(letter_char);
        }
    });
    letter.sort(SortByName);
    var url = "?";
    var items = window.location.search.substr(1).split("&");
    var result = undefined, tmp = [],p_num =0;
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if(tmp[0] != "sorting"){
            if(p_num == 0){
                url += items[index];
            }else{
                url += "&"+items[index];
            }
        }
        p_num++;
    }
    // var letter_html = ' <a href="'+url+'&sorting=">ALL</a> ';
    // for(var i =0;i<letter.length;i++){
    //     letter_html += '|';
    //     letter_html += ' <a href="'+url+'&sorting='+letter[i]+'">'+letter[i]+'</a> ';
    // }
    var letter_html = ' <a onclick="load_data()">ALL</a> ';
    for(var i =0;i<letter.length;i++){
        letter_html += '|';
        letter_html += " <a onclick='load_data(\""+letter[i]+"\")'>"+letter[i]+"</a> ";
    }

    $('#sorting').html(letter_html);
    function SortByName(a, b){
      var aName = a.toLowerCase();
      var bName = b.toLowerCase(); 
      return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }
</script>
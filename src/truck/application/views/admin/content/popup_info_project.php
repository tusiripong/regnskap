<div class="modal-header">
        <button type="button" class="close" onclick="close_model();">&times;</button>
</div>
<div class="modal-body">
<h4>Project name: <?php echo $project_name; ?></h4>
<a class="font-bold" href="/admin/content/bankaccount/detail?project_id=<?php echo $project_id; ?>&show_lock=3">(<?php echo $transaction_unlock;?>) unlocked transactions <br></a>
<a class="font-bold" href="/admin/content/invoices?project_id=<?php echo $project_id; ?>&show_lock=3">(<?php echo $invoice_unlock;?>) unlocked invoices</a>
<br>
<br>
<button type="button" class="btn btn-warning"  onclick="close_model();">CLOSE</button>  
</div>
<?php

Assets::add_js(array('jquery.js','jquery.tablesorter.combined.js','jquery.tablesorter.js','jquery.dataTables.min.js','dataTables.buttons.min.js','pdfmake.min.js','buttons.html5.min.js','vfs_fonts.js','bootstrap.min.js', 'jwerty.js'), 'external', true);

echo theme_view('header');

?>
<div class="body">
	<div class="container-fluid">
	    <?php
            echo Template::message();
            echo isset($content) ? $content : Template::content();
        ?>
	</div>
</div>
<?php echo theme_view('footer'); ?>
<?php $this->view("header"); ?>

<h1><?php echo lang("database_create_title"); ?></h1>

<p><?php echo lang("database_create_message"); ?></p>

<table id="tests">
	<tr class="<?php echo $status; ?>">
		<th>
			<div><?php
            if($status == "passed")
                echo lang("database_create_success");
            else
                echo lang("database_create_failed").'<br />'.$message;
            ?></div>
		</th>
	</tr>
</table>

<?php $this->view("footer"); ?>
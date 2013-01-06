<?php $this->view("header"); ?>

<h1><?php echo lang("database_setup_title"); ?></h1>

<p><?php echo lang("database_setup_message"); ?></p>

<table id="tests">
<?php foreach($tables as $table=>$query_status): ?>
	<tr class="<?php echo $query_status; ?>">
		<th>	
            <?php echo $table ?>
        </th>
		<td>
		    <?php 
		        if($query_status=="passed") 
                    echo lang("database_table_created");
                else
                    echo lang("database_table_failed"); 
            ?>
		</td>
	</tr>
<?php endforeach; ?>
	<tr class="<?php echo $status; ?>">
		<td colspan="2">
			<div><?php
            if($status == "passed")
                echo lang("database_setup_success");
            else
                echo lang("database_setup_failed").'<br />'.$message;
            ?></div>
		</td>
	</tr>
</table>

<?php $this->view("footer"); ?>
<?php $this->view("header"); ?>

<h1><?php echo lang("database_title"); ?></h1>

<p><?php echo lang("database_message"); ?></p>

<table id="tests">
<?php foreach($credentials as $key=>$value): ?>
	<tr>
		<th>	
            <?php echo lang($key); ?>
        </th>
		<td>
		    <?php echo $value; ?>
		</td>
	</tr>
<?php endforeach; ?>
	<tr class="<?php echo $status; ?>">
		<th colspan="2">
			<div><?php
            if($status == "passed")
                echo lang("database_credentials_ok");
            elseif($status == "failed")
                echo lang("database_credentials_wrong").'<br />'.lang($message);
            elseif($message)
                echo lang($message);
            ?></div>
		</th>
	</tr>
</table>

<?php $this->view("footer"); ?>
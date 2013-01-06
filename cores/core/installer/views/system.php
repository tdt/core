<?php $this->view("header"); ?>

<h1><?php echo lang("system_title"); ?></h1>

<p><?php echo lang("system_message"); ?></p>

<table id="tests">
<?php foreach($tests as $test=>$result): ?>
	<tr class="<?php echo $result["status"]; ?>">
		<th>	
            <?php echo lang($test); ?>
        </th>
		<td>
		    <?php echo $result["value"]; ?>
		    <?php if(isset($result["message"]) && $result["message"]): ?>
		    	<div><?php echo lang($result["message"]); ?></div>
		    <?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php $this->view("footer"); ?>
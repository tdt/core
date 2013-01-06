<?php $this->view("header"); ?>

<h1><?php echo lang("config_check_title"); ?></h1>

<?php if($config_exists): ?>
<p><?php echo lang("config_check_message"); ?></p>

<table id="tests">
<?php foreach($tests as $test=>$result): ?>
	<?php if($result["status"] != "skipped"): ?>
	<tr class="<?php echo $result["status"]; ?>">
		<th>	
            <?php echo $test; ?>
        </th>
		<td>
		    <?php echo $result["value"]; ?>
		    <?php if(isset($result["message"]) && $result["message"]): ?>
		    	<div><?php echo lang($result["message"]); ?></div>
		    <?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
<?php endforeach; ?>
</table>

<?php else: ?>
<p><?php echo lang("no_config"); ?></p>
<?php endif; ?>

<?php $this->view("footer"); ?>
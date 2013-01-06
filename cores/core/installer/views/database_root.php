<?php $this->view("header"); ?>

<h1><?php echo lang("database_root_title"); ?></h1>

<p><?php echo lang("database_root_message"); ?></p>

<?php if($message): ?>
<p class="error"><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST">
    <table>
    	<tr>
    		<td>
    			Root username:
    		</td>
    		<td>
    			<input type="text" name="user" required autofocus />
    		</td>
    	</tr>
    	<tr>
    		<td>
    			Root password:
    		</td>
    		<td>
    			<input type="password" name="pass" />
    		</td>
    	</tr>
    	<tr>
    		<td></td>
    		<td><input type="submit" class="button" value="Create database" />
    		</td>
    	</tr>
    </table>
</form>

<?php $this->view("footer"); ?>
                <?php if($this->installer->previousStep()): ?>
					<a id="previous-button" href="?<?php echo $this->installer->previousStep(); ?>"><?php echo lang("previous_button"); ?></a> 
				<?php endif; ?>
				
				<?php if($this->installer->nextStep()): ?>
					<a id="next-button" href="?<?php echo $this->installer->nextStep(); ?>"><?php echo lang("next_button"); ?></a>
        		<?php endif; ?>
        	</div>
        </div>
    </body>
</html>
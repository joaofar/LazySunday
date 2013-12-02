<div class="players form">
<?php echo $this->Form->create('Player');?>
	<fieldset>
		<legend><?php echo __('Add Player'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('Rating.rating', array(
			'options' => array(
				'4' => 4, 
				'5' => 5, 
				'6' => 6),
			'selected' => '5'
			));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

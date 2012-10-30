<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'feed-form',
	'enableAjaxValidation' => false,
));?>
	<?php echo $form->textArea($model, 'description', array('placeholder'=>'What\'s up?', 'rows'=>1)); ?>
	<div class="form-rest" style="display:none;">
		<a class="btn btn-primary btn-create disabled">Submit</a>
		<a class="btn btn-cancel">Cancel</a>
	</div>
<?php $this->endWidget(); ?>

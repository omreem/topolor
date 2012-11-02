<?php $form = $this->beginWidget('GxActiveForm', array(
		'enableAjaxValidation' => false,
		'id' => 'message-form',
		'action' => $this->createUrl('create'),
));?>
	<div class="form-rest" style="display:none;">Send message to: <?php echo $form->dropDownList($model, 'to_user_id', GxHtml::listDataEx(User::model()->findAllAttributes(null, true, 'id<>'.Yii::app()->user->id))); ?></div>
	<?php echo $form->textArea($model, 'description', array('placeholder'=>'Send a message', 'rows'=>1)); ?>
	<div class="form-rest" style="display:none;">
		<a class="btn btn-primary btn-create disabled">Send</a>
		<a class="btn btn-cancel">Cancel</a>
	</div>
<?php $this->endWidget();?>
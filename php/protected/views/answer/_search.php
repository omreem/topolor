<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'id'); ?>
		<?php echo $form->textField($model, 'id', array('maxlength' => 10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'learner_id'); ?>
		<?php echo $form->dropDownList($model, 'learner_id', GxHtml::listDataEx(User::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'ask_id'); ?>
		<?php echo $form->dropDownList($model, 'ask_id', GxHtml::listDataEx(Ask::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'is_best'); ?>
		<?php echo $form->textField($model, 'is_best'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'useful'); ?>
		<?php echo $form->textField($model, 'useful', array('maxlength' => 10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'useless'); ?>
		<?php echo $form->textField($model, 'useless', array('maxlength' => 10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'create_at'); ?>
		<?php echo $form->textField($model, 'create_at'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'update_at'); ?>
		<?php echo $form->textField($model, 'update_at'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

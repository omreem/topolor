<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'question-option-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'question_id'); ?>
		<?php echo $form->dropDownList($model, 'question_id', GxHtml::listDataEx(Question::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'question_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'opt'); ?>
		<?php echo $form->textField($model, 'opt', array('maxlength' => 1)); ?>
		<?php echo $form->error($model,'opt'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'val'); ?>
		<?php echo $form->textArea($model, 'val'); ?>
		<?php echo $form->error($model,'val'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
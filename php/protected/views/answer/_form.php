<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'answer-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'learner_id'); ?>
		<?php echo $form->dropDownList($model, 'learner_id', GxHtml::listDataEx(User::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'learner_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'ask_id'); ?>
		<?php echo $form->dropDownList($model, 'ask_id', GxHtml::listDataEx(Ask::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'ask_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'is_best'); ?>
		<?php echo $form->textField($model, 'is_best'); ?>
		<?php echo $form->error($model,'is_best'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'useful'); ?>
		<?php echo $form->textField($model, 'useful', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'useful'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'useless'); ?>
		<?php echo $form->textField($model, 'useless', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'useless'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'create_at'); ?>
		<?php echo $form->textField($model, 'create_at'); ?>
		<?php echo $form->error($model,'create_at'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'update_at'); ?>
		<?php echo $form->textField($model, 'update_at'); ?>
		<?php echo $form->error($model,'update_at'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
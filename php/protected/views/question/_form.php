<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'question-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'concept_id'); ?>
		<?php echo $form->dropDownList($model, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'concept_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'correct_answer'); ?>
		<?php echo $form->textField($model, 'correct_answer', array('maxlength' => 1)); ?>
		<?php echo $form->error($model,'correct_answer'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('options')); ?></label>
		<?php echo $form->checkBoxList($model, 'options', GxHtml::encodeEx(GxHtml::listDataEx(QuestionOption::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('quizzes')); ?></label>
		<?php echo $form->checkBoxList($model, 'quizzes', GxHtml::encodeEx(GxHtml::listDataEx(Quiz::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
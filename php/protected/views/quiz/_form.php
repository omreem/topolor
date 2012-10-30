<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'quiz-form',
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
		<?php echo $form->labelEx($model,'concept_id'); ?>
		<?php echo $form->dropDownList($model, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'concept_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'score'); ?>
		<?php echo $form->textField($model, 'score', array('maxlength' => 5)); ?>
		<?php echo $form->error($model,'score'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'create_at'); ?>
		<?php echo $form->textField($model, 'create_at'); ?>
		<?php echo $form->error($model,'create_at'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'done_at'); ?>
		<?php echo $form->textField($model, 'done_at'); ?>
		<?php echo $form->error($model,'done_at'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'lastaccess_at'); ?>
		<?php echo $form->textField($model, 'lastaccess_at'); ?>
		<?php echo $form->error($model,'lastaccess_at'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('questions')); ?></label>
		<?php echo $form->checkBoxList($model, 'questions', GxHtml::encodeEx(GxHtml::listDataEx(Question::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
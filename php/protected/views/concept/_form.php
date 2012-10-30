<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'concept-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model, 'title', array('maxlength' => 256)); ?>
		<?php echo $form->error($model,'title'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'root'); ?>
		<?php echo $form->textField($model, 'root', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'root'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'lft'); ?>
		<?php echo $form->textField($model, 'lft', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'lft'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'rgt'); ?>
		<?php echo $form->textField($model, 'rgt', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'rgt'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->textField($model, 'level'); ?>
		<?php echo $form->error($model,'level'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('asks')); ?></label>
		<?php echo $form->checkBoxList($model, 'asks', GxHtml::encodeEx(GxHtml::listDataEx(Ask::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('comments')); ?></label>
		<?php echo $form->checkBoxList($model, 'comments', GxHtml::encodeEx(GxHtml::listDataEx(ConceptComment::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('resources')); ?></label>
		<?php echo $form->checkBoxList($model, 'resources', GxHtml::encodeEx(GxHtml::listDataEx(Resource::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('learners')); ?></label>
		<?php echo $form->checkBoxList($model, 'learners', GxHtml::encodeEx(GxHtml::listDataEx(User::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('notes')); ?></label>
		<?php echo $form->checkBoxList($model, 'notes', GxHtml::encodeEx(GxHtml::listDataEx(Note::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('questions')); ?></label>
		<?php echo $form->checkBoxList($model, 'questions', GxHtml::encodeEx(GxHtml::listDataEx(Question::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('quizzes')); ?></label>
		<?php echo $form->checkBoxList($model, 'quizzes', GxHtml::encodeEx(GxHtml::listDataEx(Quiz::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
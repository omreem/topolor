<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'favorite-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->dropDownList($model, 'user_id', GxHtml::listDataEx(User::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'user_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'of'); ?>
		<?php echo $form->textField($model, 'of', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'of'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'of_id'); ?>
		<?php echo $form->textField($model, 'of_id', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'of_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'create_at'); ?>
		<?php echo $form->textField($model, 'create_at'); ?>
		<?php echo $form->error($model,'create_at'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->
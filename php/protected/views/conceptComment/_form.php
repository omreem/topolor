<div class="post clearfix">
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/0.png','',
			array(
				'width'=>'66px',
				'height'=>'66px',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<?php $form = $this->beginWidget('GxActiveForm', array(
			'id' => 'concept-comment-form',
			'enableAjaxValidation' => false,
		));?>
		<div class="control-group">
		<?php echo $form->textArea($model, 'description', array('placeholder'=>'Description', 'rows'=>1)); ?>
		<?php echo $form->dropDownList($model, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true)), array('style'=>'display:none')); ?>
		</div>
		<a class="btn btn-primary btn-create disabled">Submit</a>
		<?php $this->endWidget();?>
	</div><!-- /.well -->
</div>
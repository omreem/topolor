<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'note-form',
	'enableAjaxValidation' => false,
));?>
	<?php echo $form->textArea($model, 'title', array('placeholder'=>'Write a note', 'rows'=>1)); ?>
	<div class="form-rest" style="display:none;">
		<?php echo $form->textArea($model, 'description', array('placeholder'=>'Description', 'rows'=>4)); ?>
		<?php echo $form->dropDownList($model, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true))); ?>
		<br>
		<?php $this->widget('CAutoComplete', array(
			'model'=>$model,
			'attribute'=>'tags',
			'url'=>array('note/suggestTags'),
			'multiple'=>true,
			'htmlOptions'=>array('size'=>50),
		)); ?>
		<span class="hint">Please separate different tags with commas and space.</span>
		<div>
			<a class="btn btn-primary btn-create disabled">Submit</a>
			<a class="btn btn-cancel">Cancel</a>
		</div>
	</div>
<?php $this->endWidget(); ?>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'todo-form',
	'enableAjaxValidation' => false,
));?>
	<?php echo $form->textArea($model, 'title', array('placeholder'=>'Create a todo', 'rows'=>1)); ?>
	<div class="form-rest" style="display:none;">
		<input class="start_at_time" name="start_at_time" id="start_at_time" type="text" style="width:75px;">
		<i class="icon-time" style="margin: -2px 0 0 -22.5px; pointer-events: none; position: relative;"></i>
		&nbsp;
		<input class="start_at_date" name="start_at_date" id="start_at_date" type="text" style="width:90px;" data-date-format="dd-mm-yyyy">
		<i class="icon-calendar" style="margin: -2px 0 0 -22.5px; pointer-events: none; position: relative;"></i>
		&nbsp;&nbsp;&nbsp;<i class="icon-minus" style="margin: -2px 0 0 0; pointer-events: none; position: relative;"></i>&nbsp;&nbsp;
		<input class="end_at_time" name="end_at_time" id="end_at_time" type="text" style="width:75px;"/>
		<i class="icon-time" style="margin: -2px 0 0 -22.5px; pointer-events: none; position: relative;"></i>
		&nbsp;
		<input class="end_at_date" name="end_at_date" id="end_at_date" type="text" style="width:90px;" data-date-format="dd-mm-yyyy">
		<i class="icon-calendar" style="margin: -2px 0 0 -22.5px; pointer-events: none; position: relative;"></i>
		<?php echo $form->textArea($model, 'description', array('placeholder'=>'Description', 'rows'=>4)); ?>
		<?php echo $form->dropDownList($model, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true))); ?>
		<br>
		<?php $this->widget('CAutoComplete', array(
			'model'=>$model,
			'attribute'=>'tags',
			'url'=>array('todo/suggestTags'),
			'multiple'=>true,
			'htmlOptions'=>array('size'=>50),
		)); ?>
		<span class="hint">Please separate different tags with commas.</span>
		<div>
			<a class="btn btn-primary btn-create disabled">Submit</a>
			<a class="btn btn-cancel">Cancel</a>
		</div>
	</div>
<?php $this->endWidget();?>

<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/ext/jdewit-bootstrap-timepicker/css/timepicker.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/ext/jdewit-bootstrap-datepicker/css/datepicker.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/ext/jdewit-bootstrap-timepicker/js/bootstrap-timepicker.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/ext/jdewit-bootstrap-datepicker/js/bootstrap-datepicker.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('todo-form-js', "
	$(document).ready(function() {
		$('.start_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true
		});
		
		$('.end_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true,
			defaultTime: '".date("h:i A", strtotime('+30 minutes'))."'
		});
		
		$('.start_at_date, .end_at_date').val('".date("d-m-Y")."');
		
		$('.start_at_date, .end_at_date').datepicker();
	});
");	
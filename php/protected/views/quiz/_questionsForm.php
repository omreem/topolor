<div>
	<?php echo CHtml::link ('Cancel', CController::createUrl('/concept/view/'.$concept_id), array('class'=>'btn')); ?>
</div>
<hr>
<form novalidate="novalidate" class="quiz-form" autocomplete="off" method="POST" action="<?php echo CController::createUrl('/quiz/quizSubmit');?>">
	<ol>
	<?php foreach ($questions as $question):?>
		<li>
			<div>
				<p><?php echo $question['description'];?></p>
				<?php foreach ($question['options'] as $option) :?>
				<p>
				<input type="radio" name='<?php echo "q".$question["id"];?>' value='<?php echo $option["opt"]?>'>
				<?php echo $option['val'];?>
				</p>
				<?php endforeach;?>
			</div>
				<span class="error-msg"></span>
			<hr>
		</li>
	<?php endforeach;?>
	</ol>
	<input type="hidden" name="quiz_id" value="<?php echo $quiz_id;?>">
	<button class="btn" type="submit" name="submit" style="margin-left: 10px;">Submit</button>
</form>

<?php 
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-1.8.2.min.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.validate.js', CClientScript::POS_HEAD);
$rules = 'rules: {';
$messages = 'message: {';
foreach ($questions as $question) {
	$rules = $rules."q".$question['id'].': "required",';
	$messages = $messages."q".$question['id'].': "Please choose an option",';
}
$rules = $rules.'}';
$messages = $messages.'}';	
Yii::app()->clientScript->registerScript('qz-form-js', "
$(document).ready(function() {
	var validator = $('.quiz-form').validate({
		$rules,$messages,
		// the errorPlacement has to take the table layout into account
		errorPlacement: function(error, element) {
				error.appendTo( element.parent().parent().next() );
		},
	});
});
		
", CClientScript::POS_END);
?>
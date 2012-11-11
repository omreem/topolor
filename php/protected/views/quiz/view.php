<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title">
	  		<a href="<?php echo Yii::app()->baseUrl.'/index.php/concept/'.$model->concept->module->id;?>" class="btn-link"><?php echo $model->concept->module->title;?></a>
  			 &raquo; <a href="<?php echo Yii::app()->baseUrl.'/index.php/concept/'.$model->concept->id;?>" class="btn-link"><?php echo $model->concept->title;?></a>
  			 &raquo; Quiz
  		</span>
  		<div id="learnt-info" class="pull-right">
  		<?php if ($learnt_at != null) {?>
  		<span class="date-time pull-right">Learnt at: <?php echo Helpers::datatime_feed($learnt_at);?></span>
  		<?php } else { ?>
  		<?php echo CHtml::ajaxButton ("I've learnt",
						CController::createUrl('/concept/hasLearnt'), 
						array('update' => '#learnt-info',
							'type' => 'POST',
							'data' => array(
								'concept_id' => $model->id,
							),
						),
						array('class' =>'btn pull-right',
							'id' => 'hl'.uniqid()
			));?>
  		<?php } ?>
  		</div>
  </div>
</div>
<?php if (isset($_SERVER['HTTP_REFERER'])) {
	$returnUrl = $_SERVER['HTTP_REFERER'];
?>
<div class="well top-panel-fix"><a href="<?php echo $returnUrl;?>" class="btn">Return back</a></div>
<?php } ?>
<div style="border: solid 1px #ddd; padding: 20px;">
	<?php
		if ($questions == null)
			echo 'no questions for this concept!';
			
		else {	
			if ($quizDoneAt != null) {
				// the learner has answered all the questions in the quiz
				$this->renderPartial('_questionOld', array(
						'questions' => $questions,
						'concept_id'=>$model->concept->id,
						'quizDoneAt' => $quizDoneAt,
				));
			} else {
				// new quiz
				$this->renderPartial('_questionsForm', array(
						'questions'=>$questions,
						'quiz_id'=>$model->id,
						'concept_id'=>$model->concept->id,
				));
			}
		}	
	?>
</div>


<div class="content-title">
	<?php echo $data['title']?>
</div>
<p>
	<span class="date-time">Done at <?php echo Helpers::datatime_feed($data['done_at']);?></span>
	<?php echo CHtml::link ('Review', '',
			array(
				'class' =>'btn pull-right',
				'submit' => CController::createUrl('/quiz/view'),
				'params' => array('concept_id'=>$data['concept_id'], 'quizType'=>$data['type']),
		)); ?>
</p>
<hr>
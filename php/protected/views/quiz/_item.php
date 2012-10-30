<div class="content-title">
	<?php echo $data['title']?>
</div>
<p>
	<span class="date-time">Done at <?php echo Helpers::datatime_feed($data['done_at']);?></span>
	<?php echo CHtml::link ('Review', '',
				array(
						'class' =>'btn btn-small pull-right',
						'id' => 'qi'.uniqid(),
						'submit' => CController::createUrl('/quiz/view'),
						'params' => array('concept_id'=>$data['concept_id']),
		)); ?>
</p>
<hr>
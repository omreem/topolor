<div class="well">
	<div><?php echo CHtml::link ('Review', '',
			array(
				'class' =>'btn pull-right',
				'submit' => CController::createUrl('/quiz/view'),
				'params' => array('concept_id'=>$data['concept_id']),
		)); ?>
	</div>
	<h4>Score: <span style="color: red;"><?php echo $data['score']; ?></span></h4>
	<p><span class="date-time">Done at: <?php echo Helpers::datatime_feed($data['done_at']); ?></span></p>
	<p><b>Concept:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$data['concept_id'];?>"><span class="label label-success"><?php echo GxHtml::encode($data['concept_title']);?></span></a></p>
	<p><b>Tag:</b> <?php echo $data['tags'];?></p>
</div>
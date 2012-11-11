<div class="well">
	<div><?php echo CHtml::link ('Review', '',
			array(
				'class' =>'btn pull-right',
				'submit' => CController::createUrl('/quiz/view'),
				'params' => array('concept_id'=>$data->concept_id),
		)); ?>
	</div>
	<h4>Score: <span style="color: red;"><?php echo GxHtml::encode($data->score); ?></span></h4>
	<p><span class="date-time">Done at: <?php echo GxHtml::encode($data->done_at); ?></span></p>
	<p><b>Concept:</b> <span class="label label-success"><?php echo GxHtml::encode(GxHtml::valueEx($data->concept)); ?></span></p>
	<p><b>Tag:</b> <?php echo $data->tagLabels;?></p>
</div>
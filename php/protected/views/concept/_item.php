<div class="content-title">
	<?php echo $data['title']?>
</div>
<p class="item-description">
	<span class="date-time">Learnt by <?php echo Helpers::datatime_feed($data['lastaction_at']);?></span>
	<?php echo CHtml::link('Review',array('concept/'.$data['id']), array('class'=>'pull-right btn btn-small')); ?>
</p>
<hr>
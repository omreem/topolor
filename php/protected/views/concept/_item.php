<span class="content-title"><b><?php echo $data['title']?></b></span>
<?php echo CHtml::link('Review',array('concept/'.$data['id']), array('class'=>'pull-right btn')); ?>
<div class="item-description">
	<?php echo CHtml::encode($data['description']);?>
	<span class="date-time">Learnt by <?php echo Helpers::datatime_feed($data['lastaction_at']);?></span>
</div>
<hr>
<div><span class="content-title"><b><?php echo $data['title']?></b></span>
<?php echo CHtml::link('Review',array('concept/'.$data['id']), array('class'=>'pull-right btn')); ?>
</div>
<div class="item-description"><?php echo $data['description'];?></div>
<div><b>Tag:</b> <?php echo $this->getTags($data['id']); ?></div>
<div class="date-time">Learnt by <?php echo Helpers::datatime_feed($data['lastaction_at']);?></div>
<hr>
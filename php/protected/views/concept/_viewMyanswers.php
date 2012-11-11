<div class="well">
	<div class="item-description">
		<p><b><?php echo CHtml::encode($data['description']);?></b></p>
		<p>
			<?php
			foreach ($data['option'] as $opt => $val)
				echo $opt.'. '.CHtml::encode($val).'<br>';
			?>
		</p>
		<p<?php echo $data['answer']==$data['correct_answer'] ? '' : ' style="color: red;"';?>>You answer is: <?php echo $data['answer'];?><br>Correct answer is : <?php echo $data['correct_answer'];?></p>
	</div>
	<div><p><span class="date-time">Done at: <?php echo Helpers::datatime_feed($data['done_at']);?></span></p></div>
	<div>
		<p><b>Concept:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$data['concept_id'];?>"><span class="label label-success"><?php echo CHtml::encode($data['concept_title']);?></span></a></p>
		<p><b>Tag:</b> <?php echo $this->getTags($data['concept_id']); ?></p>
	</div>
</div>
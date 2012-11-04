<div class="post clearfix">
	<?php if($data->learner_id == Yii::app()->user->id) {?><span class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</span><?php } ?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->learner_id.'.png','',
			array(
				'width'=>'66px',
				'height'=>'66px',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<span class="user-name"><?php echo $data->learner;?></span>: <?php echo GxHtml::encode($data->description); ?>
		<span class="date-time">( <?php echo Helpers::datatime_feed($data->create_at);?> )</span>
		<input id="data_id" type="hidden" value='<?php echo $data->id;?>'/>
		<br><br>
	</div><!-- /.well -->
</div>

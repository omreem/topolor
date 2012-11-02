<?php
	if ($data->user_id == Yii::app()->user->id) {
		$imgUrl = Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->to_user_id.'.png';
		$userName = GxHtml::encode(GxHtml::valueEx($data->toUser));
	} else {
		$imgUrl = Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->user_id.'.png';
		$userName = GxHtml::encode(GxHtml::valueEx($data->user));
	}
?>
<div class="post clearfix well" style="margin-bottom: 20px;">
	<div style="display: table; width: 100%;">
		<div class="user-avatar" style="display: table-cell; width: 66px;">
			<?php echo GxHtml::image(
				$imgUrl,'',
				array(
					'width'=>'44px',
					'height'=>'44px',
					'class'=>'img-rounded',
				));?>
		</div>
		<div style="display: table-cell; vertical-align: top;">
			<div>
				<?php if ($data->user_id == Yii::app()->user->id) echo 'Sent To '?>
				<span class="user-name"><?php echo $userName; ?>: </span>
				<span class="content-title"><?php echo GxHtml::encode($data->description); ?></span>
			</div>
			<div>
				<span class="date-time"><?php echo Helpers::datatime_trim($data->create_at);?></span>
				<a class="btn-link pull-right" href="message/<?php echo $data->id;?>" style="padding-right: 4px;"><?php echo $data->count;?> messages</a>
			</div>
		</div>
	</div>
	<input id="data_id" type="hidden" value='<?php echo $data->id;?>'/>
</div><!-- /.well -->


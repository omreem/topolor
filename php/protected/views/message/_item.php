<div class="post clearfix">
	<?php if ($data->to_user_id == Yii::app()->user->id):?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->user_id,'',
			array(
				'style'=>'width: 66px; height: 66px;',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well" style="margin-right: 112px;">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($data->user); ?>: </span>
			<span class="content-title"><?php echo GxHtml::encode($data->description);?></span>
		</div>
		<span class="date-time"><?php echo Helpers::datatime_trim($data->create_at);?></span>
		<a class="btn-link pull-right" href="#" style="padding-right: 4px;">reply</a>
	</div>
	<?php else :?>
	<div class="pull-right">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->user_id,'',
			array(
				'style'=>'width: 66px; height: 66px;',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle-r pull-right"></div>
	<div class="well post-content-r">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($data->user); ?>: </span>
			<span class="content-title"><?php echo GxHtml::encode($data->description);?></span>
		</div>
		<span class="date-time"><?php echo Helpers::datatime_trim($data->create_at);?></span>
		<span class="btn-link pull-right btn-reply" style="padding-right: 4px;">reply</span>
	</div>
	<?php endif;?>
</div>
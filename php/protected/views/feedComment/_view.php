<div class="comment-item clearfix">
	<?php if($data->user_id == Yii::app()->user->id) {?>
		<span class='btn btn-link pull-right btn-comment-delete' style="color: #ddd; margin: -10px -10px 0 0">x</span>
		<i class="icon-pencil transparent30 pull-right btn-comment-edit" style="margin-top: -2px"></i>
	<?php } ?>
	<div class="user-avatar">
		<?php echo GxHtml::image(Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->user_id,'', array('style'=>'width: 40px; height: 40px;','class'=>'img-polaroid'));?>
	</div>
	<div class="content" style="margin-left: 70px;">
		<div class="description" style="display: inline;">
			<p>
				<span class="user-name"><?php echo $data->user?>:</span>
				<span id="comment-description"><?php echo GxHtml::encode($data->description); ?></span>
			</p>
			<p class="date-time"><?php echo Helpers::datatime_feed($data->create_at);?></p>
		</div>
		<form id='comment-form' style="display: none;">
			<input type="hidden" name="id" id="id" value="<?php echo $data->id;?>">
			<textarea name="description" id="description"><?php echo GxHtml::encode($data->description); ?></textarea>
			<a class="btn btn-primary btn-small btn-comment-update disabled">Confirm</a>
			<a class="btn btn-small btn-comment-cancel">Cancel</a>
		</form>
	</div>
	<input type="hidden" id="data_id" value="<?php echo $data->id;?>">
</div>

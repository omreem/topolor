<div class="comment-item clearfix">
	<div class="user-avatar">
		<?php echo GxHtml::image(Yii::app()->baseUrl.'/uploads/images/profile-avatar/0.png','', array('width'=>'48px', 'height'=>'48px', 'class'=>'img-polaroid'));?>
	</div>
	<div class="content">
		<span class="user-name"><?php echo $data->learner?></span>:
		<?php if ($data->learner_id == Yii::app()->user->id) {?>
		<span class="pull-right owner" style="display:none;">
			<input type="hidden" id="answer_id" value="<?php echo $data->id;?>">
			<span class="btn-link btn-edit">edit</span>
			<span style="color:grey;">&nbsp;/&nbsp;</span>
			<span class="btn-link btn-delete">delete</span>
		</span>
		<?php }?>
		<br>
		<span class="description"><?php echo GxHtml::encode($data->description); ?></span>
	</div>
</div>

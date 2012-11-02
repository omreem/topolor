<div class="post clearfix">
	<?php if($data->learner_id == Yii::app()->user->id) {?><button class="close pull-right delete" style="margin: 10px;">&times;</button><?php }?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.Yii::app()->user->id.'.png','',
			array(
				'width'=>'66px',
				'height'=>'66px',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($data->learner);?>: </span>
			<span class="content-title"><?php echo GxHtml::encode($data->title); ?></span>
		</div>
		<div class="content-details clearfix">
			<div class='content-description'><?php echo GxHtml::encode($data->description); ?></div>
			<div style="margin: 10px 0 0 20px;"><?php if ($data->update_at != '') echo '<p><span class="date-time">Edited at '.Helpers::datatime_trim($data->update_at).'</span></p>';?></div>
			<?php if ($data->concept != null): ?>
			<div class="content-metadata">
				<b>Module:</b> <a href="<?php echo Yii::app()->homeUrl.'/module/'.$data->concept->module->id;?>"><?php echo GxHtml::encode($data->concept->module->title);?></a><br>
				<?php if ($data->concept_id != $data->concept->module->id) {?>
				<b>Concept:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$data->concept->id;?>"><?php echo GxHtml::encode($data->concept->title);?></a>
				<?php } ?>
			</div>
			<?php endif;?>
			<div class="content-tag">
				<?php if ($data->tags != ''): ?>
				<b>Tag:</b> <?php echo implode(' ', $data->tagLabels); ?> <a data-toggle="modal" href="#tag-canvas"><i class="icon-pencil transparent50 btn-tag-edit" style="display: none;"></i></a>
				<?php else: ?>
				<a data-toggle="modal" href="#tag-canvas" class="label label-info add-tag">+ tag</a>
				<?php endif; ?>
			</div>
		</div>
		<span class="date-time"><?php echo Helpers::datatime_trim($data->create_at);?></span>
		<span class="btn-link pull-right" style="padding-right: 4px;" onclick="$(this).closest('.post-content').find('.content-details').slideToggle();">Details &raquo;</span>
		<span class="social-bar pull-right" style="display: none;">
			<?php
				$favoriteCount = $data->favoriteCount;
				$shareCount = $data->shareCount;
				$isMyFavorite = $data->isMyFavorite();
			?>
			<a class="btn-link <?php echo $isMyFavorite ? 'btn-unfavorite' : 'btn-favorite';?>" rel="tooltip" data-placement="top" title="<?php echo $isMyFavorite == 1 ? "click to unfavorite it": 'click to favorite it'; ?>">Favorite<?php if ($favoriteCount > 0) echo 'd ('.$favoriteCount.')';?></a>&nbsp;&middot;
			<a class="btn-link btn-share" data-toggle="modal" href="#share-canvas">Share<?php if ($shareCount > 0) echo ' ('.$shareCount.')';?></a>&nbsp;&middot;&nbsp;
		</span>
		<input id="data_id" type="hidden" value='<?php echo $data->id;?>'/>
	</div><!-- /.well -->
</div>
<div class="post-comment"><?php $answerCount = $data->answerCount?>
	<div class="fake-input" style="border: solid 1px #ddd;">My answer...</div>
	<form class="answer-form" style="display:none;">
		<input type="hidden" name="Answer[ask_id]" id="Answer_ask_id" value="<?php echo $data->id;?>">
		<textarea name="Answer[description]" id="Answer_description"></textarea>
		<a class="btn btn-small btn-primary answer-form-create disabled">Submit</a>
		<a class="btn btn-small answer-form-cancel">Cancel</a>
	</form>
	<span id="comment-count" class="comment-count<?php if ($answerCount > 0){?> btn-link<?php }?>" <?php if ($answerCount > 0){?>onclick="$(this).next().slideToggle();"<?php }?>><?php echo $answerCount;?> answer<?php echo $answerCount>1?'s':'';?></span>
	<div class="comment-list">
		<?php $this->widget('zii.widgets.CListView', array(
			'id' => 'answer-list',
			'emptyText' => '',
			'dataProvider'=>new CArrayDataProvider($data->answersRecent, array(
				'keyField'=>'id',
			)),
			'itemView' => '/answer/_view',
			'summaryText' => '',
		));?>
		<?php if($answerCount > 5):?><br>
		<a href="<?php echo Yii::app()->homeUrl.'/ask/'.$data->id;?>">All answers &raquo;</a>
		<?php endif;?>
    </div>
</div>

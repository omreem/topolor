<div class="post clearfix">
	<?php if($data->user_id == Yii::app()->user->id) {?><span class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</span><?php } ?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$data->user_id,'',
			array(
				'style'=>'width: 66px; height: 66px;',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($data->user);?>: </span>
			<span class="content-description-feed<?php echo $data->user_id == Yii::app()->user->id ? ' edible' : ''?>" style="margin-bottom: 8px;"><?php echo GxHtml::encode($data->description); ?></span>
		</div>
		<?php if ($data->of == 'feed'):?>
		<?php
			$s = Feed::model()->findByPk($data->of_id);
		?>
		<div class="shared-content">
			<div>@<?php echo $s->user.': ';?>
				<span style="margin-bottom: 8px;"><?php echo GxHtml::encode($s->description); ?></span>
			</div>
			<div>
				<span class="date-time"><?php echo Helpers::datatime_trim($s->create_at);?></span>
				<?php
					$s_commentCount = $s->commentCount;
					$s_shareCount = $s->shareCount;
				?>
				<a class="btn-link pull-right" style="padding-right: 4px;" href="<?php echo 'index.php/feed/'.$s->id;?>">Comment<?php if ($s_commentCount > 0) echo ' ('.$s_commentCount.')';?></a>
				<span class="pull-right">&nbsp;&middot;&nbsp;</span>
				<a class="btn-link pull-right" href="<?php echo 'index.php/feed/'.$s->id;?>">Share<?php if ($s_shareCount > 0) echo ' ('.$s_shareCount.')';?></a>
			</div>
		</div>
		<?php elseif ($data->of == 'note'):?>
		<?php
			$s = Note::model()->findByPk($data->of_id);
		?>
		<div class="shared-content">
			<div><span class="badge badge-important">note</span> @<?php echo $s->learner.': ';?>
				<span style="margin-bottom: 8px;"><?php echo GxHtml::encode($s->title); ?></span>
			</div>
			<div class="content-details clearfix">
				<div class='content-description'><?php echo GxHtml::encode($s->description); ?></div>
				<div style="margin: 10px 0 0 20px;"><?php if ($s->update_at != '') echo '<p><span class="date-time">Edited at '.Helpers::datatime_trim($s->update_at).'</span></p>';?></div>
				<?php if ($s->concept != null): ?>
				<div class="content-metadata">
					<b>Module:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$s->concept->module->id;?>"><?php echo GxHtml::encode($s->concept->module->title);?></a><br>
					<?php if ($s->concept_id != $s->concept->module->id) {?>
					<b>Concept:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$s->concept->id;?>"><?php echo GxHtml::encode($s->concept->title);?></a>
					<?php } ?>
				</div>
				<?php endif;?>
				<div class="content-tag">
					<?php if ($s->tags != ''): ?>
					<b>Tag:</b> <?php echo implode(' ', $s->tagLabels); ?> <a data-toggle="modal" href="#tag-canvas"><i class="icon-pencil transparent50 btn-tag-edit" style="display: none;"></i></a>
					<?php else: ?>
					<a data-toggle="modal" href="#tag-canvas" class="label label-info add-tag">+ tag</a>
					<?php endif; ?>
				</div>
			</div>
			<div>
				<span class="date-time"><?php echo Helpers::datatime_trim($s->create_at);?></span>
				<?php
					$s_shareCount = $s->shareCount;
				?>
				<span class="btn-link pull-right" style="padding-right: 4px;" onclick="$(this).closest('.shared-content').find('.content-details').slideToggle();">Details &raquo;</span>
				<span class="pull-right">&nbsp;&middot;&nbsp;</span>
				<span class="pull-right" style="color: #666;">Share<?php if ($s_shareCount > 0) echo ' ('.$s_shareCount.')';?></span>
			</div>
		</div>
		<?php elseif ($data->of == 'ask'):?>
		<?php
			$s = Ask::model()->findByPk($data->of_id);
		?>
		<div class="shared-content">
			<div><span class="badge badge-warning">question</span> @<?php echo $s->learner.': ';?>
				<span style="margin-bottom: 8px;"><?php echo GxHtml::encode($s->title); ?></span>
			</div>
			<div>
				<span class="date-time"><?php echo Helpers::datatime_trim($s->create_at);?></span>
				<?php
					$s_shareCount = $s->shareCount;
				?>
				<a class="btn-link pull-right" style="padding-right: 4px;" href="<?php echo 'index.php/ask/'.$s->id;?>">Details &raquo;</a>
				<span class="pull-right">&nbsp;&middot;&nbsp;</span>
				<a class="btn-link pull-right" href="<?php echo 'index.php/ask/'.$s->id;?>">Share<?php if ($s_shareCount > 0) echo ' ('.$s_shareCount.')';?></a>
			</div>
		</div>
		<?php endif;?>
		<span class="date-time"><?php echo Helpers::datatime_trim($data->create_at);?></span><?php $commentCount = $data->commentCount?>
		<span class="btn-link pull-right" id="sum-comments" style="padding-right: 4px;" onclick="$(this).closest('.post-content').next().slideToggle();">Comment<?php if ($commentCount > 0) echo ' ('.$commentCount.')';?></span>
		<span class="social-bar pull-right" style="display: none;">
			<?php
				$favoriteCount = $data->favoriteCount;
				$shareCount = $data->shareCount;
				$isMyFavorite = $data->isMyFavorite();
			?>
			<a class="btn-link <?php echo $isMyFavorite ? 'btn-unfavorite' : 'btn-favorite';?>" rel="tooltip" data-placement="top" title="<?php echo $isMyFavorite == 1 ? "click to unfavorite it": 'click to favorite it'; ?>">Favorite<?php if ($favoriteCount > 0) echo 'd ('.$favoriteCount.')';?></a>&nbsp;&middot;
			<a class="btn-link btn-share" data-toggle="modal" href="#share-canvas">Share<?php if ($shareCount > 0) echo ' ('.$shareCount.')';?></a>&nbsp;&middot;&nbsp;
		</span><?php echo $isMyFavorite;?>
		<input id="data_id" type="hidden" value='<?php echo $data->id;?>'/>
		<input id="of" type="hidden" value='<?php echo $data->of;?>'/>
		<input id="of_id" type="hidden" value='<?php echo $data->of_id;?>'/>
	</div><!-- /.well -->
	<div class="post-comment" style="display: none;">
		<div class="fake-input" style="border: solid 1px #ddd;">Write a comment...</div>
		<form class="comment-form" style="display:none;">
			<input type="hidden" name="FeedComment[feed_id]" id="FeedComment_feed_id" value="<?php echo $data->id;?>">
			<textarea name="FeedComment[description]" id="FeedComment_description"></textarea>
			<a class="btn btn-small btn-primary comment-form-create disabled">Submit</a>
			<a class="btn btn-small comment-form-cancel">Cancel</a>
		</form>
		<?php $this->widget('zii.widgets.CListView', array(
			'id' => 'comment-list',
			'dataProvider'=>new CArrayDataProvider($data->comments, array(
				'keyField'=>'id',
			)),
			'itemView' => '/feedComment/_view',
			'summaryText' => '',
			'emptyText' => '',
		));?>
	</div>
</div>


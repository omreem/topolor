<div class="post clearfix">
	<?php if($model->learner_id == Yii::app()->user->id) {?><span class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</span><?php } ?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/0.png','',
			array(
				'width'=>'66px',
				'height'=>'66px',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($model->learner);?>: </span>
			<span class="content-title"><?php echo GxHtml::encode($model->title); ?></span>
		</div>
		<div class="content-details clearfix" style="display: block;">
			<div class='content-description'><?php echo GxHtml::encode($model->description); ?></div>
			<div style="margin: 10px 0 0 20px;"><?php if ($model->update_at != '') echo '<p><span class="date-time">Edited at '.Helpers::datatime_trim($model->update_at).'</span></p>';?></div>
			<?php if ($model->concept != null): ?>
			<div class="content-metadata">
				<b>Module:</b> <a href="<?php echo Yii::app()->homeUrl.'/module/'.$model->concept->module->id;?>"><?php echo GxHtml::encode($model->concept->module->title);?></a><br>
				<?php if ($model->concept_id != $model->concept->module->id) {?>
				<b>Concept:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$model->concept->id;?>"><?php echo GxHtml::encode($model->concept->title);?></a>
				<?php } ?>
			</div>
			<?php endif;?>
			<div class="content-tag">
				<?php if ($model->tags != ''): ?>
				<b>Tag:</b> <?php echo implode(' ', $model->tagLabels); ?> <a data-toggle="modal" href="#tag-canvas"><i class="icon-pencil transparent50 btn-tag-edit" style="display: none;"></i></a>
				<?php else: ?>
				<a data-toggle="modal" href="#tag-canvas" class="label label-info add-tag">+ tag</a>
				<?php endif; ?>
			</div>
		</div>
		<span class="date-time"><?php echo Helpers::datatime_trim($model->create_at);?></span>
		<span class="social-bar pull-right">
			<?php
				$favoriteCount = $model->favoriteCount;
				$shareCount = $model->shareCount;
				$isMyFavorite = $model->isMyFavorite();
			?>
			<a class="btn-link <?php echo $isMyFavorite ? 'btn-unfavorite' : 'btn-favorite';?>" rel="tooltip" data-placement="top" title="<?php echo $isMyFavorite == 1 ? "click to unfavorite it": 'click to favorite it'; ?>">Favorite<?php if ($favoriteCount > 0) echo 'd ('.$favoriteCount.')';?></a>&nbsp;&middot;
			<a class="btn-link btn-share" data-toggle="modal" href="#share-canvas">Share<?php if ($shareCount > 0) echo ' ('.$shareCount.')';?></a>
		</span>
		<input id="data_id" type="hidden" value='<?php echo $model->id;?>'/>
	</div><!-- /.well -->
</div>


<div class="post clearfix">
	<?php if($data->learner_id == Yii::app()->user->id) {?><span class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</span><?php } ?>
	<div style="width: 66px; height: 48px;">
		<div class="btn-group">
	    	<?php if ($data->status == Todo::STATUS_CANCELED) :?>
			<button class="btn dropdown-toggle" style="width: 100px;" data-toggle="dropdown">Canceled <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a class="btn-link item-reactivate-btn">Re-activate</a></li>
			</ul>
			<?php elseif ($data->status == Todo::STATUS_DONE) :?>
			<button class="btn dropdown-toggle" style="width: 100px;" data-toggle="dropdown">Done <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a class="btn-link item-redo-btn">Re-do</a></li>
			</ul>
			<?php else:?>
			<button class="btn dropdown-toggle" style="width: 100px;" data-toggle="dropdown">On Going <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a class="btn-link item-done-btn">Done</a></li>
				<li><a class="btn-link item-cancel-btn">Cancel</a></li>
			</ul>
			<?php endif;?>
		</div>
	</div>
	<div class="post-content well">
		<?php if ($data->start_at != null) :?>
		<div class="date-time"><?php echo Helpers::datatime_trim($data->start_at);?> - <?php echo Helpers::datatime_trim($data->end_at);?></div>
		<?php endif;?>
		<div class="content-title edible"><?php echo GxHtml::encode($data->title); ?></div>
		<div class="content-details clearfix">
			<div class='content-description edible'><?php echo GxHtml::encode($data->description); ?></div>
			<div style="margin: 10px 0 0 20px;"><?php if ($data->update_at != '') echo '<p><span class="date-time">Edited at '.Helpers::datatime_trim($data->update_at).'</span></p>';?></div>
			<?php if ($data->concept != null && $data->concept->id != 0): ?>
			<div class="content-metadata">
				<b>Module:</b> <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$data->concept->module->id;?>"><?php echo GxHtml::encode($data->concept->module->title);?></a><br>
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
		<span class="btn-link pull-right" style="padding-right: -6px;" onclick="$(this).parent().children('.content-details').slideToggle();">Details &raquo;</span>
		<span class="social-bar pull-right" style="display: none;">
			<a class="btn-link">Favorite</a>&nbsp;&middot;
			<a class="btn-link">Share</a>&nbsp;&middot;&nbsp;
		</span>
		<input id="data_id" type="hidden" value='<?php echo $data->id;?>'/>
	</div>
</div>
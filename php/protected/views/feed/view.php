<div class="post clearfix top-panel-fix">
	<?php if($model->user_id == Yii::app()->user->id) {?><span class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</span><?php } ?>
	<div class="user-avatar">
		<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.$model->user_id,'',
			array(
				'style'=>'width: 66px; height: 66px;',
				'class'=>'img-polaroid',
			));?>
	</div>
	<div class="post-triangle"></div>
	<div class="post-content well">
		<div style="margin-bottom: 8px;">
			<span class="user-name"><?php echo GxHtml::encode($model->user);?>: </span>
			<span class="content-description-feed" style="margin-bottom: 8px;"><?php echo GxHtml::encode($model->description); ?></span>
		</div>
		<span class="date-time"><?php echo Helpers::datatime_trim($model->create_at);?></span><?php $commentCount = $model->commentCount?>
		<span class="btn-link pull-right btn-comment" id="sum-comments" style="padding-right: 4px;">Comment<?php if ($commentCount > 0) echo ' ('.$commentCount.')';?></span>
		<span class="social-bar pull-right">
			<?php
				$favoriteCount = $model->favoriteCount;
				$shareCount = $model->shareCount;
				$isMyFavorite = $model->isMyFavorite();
			?>
			<a class="btn-link <?php echo $isMyFavorite ? 'btn-unfavorite' : 'btn-favorite';?>" rel="tooltip" data-placement="top" title="<?php echo $isMyFavorite == 1 ? "click to unfavorite it": 'click to favorite it'; ?>">Favorite<?php if ($favoriteCount > 0) echo 'd ('.$favoriteCount.')';?></a>&nbsp;&middot;
			<a class="btn-link btn-share" data-toggle="modal" href="#share-canvas">Share<?php if ($shareCount > 0) echo ' ('.$shareCount.')';?></a>&nbsp;&middot;&nbsp;
		</span>
		<input id="data_id" type="hidden" value='<?php echo $model->id;?>'/>
	</div><!-- /.well -->
	<div class="post-comment">
		<div class="fake-input" style="border: solid 1px #ddd;">Write a comment...</div>
		<form class="comment-form" style="display:none;">
			<input type="hidden" name="FeedComment[feed_id]" id="FeedComment_feed_id" value="<?php echo $model->id;?>">
			<textarea name="FeedComment[description]" id="FeedComment_description"></textarea>
			<a class="btn btn-small btn-primary comment-form-create disabled">Submit</a>
			<a class="btn btn-small comment-form-cancel">Cancel</a>
		</form>
		<?php $this->widget('zii.widgets.CListView', array(
			'id' => 'comment-list',
			'dataProvider'=>new CArrayDataProvider($model->comments, array(
				'keyField'=>'id',
			)),
			'itemView' => '/feedComment/_view',
			'summaryText' => '',
			'emptyText' => '',
		));?>
	</div>
</div>

<div id="share-canvas" class="modal hide fade in" style="display: none;">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<b>Share</b>
	</div>
	<div class="modal-body">
		<textarea id="description" rows="2" placeholder="Say something..."></textarea>
		<br>
		<div class="shared-content"></div>
	</div>
	<div class="alert alert-success" style="display: none;">Successfully shared!</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-primary btn-small btn-share-create">Share</a>
		<a href="#" class="btn btn-small btn-share-cancel" data-dismiss="modal">Cancel</a>
	</div>
</div>

<?php
	$model = new Feed;
	$form = $this->beginWidget('GxActiveForm', array(
	'id' => 'share-form',
	'method' => 'post',
));?>
	<?php echo $form->textArea($model, 'description'); ?>
	<?php echo $form->textField($model, 'of')?>
	<?php echo $form->textField($model, 'of_id')?>
	<?php echo $form->textField($model, 'from_id')?>
<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScript('site-index-js', "
	$(document).ready(function() {
		$('#share-form').hide();
		$('[rel=tooltip]').tooltip();
	});

//********* add comment to feed
	$('.btn-comment').click(function(){
		$('.post-comment .fake-input').click();
		
	});
		
	$('.post-comment .fake-input').click(function(){
		var fakeInput = $(this);
		fakeInput.hide();
		fakeInput.next().show();
		var input = $(this).next().children('#FeedComment_description');
		input.focus();
		input.focusout(function(){
			if(input.val() == '') {
				input.parent().hide();
				input.parent().prev().show();
			}
		});
	
		input.keyup(function(){
			if(input.val() != '')
				input.next().removeClass('disabled');
			else
				input.next().addClass('disabled');
		});
	});
	
	$('.post-comment .comment-form-cancel').live('click', function(){
		$(this).prev().prev().val('');
		$(this).parent().hide();
		$(this).parent().prev().show();
		$(this).prev().addClass('disabled');
	})
	
	$('.post-comment .comment-form-create').live('click', function(){
		if($(this).hasClass('disabled'))
			return false;
		\$form = $(this).parent();
		var newCommentDescription = \$form.children('#FeedComment_description').val();
		
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/feedComment/create')."',
			data: \$form.serialize(),
			success: function (newId) {
				\$form.children('#FeedComment_description').val('');
				\$form.children('.comment-form-create').addClass('disabled');
				\$form.hide();
				\$form.prev().show();
				var now;
				var d = new Date();
				var h = d.getHours();
				var m = d.getMinutes();
				if (h < 13)
					now = h;
				else
					now = h-12;
				if (m < 10)
					now += ':0' + m;
				else
					now += ':' +m;
				if (h < 12)
					now += 'AM';
				else
					now += 'PM';
				if (h < 10 || (h > 12 && h < 22))
					now = '0'+ now;
				var str =
					'<div class=\"comment-item clearfix\" style=\"display: none;\">'
						+ '<span class=\"btn btn-link pull-right btn-comment-delete\" style=\"color: #ddd; margin: -10px -10px 0 0\">x</span>'
						+ '<i class=\"icon-pencil transparent30 pull-right btn-comment-edit\" style=\"margin-top: -2px\"></i>'
						+ '<div class=\"user-avatar\">'
							+ '<img style=\"width: 40px; height: 40px;\" class=\"img-polaroid\" src=\"/topolor/uploads/images/profile-avatar/".Yii::app()->user->id."\"/>'
						+ '</div>'
						+ '<div class=\"content\" style=\"margin-left: 70px;\">'
							+ '<div class=\"description\" style=\"display: inline;\">'
								+ '<p>'
									+ '<span class=\"user-name\">".Yii::app()->user->name.":</span>'
									+ '<span id=\"comment-description\">'+newCommentDescription+'</span>'
								+ '</p>'
								+ '<p class=\"date-time\">'+now+'</p>'
							+ '</div>'
							+ '<form id=\"comment-form\" style=\"display: none;\">'
								+ '<input type=\"hidden\" name=\"id\" id=\"id\" value=\"'+newId+'\">'
								+ '<textarea name=\"description\" id=\"description\">'+newCommentDescription+'</textarea>'
								+ '<a class=\"btn btn-primary btn-small btn-comment-update disabled\">Confirm</a>'
								+ '<a class=\"btn btn-small btn-comment-cancel\">Cancel</a>'
							+ '</form>'
						+ '</div>'
						+ '<input type=\"hidden\" id=\"data_id\" value=\"'+newId+'\">'
					+ '<div>';

				\$form.closest('.post').find('#comment-list').children('.items').prepend(str);
				\$form.closest('.post-comment').find('#comment-list div:first-child').slideDown();
				\$commentCount=\$form.closest('.post').find('#sum-comments');
				var len = \$commentCount.text().length;
				if (len > 7) {
					\$newSum = parseInt(\$commentCount.text().substr(9, len - 10)) + 1;
					\$commentCount.text('Comment ('+\$newSum+')');
				} else {
					\$commentCount.text('Comment (1)');
				}
			}
		});
		
	});
		
	// update comment
		
	$('.comment-item .btn-comment-edit, .comment-item .btn-comment-delete').live('mouseenter', function() {
		$(this).css('cursor','pointer');
	});
		
	$('.comment-item .btn-comment-edit .comment-item .btn-comment-delete').live('mouseleave', function() {
		$(this).removeClass('cursor');
	});	
	
	var oriCommentDescription = '';
	$('.comment-item .btn-comment-edit').live('click', function() {
		$(this).closest('.comment-item').find('.description').hide();
		$(this).closest('.comment-item').find('#comment-form').show();
		oriCommentDescription = $(this).closest('.comment-item').find('#comment-description').html();
	});
		
	$('#comment-form #description').live('keyup', function(){
		if ($(this).val() != '' && $(this).val() != oriCommentDescription)
			$(this).next().removeClass('disabled');
		else
			$(this).next().addClass('disabled');
	});
		
	$('.comment-item .btn-comment-update').live('click', function() {
		if ($(this).hasClass('disabled'))
			return;
		
		\$this=$(this);
		\$form = \$this.parent();
		var newCommentDescription = \$this.prev().val();

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/feedComment/updateAjax')."',
			data: \$form.serialize(),
			success: function (html) {
				\$this.closest('.comment-item').find('#comment-description').text(newCommentDescription);
				\$this.closest('.comment-item').find('.description').show();
				\$this.closest('.comment-item').find('#comment-form').hide();
				\$this.addClass('disabled')
			}
		});
		return false;
	});
		
	$('.comment-item .btn-comment-cancel').live('click', function() {
		$(this).prev().addClass('disabled');
		$(this).prev().prev().val($(this).closest('.comment-item').find('#comment-description').text());
		oriCommentDescription = '';
		$(this).closest('.comment-item').find('.description').show();
		$(this).closest('.comment-item').find('#comment-form').hide();
	});

	$('.comment-item .btn-comment-delete').live('click', function (){
		\$this=$(this);
		var id = $(this).closest('.comment-item').find('#data_id').val();
		bootbox.confirm('Are you sure?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('feedComment/deleteAjax')."',
					data: {id: id},
					success: function(data) {
						\$this.closest('.comment-item').slideUp();
						\$commentCount=\$this.closest('.post').find('#sum-comments');
						\$newSum = parseInt(\$commentCount.text().substr(9, \$commentCount.text().length - 10)) - 1;
						if (\$newSum > 0)
							\$commentCount.text('Comment ('+\$newSum+')');
						else
							\$commentCount.text('Comment');
					}
				});
		    }
		});
	});
	
//********* favorite
		
	$('.btn-favorite').live('click', function(){
		var id = $(this).closest('.post').find('#data_id').val();
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('favorite/favorite')."',
			data: {id: id, of: 'feed'},
			success: function() {
				var len = \$this.text().length;
				if (len > 9) {
					\$newSum = parseInt(\$this.text().substr(11, len - 12)) + 1;
					\$this.text('Favorited ('+\$newSum+')');
				} else
					\$this.text('Favorited (1)');
				\$this.removeClass('btn-favorite');
				\$this.addClass('btn-unfavorite');
				\$this.attr('title', 'click to unfavorite it');
		$('[rel=tooltip]').tooltip();						////////??????????????
			}
		});
	});
		
	$('.btn-unfavorite').live('click', function(){
		var id = $(this).closest('.post').find('#data_id').val();
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('favorite/unfavorite')."',
			data: {id: id, of: 'feed'},
			success: function(favorite) {
				var len = \$this.text().length;
				\$newSum = parseInt(\$this.text().substr(11, len - 12)) - 1;
				if (\$newSum > 0)
					\$this.text('Favorited ('+\$newSum+')');
				else
					\$this.text('Favorite');
				\$this.removeClass('btn-unfavorite');
				\$this.addClass('btn-favorite');
				\$this.attr('title', 'click to favorite it');
		$('[rel=tooltip]').tooltip();;						////////??????????????
			}
		});
	});

//********* share
		
	$(document).ready(function() {
		$('#share-form').hide();
	});
		
	$('.btn-share').live('click', function() {
		var id = $(this).closest('.post').find('#data_id').val();
		var str = '@' + $(this).closest('.post').find('.user-name').html() + $(this).closest('.post-content').find('.content-title').text();
		$('#share-canvas').find('.shared-content').text(str);
		$('#share-form #Feed_of').val('feed');
		$('#share-form #Feed_of_id').val(id);
		$('#share-form #Feed_from_id').val(id);
		$('#share-canvas').find('#description').focus();    ///??????????
	});
		
	$('.btn-share-create').live('click', function(){
		$('#share-form #Feed_description').val($('#share-canvas').find('#description').val());
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('feed/create')."',
			data: $('#share-form').serialize(),
			success: function (html) {
				var len = $('.btn-share').text().length;
				if (len > 5) {
					\$newSum = parseInt($('.btn-share').text().substr(7, len - 8)) + 1;
					$('.btn-share').text('Share ('+\$newSum+')');
				} else
					$('.btn-share').text('Share (1)');
				$('#share-form').find('textarea').val('');
				$('#share-form').find('input').val('');
			}
		});
		setTimeout(function() {
			$('.modal.in').modal('hide');
		}, 400);
		return false;
	});
	
	$('.btn-share-cancel').live('click', function(){
		$('#share-canvas').find('#description').val('');
		$('#share-form #Feed_of').val('');
		$('#share-form #Feed_of_id').val('');
		$('#share-form #Feed_from_id').val('');
	});
		
");
<?php $this->pageTitle=Yii::app()->name;?>
<div class="well top-panel-fix">
	<ul class="nav nav-pills top-panel-fix" id="create-pills" style="margin-top: -6px;">
		<li class="active btn-feed"><a class="btn-link">Status</a></li>
		<li class="btn-message"><a class="btn-link">Message</a></li>
		<li class="btn-ask"><a class="btn-link">Q&amp;A</a></li>
		<li class="btn-note"><a class="btn-link">Note</a></li>
		<li class="btn-todo"><a class="btn-link">Todo</a></li>
	</ul>
	<div id="create-panel-feed" style="margin: -10px 0 -20px 0;">
		<?php $this->renderPartial('/feed/_form', array('model' => $newFeed));?>
	</div>
	<div id="create-panel-message" style="margin: -10px 0 -20px 0; display: none;">
		<?php $this->renderPartial('/message/_form', array('model' => $newMessage));?>
	</div>
	<div id="create-panel-ask" style="margin: -10px 0 -20px 0; display: none;">
		<?php $this->renderPartial('/ask/_form', array('model' => $newAsk));?>
	</div>
	<div id="create-panel-note" style="margin: -10px 0 -20px 0; display: none;">
		<?php $this->renderPartial('/note/_form', array('model' => $newNote));?>
	</div>
	<div id="create-panel-todo" style="margin: -10px 0 -20px 0; display: none;">
		<?php $this->renderPartial('/todo/_form', array('model' => $newTodo));?>
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

<?php if (Yii::app()->user->isGuest):
$model=new UserLogin;?>
<div id="login-modal" class="modal hide">
	<div class="modal-header"><b>Login</b></div>
	<div class="modal-body">
		<?php echo CHtml::beginForm(Yii::app()->homeUrl.'/user/login', 'POST', array('class'=>'form-horizontal')); ?>
			<div class="control-group">
				<label class="control-label" for="inputEmail">
					<?php echo CHtml::activeLabelEx($model,'username'); ?>
				</label>
				<div class="controls">
	    			<?php echo CHtml::activeTextField($model,'username', array('id'=>'inputEmail', 'placeholder'=>'Email or Username')) ?>
	    		</div>
	    	</div>
			<div class="control-group">
				<label class="control-label" for="inputPassword"><?php echo CHtml::activeLabelEx($model,'password'); ?></label>
				<div class="controls">
					<?php echo CHtml::activePasswordField($model,'password', array('id'=>'inputPassword', 'placeholder'=>'Password')) ?>
				</div>
			</div>
			<div class="control-group">
	    		<div class="controls">
	   				<label class="checkbox">
						<?php echo CHtml::activeCheckBox($model,'rememberMe'); ?> <?php echo CHtml::activeLabelEx($model,'rememberMe'); ?>
					</label>
					<?php echo CHtml::submitButton(UserModule::t("Sign in"), array('class'=>'btn')); ?>
				</div>
			</div>
		<?php echo CHtml::endForm(); ?><!-- form -->
	</div>
</div>
<?php endif;?>

<div style="display: none;">
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
</div>

<div id="new-feed-count" class="alert alert-info" style="display: none;">
	<div class="btn-show-new-feed btn-link" style="text-align:center;"></div>
</div>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'summaryText'=>'',
	'emptyText' => 'No feed yet.',
	'id'=>'feed-list',
));

Yii::app()->clientScript->registerScript('feed-index-js', "
	$('[rel=tooltip]').tooltip();
	var isguest = ".Yii::app()->user->id.";
	if (isguest == 0)
		$('#login-modal').modal({show: true, backdrop: 'static'});
	
//********* if there are any new news

	var oldCount = ".$dataProvider->totalItemCount.";
	var newCount = oldCount;
	
	setInterval(function(){getNewFeedCount()},60000);
	
	function getNewFeedCount() {
		$.ajax({
			type: 'post',
			url: '".$this->createUrl('feedCount')."',
			success: function (count) {
				newCount = count;
				var diffCount = newCount - oldCount;
				if (diffCount > 0) {
					var str = diffCount + ' new feed';
					if (diffCount > 1)
						str += 's';
					str += '. Click to refresh the list.';
					$('.btn-show-new-feed').text(str);
					$('#new-feed-count').attr('style', 'display: block;');
				}
			}
		});
	}

	$('.btn-show-new-feed').click(function(){
		oldCount = newCount;
		setTimeout(function() {
			$.fn.yiiListView.update('feed-list', {
				data: $(this).serialize()
			});
			$('#new-feed-count').attr('style','display: none;');
		}, 400);
	});
		
//********* change create-panel
				
	$('.btn-feed').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		if ($('#Message_description').val() == '')
			$('#message-form .form-rest').slideUp('fast');
		if ($('#Ask_title').val() == '' && $('#Ask_description').val() == '')
			$('#ask-form .form-rest').slideUp('fast');
		if ($('#Note_title').val() == '' && $('#Note_description').val() == '')
			$('#note-form .form-rest').slideUp('fast');
		if ($('#Todo_title').val() == '' && $('#Todo_description').val() == '')
			$('#todo-form .form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-feed').show();
			$('#create-panel-message').hide();
			$('#create-panel-ask').hide();
			$('#create-panel-note').hide();
			$('#create-panel-todo').hide();
		}, 100);
	});
		
	$('.btn-message').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		if ($('#Feed_description').val() == '')
			$('#feed-form .form-rest').slideUp('fast');
		if ($('#Ask_title').val() == '' && $('#Ask_description').val() == '')
			$('#ask-form .form-rest').slideUp('fast');
		if ($('#Note_title').val() == '' && $('#Note_description').val() == '')
			$('#note-form .form-rest').slideUp('fast');
		if ($('#Todo_title').val() == '' && $('#Todo_description').val() == '')
			$('#todo-form .form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-feed').hide();
			$('#create-panel-message').show();
			$('#create-panel-ask').hide();
			$('#create-panel-note').hide();
			$('#create-panel-todo').hide();
		}, 100);
	});

	$('.btn-ask').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		if ($('#Feed_description').val() == '')
			$('#feed-form .form-rest').slideUp('fast');
		if ($('#Message_description').val() == '')
			$('#message-form .form-rest').slideUp('fast');
		if ($('#Note_title').val() == '' && $('#Note_description').val() == '')
			$('#note-form .form-rest').slideUp('fast');
		if ($('#Todo_title').val() == '' && $('#Todo_description').val() == '')
			$('#todo-form .form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-feed').hide();
			$('#create-panel-message').hide();
			$('#create-panel-ask').show();
			$('#create-panel-note').hide();
			$('#create-panel-todo').hide();
		}, 100);
	});
		
	$('.btn-note').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		if ($('#Feed_description').val() == '')
			$('#feed-form .form-rest').slideUp('fast');
		if ($('#Message_description').val() == '')
			$('#message-form .form-rest').slideUp('fast');
		if ($('#Ask_title').val() == '' && $('#Ask_description').val() == '')
			$('#ask-form .form-rest').slideUp('fast');
		if ($('#Todo_title').val() == '' && $('#Todo_description').val() == '')
			$('#todo-form .form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-feed').hide();
			$('#create-panel-message').hide();
			$('#create-panel-ask').hide();
			$('#create-panel-note').show();
			$('#create-panel-todo').hide();
		}, 100);
	});
		
	$('.btn-todo').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		if ($('#Feed_description').val() == '')
			$('#feed-form .form-rest').slideUp('fast');
		if ($('#Message_description').val() == '')
			$('#message-form .form-rest').slideUp('fast');
		if ($('#Ask_title').val() == '' && $('#Ask_description').val() == '')
			$('#ask-form .form-rest').slideUp('fast');
		if ($('#Note_title').val() == '' && $('#Note_description').val() == '')
			$('#note-form .form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-feed').hide();
			$('#create-panel-message').hide();
			$('#create-panel-ask').hide();
			$('#create-panel-note').hide();
			$('#create-panel-todo').show();
		}, 100);
	});
	
//********* create feed, message, ask, note, todo
	
	$('#Feed_description').focus(function () {
		$('#feed-form .form-rest').slideDown();
	});
	
	$('#Feed_description').keyup(function(event) {
		if ($('#Feed_description').val() != '')
			$('#feed-form .btn-create').removeClass('disabled')
		else
			$('#feed-form .btn-create').addClass('disabled')
	});
		
	$('#feed-form .btn-create').live('click', function(){
		if($(this).hasClass('disabled')) {
			$('#Feed_description').attr('placeholder','Say something here!');
			setTimeout(function() {
				$('#Feed_description').attr('placeholder','What \'s up?');
				$('#Feed_description').focus();
			}, 400);
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('feed/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#feed-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('feed-list', {
						data: $(this).serialize()
					});
					$('#feed-form').find('textarea').val('');
					$('#feed-form .btn-create').addClass('disabled');
                }, 400);
				
				oldCount++;
			}
		});
		return false;
	
	});
	
	$('#feed-form .btn-cancel').click(function (){
		$('#feed-form .form-rest').slideUp();
		$('#feed-form .btn-create').addClass('disabled');
		$('#feed-form').find('textarea').val('');
		$('#feed-form').find('#Feed_description').attr('placeholder','What\'s up?');
	});
		
//--
	$('#Message_description').focus(function () {
		$('#message-form .form-rest').slideDown();
	});
	
	$('#Message_description').keyup(function(event) {
		if ($('#Message_description').val() != '')
			$('#message-form .btn-create').removeClass('disabled')
		else
			$('#message-form .btn-create').addClass('disabled')
	});
	
	$('#message-form .btn-create').click(function(){
		if($(this).hasClass('disabled'))
			return;
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('message/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#message-form .form-rest').slideUp();
				setTimeout(function() {
					$('#message-form .btn-create').addClass('disabled')
					$('#Message_description').val('');
                }, 400);
				$('.left-main-menu').find('.icon-envelope').parent().attr('style', 'background-color: #F5A9A9; color: white;');
				setTimeout(function() {
					$('.left-main-menu').find('.icon-envelope').parent().removeAttr('style');
				}, 600);
			}
		});
		return false;
	});
	
	$('#message-form .btn-cancel').click(function (){
		$('#message-form .btn-create').addClass('disabled')
		$('#Message_description').val('');
	});
		
//--
	$('#Ask_title').focus(function () {
		$('#ask-form .form-rest').slideDown();
	});
	
	$('#Ask_title, #Ask_description').keyup(function(event) {
		if ($('#Ask_title').val() != '' && $('#Ask_description').val() != '')
			$('#ask-form .btn-create').removeClass('disabled')
		else if ($('#Ask_title').val() == '' || $('#Ask_description').val() == '')
			$('#ask-form .btn-create').addClass('disabled')
	});
		
	$('#ask-form .btn-create').live('click', function(){
		if($(this).hasClass('disabled')) {
			if ($('#Ask_title').val() == '') {
				$('#Ask_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Ask_title').attr('placeholder','Title');
					$('#Ask_title').focus();
				}, 400);
			} else {
				$('#Ask_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Ask_description').attr('placeholder','Description');
					$('#Ask_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('ask/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#ask-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('feed-list', {
						data: $(this).serialize()
					});
					$('#ask-form').find('input').val('');
					$('#ask-form').find('textarea').val('');
					$('#ask-form .btn-create').addClass('disabled');
                }, 400);
				$('.left-main-menu').find('.icon-comment').parent().attr('style', 'background-color: #F5A9A9; color: white;');
				setTimeout(function() {
					$('.left-main-menu').find('.icon-comment').parent().removeAttr('style');
				}, 600);
				
				oldCount++;
			}
		});
		return false;
	
	});
	
	$('#ask-form .btn-cancel').click(function (){
		$('#ask-form .form-rest').slideUp();
		$('#ask-form .btn-create').addClass('disabled')
		$('#ask-form').find('textarea').val('');
		$('#ask-form').find('input').val('');
		$('#ask-form').find('#Ask_title').attr('placeholder','Ask a question');
	});
//--
	$('#Note_title').focus(function () {
		$('#note-form .form-rest').slideDown();
	});
	
	$('#Note_title, #Note_description').keyup(function(event) {
		if ($('#Note_title').val() != '' && $('#Note_description').val() != '')
			$('#note-form .btn-create').removeClass('disabled')
		else if ($('#Note_title').val() == '' || $('#Note_description').val() == '')
			$('#note-form .btn-create').addClass('disabled')
	});
	
	$('#note-form .btn-create').click(function(){
		if($(this).hasClass('disabled')) {
			if ($('#Note_title').val() == '') {
				$('#Note_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Note_title').attr('placeholder','Title');
					$('#Note_title').focus();
				}, 400);
			} else {
				$('#Note_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Note_description').attr('placeholder','Description');
					$('#Note_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('note/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#note-form .form-rest').slideUp();
				setTimeout(function() {
					$('#note-form').find('textarea').val('');
					$('#note-form').find('input').val('');
					$('#note-form .btn-create').addClass('disabled');
                }, 400);
				$('.left-main-menu').find('.icon-edit').parent().attr('style', 'background-color: #F5A9A9; color: white;');
				setTimeout(function() {
					$('.left-main-menu').find('.icon-edit').parent().removeAttr('style');
				}, 600);
			}
		});
		return false;
	});
	
	$('#note-form .btn-cancel').click(function (){
		$('#note-form .form-rest').slideUp();
		$('#note-form .btn-create').addClass('disabled')
		$('#note-form').find('textarea').val('');
		$('#note-form').find('input').val('');
		$('#note-form').find('#Note_title').attr('placeholder','Create a note');
	});
		
//--	
	$('#Todo_title').focus(function () {
		$('#todo-form .form-rest').slideDown();
	});
		
	$('#Todo_description').focus(function () {
		$(this).attr('placeholder','Description');
	});
	
	$('#Todo_title, #Todo_description').keyup(function(event) {
		if ($('#Todo_title').val() != '')
			$('#todo-form .btn-create').removeClass('disabled')
		else
			$('#todo-form .btn-create').addClass('disabled')
	});
	
	$('#todo-form .btn-create').live('click', function(){
		if($(this).hasClass('disabled')) {
			if ($('#Todo_title').val() == '') {
				$('#Todo_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Todo_title').attr('placeholder','Title');
					$('#Todo_title').focus();
				}, 400);
			} else {
				$('#Todo_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Todo_description').attr('placeholder','Description');
					$('#Todo_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('todo/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#todo-form .form-rest').slideUp();
                setTimeout(function() {
					$('#todo-form').find('textarea').val('');
					$('#todo-form').find('input').val('');
					$('#todo-form .btn-create').addClass('disabled');
                }, 400);
				$('.left-main-menu').find('.icon-check').parent().attr('style', 'background-color: #F5A9A9; color: white;');
				setTimeout(function() {
					$('.left-main-menu').find('.icon-check').parent().removeAttr('style');
				}, 600);
			}
		});
		return false;
	
	});	
	
	$('#todo-form .btn-cancel').click(function (){
		$('#todo-form .form-rest').slideUp();
		$('#todo-form .btn-create').addClass('disabled')
		$('#todo-form').find('textarea').val('');
		$('#todo-form').find('#Todo_title').attr('placeholder','Create a todo');
	});

//********* feed-list

	function descriptionClick() {
		var description_ori=$(this).html();
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><textarea rows=\"4\" id=\"description\" style=\"width: 100%;\"></textarea></div>');
		$(this).find('#description').text(htmlDecode(description_ori));
		$(this).find('#description').focus();
		$(this).find('#description').focusout(function(){
			var description = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(description));
			$('.content-description-feed').live('click', descriptionClick);
			if (description_ori != htmlEncode(description))
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id, description: description}
				});
		});
		
		$('.content-description-feed').die('click');
	}
		
	$('.content-description-feed').live('click', descriptionClick);
		
	$('#feed-list .post').live('mouseenter', function (){
		$(this).find('.social-bar').fadeIn('fast');
	});
	
	$('#feed-list .post').live('mouseleave', function (){
		$(this).find('.social-bar').fadeOut('fast');
	});
	
	$('#feed-list .delete').live('click', function() {
		var elem = $(this).closest('.post');
		\$this=$(this);
		bootbox.confirm('Delete this feed?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						$.fn.yiiListView.update('feed-list', {
							data: $(this).serialize()
						});
						setTimeout(function() {
							elem.slideUp();
						}, 500);
					}
				});
				return false;
			}
		});
	});
		
//********* add comment to feed
		
	$('#feed-list .post-comment .fake-input').live('click', function(){
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
	
	$('#feed-list .post-comment .comment-form-cancel').live('click', function(){
		$(this).prev().prev().val('');
		$(this).parent().hide();
		$(this).parent().prev().show();
		$(this).prev().addClass('disabled');
	})
	
	$('#feed-list .post-comment .comment-form-create').live('click', function(){
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
							+ '<img width=\"40px\" height=\"40px\" class=\"img-polaroid\" src=\"".Yii::app()->baseUrl."/uploads/images/profile-avatar/0.png\"/>'
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
		
	$('.btn-share').live('click', function() {
		var id = $(this).closest('.post').find('#data_id').val();
		var str = '@' + $(this).closest('.post').find('.user-name').html() + $(this).closest('.post').find('.content-description-feed').text();
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('sharePrepare')."',
			data: {id: id},
			success: function (description) {
				$('#share-canvas').find('.shared-content').text(str);
				$('#share-form #Feed_of').val('feed');
				$('#share-form #Feed_of_id').val(id);
				$('#share-form #Feed_from_id').val(id);
				$('#share-canvas').find('#description').val(description);
				$('#share-canvas').find('#description').focus();
			}
		});
	});
		
	$('.btn-share-create').live('click', function(){
		$('#share-form #Feed_description').val($('#share-canvas').find('#description').val());

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('create')."',
			data: $('#share-form').serialize(),
			success: function (html) {
				$.fn.yiiListView.update('feed-list', {
					data: $(this).serialize()
				});
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
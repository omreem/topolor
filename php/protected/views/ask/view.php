<div class="post top-panel-fix">
	<?php if($model->learner_id == Yii::app()->user->id) {?><div class='btn btn-link pull-right delete' style="color: #ddd; margin-right: -3px;">x</div><?php }?>
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
		<span class="user-name"><?php echo GxHtml::encode($model->learner);?>: </span>
		<span class="content-description-feed" style="margin-bottom: 8px;"><?php echo GxHtml::encode($model->title); ?></span>
		<div class="content-details clearfix" style="display: block;">
			<div class='content-description'><?php echo GxHtml::encode($model->description); ?></div>
			<?php if ($model->concept != null): ?>
			<p class="content-metadata">
				Module: <a href="<?php echo Yii::app()->homeUrl.'/module/'.$model->concept->module->id;?>"><?php echo GxHtml::encode($model->concept->module->title);?></a><br>
				<?php if ($model->concept_id != $model->concept->module->id) {?>
				Concept: <a href="<?php echo Yii::app()->homeUrl.'/concept/'.$model->concept->id;?>"><?php echo GxHtml::encode($model->concept->title);?></a>
				<?php } ?>
			</p>
			<?php endif;?>
			<p class="content-tag">
				<?php if ($model->tags != ''): ?>
				<b>Tag:</b> <?php echo implode(' ', $model->tagLabels); ?> <a data-toggle="modal" href="#tag-canvas"><i class="icon-pencil transparent50 btn-tag-edit" style="display: none;"></i></a>
				<?php else: ?>
				<a data-toggle="modal" href="#tag-canvas" class="label label-info add-tag">+ tag</a>
				<?php endif; ?>
			</p>
		</div>
		<span class="date-time">
			<?php echo Helpers::datatime_trim($model->create_at);?>
			<?php if ($model->update_at != '') echo ' (edited at '.Helpers::datatime_trim($model->update_at).')';?>
		</span>
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
<div class="post-comment"><?php $answerCount = $model->answerCount?>
	<form class="answer-form" id="f-ar">
		<div class="control-group">
			<textarea name="Answer[description]" id="Answer_description" placeholder="Answer it"></textarea>
		</div>
		<input type="hidden" name="Answer[ask_id]" id="Answer_ask_id" value="<?php echo $model->id;?>">
		<a class="btn btn-primary answer-form-create">Submit</a>
	</form>
	<?php $this->widget('zii.widgets.CListView', array(
		'id' => 'answer-list',
		'dataProvider'=>new CArrayDataProvider($model->answersAll, array(
			'keyField'=>'id',
			'pagination' => array(
					'pageSize' => 10,
			),
		)),
		'itemView' => '/answer/_view',
		'summaryText' => '',
		'emptyText' =>'',
	));?>
</div>

<div id="tag-canvas" class="modal hide fade in" style="display: none;">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<b>Add tags</b>
	</div>
	<div class="modal-body" style="display: table">
		<div style="display: table-row">
			<div style="display: table-cell; width: 70px;">My tags:</div>
			<div style="display: table-cell" class="modal-body-tags"></div>
		</div><br>
		<div style="display: table-row">
			<div style="display: table-cell;">Tags:</div>
			<div style="display: table-cell">
				<input id="add-tags-input" type="text">
				<input id="data_id" type="hidden">
				<input id="add-tags-input_ori" type="hidden">
			</div>
		</div>
	</div>
	<div class="alert alert-success" style="display: none;">Successfully saved!</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-primary btn-small btn-save-tags disabled">Save</a>
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
Yii::app()->clientScript->registerScript('ask-view-js', "
	$(document).ready(function() {
		
		$('#Answer_description').click(function () {
			$(this).parent().removeClass('error');
			$(this).attr('placeholder','Description');
		});
		
		$('#Answer_description').keyup(function(event) {
			if ($(this).val() != '')
				$(this).parent().removeClass('error');
		});
		
		$('#Answer_description').focusout(function(event) {
			if ($(this).val() == '') {
				$(this).parent().addClass('error');
				$(this).attr('placeholder','Please input a description!');
			}
		});
		
		$('.answer-form-create').live('click', function(){
			\$this=$(this);
			\$form = \$this.parent();
		
			if ($('#Answer_description').val() == '') {
				$('#Answer_description').parent().addClass('error');
				$('#Answer_description').focus();
				$('#Answer_description').attr('placeholder','Please input a description!');
				return false;
			}

			$.ajax({
				type: 'POST',
				url: '".$this->createUrl('/answer/create')."',
				data: \$form.serialize(),
				success: function (html) {
					$.fn.yiiListView.update('answer-list', {
							data: $(this).serialize()
					});
					$('.form-rest').slideUp();
	                setTimeout(function() {
						$('input[type=text], textarea').val('');
						$('#Answer_description').attr('placeholder','Answer it');
	                }, 400);
				}
			});
			return false;
		
		});
		
		$('.delete').click(function() {
			bootbox.confirm('Are you sure?', function(result) {
			    if (result) {
					$.ajax({
						type: 'POST',
						url: '/topolor/index.php/ask/delete/'+$('#data_id').val(),
						success: function(data) {
							window.location = '".Yii::app()->homeUrl."/ask';
						}
					});
					return false;
				}
			});
		});
		
		$('#answer-list .comment-item').live('hover', function (){
			$(this).children('.content').children('.owner').fadeIn('fast');
		});
		
		$('#answer-list .comment-item').live('mouseleave', function (){
			$(this).children('.content').children('.owner').fadeOut('fast');
		});
		
		$('.btn-edit').live('click', function() {
			\$this=$(this);
			$.ajax({
				type: 'GET',
				url: '".$this->createUrl('/answer/updateView')."',
				data: { view: 'update', id: \$this.prev().val()},
				success: function (html) {
					\$this.parent().parent().children('.description').html(html);
            	}  
			});
		});
		
		$('.btn-delete').live('click', function() {
			\$this=$(this);
			bootbox.confirm('Are you sure?', function(result) {
			    if (result) {
					$.ajax({
						type: 'POST',
						url: '".$this->createUrl('/answer/delete/')."'.concat('/').concat(\$this.prev().prev().prev().val()),
						success: function(data) {
							\$this.parent().parent().parent().slideUp();
						}
					});
			    }
			});
		});
		
		$('.btn-update').live('click', function() {
			\$this=$(this);
			\$form = \$this.parent();
			\$input = \$this.prev().prev().children('#Answer_description');
		
			if (\$input.val() == '') {
				\$input.parent().addClass('error');
				\$input.focus();
				\$input.attr('placeholder','Please input a description!');
				return false;
			}

			$.ajax({
				type: 'POST',
				url: '".$this->createUrl('/answer/updateAjax')."',
				data: \$form.serialize(),
				success: function (html) {
					\$this.parent().parent().parent().children('.owner').fadeOut('fast');
					\$this.parent().html(html);
				}
			});
			return false;
		});
		
		$('.btn-cancel').live('click', function() {
			\$this=$(this);
			\$str=\$this.prev().prev().prev().children('#Answer_description').val();
			\$this.parent().html(\$str);
		});
		
	});

//********* edit tags of asks

	function tagModal() {
		\$this = $(this);
		var id = $(this).closest('.post').find('#data_id').val();
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('createTagCanvas')."',
			data: {id: id},
			success: function (tagInfo) {
				$('#tag-canvas').find('.modal-body-tags').html(tagInfo.allTags);
				$('#tag-canvas').find('#add-tags-input').val(tagInfo.thisTag);
				$('#tag-canvas').find('#add-tags-input_ori').val(tagInfo.thisTag);

				var str = $.trim(tagInfo.thisTag);
				var arr = str.split(',');
				$('.modal-body-tags .pick-tag').each(function () {
					for (var i=0; i< arr.length; i++) {
						var item = $.trim(arr[i]);
						if ($(this).text() == item) {
							$(this).removeClass('label-info');
							break;
						}
					}
					if (i == arr.length)
						$(this).addClass('label-info');
				});

				$('#tag-canvas').find('#data_id').val(id);
				$('.btn-save-tags').click(function(){
				
					if($(this).hasClass('disabled'))
						return false;
					
					$('.btn-save-tags').addClass('disabled');
		
					var id = $('#tag-canvas').find('#data_id').val();
					var tags = $('#tag-canvas').find('#add-tags-input').val();

					$.ajax({
						type: 'POST',
						url: '".$this->createUrl('updateAjax')."',
						data: {id: id, tags: tags},
						success: function (html) {
							$('#tag-canvas').find('.alert-success').show();
							if (tags != '') {
								$.ajax({
									type: 'GET',
									url: '".$this->createUrl('getTags')."',
									data: {id: id},
									success: function (tags) {
										\$this.closest('.content-tag').html('<b>Tag:</b> '+tags+' <a data-toggle=\"modal\" href=\"#tag-canvas\"><i class=\"icon-pencil transparent50 btn-tag-edit\" style=\"display: none;\"></i></a>');
									}
								});
							} else {
								\$this.closest('.content-tag').html('<a data-toggle=\"modal\" href=\"#tag-canvas\" class=\"label label-info add-tag\">+ tag</a>');
							}

			                setTimeout(function() {
								$('#tag-canvas').find('.alert-success').hide();
								$('.modal.in').modal('hide');
			                }, 1200);
						}
					});
					return false;
				});
			}
		});
		return false;
	}
		
	$('.add-tag').live('click', tagModal);
		
	$('.add-tag').live('mouseenter', function(){
		$(this).css('cursor','pointer');
	});
		
	$('.add-tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.content-tag').live('mouseenter', function(){
		$(this).find('.icon-pencil').show();
	});
		
	$('.content-tag').live('mouseleave', function(){
		$(this).find('.icon-pencil').hide();
	});
	
	$('.btn-tag-edit').live('click', tagModal);

	$('.btn-tag-edit .icon-pencil').live('mouseenter', function(){
		$(this).css('cursor','pointer');
	});

	$('.btn-tag-edit .icon-pencil').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.modal-body-tags .pick-tag').live('mouseenter', function(){
		if (!$(this).hasClass('label-info'))
			return false;
		$(this).css('cursor','pointer');
	});

	$('.modal-body-tags .pick-tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.modal-body-tags .pick-tag').live('click', function(){
		if (!$(this).hasClass('label-info'))
			return false;
		
		var ori = $.trim($('#tag-canvas').find('#add-tags-input').val());
		var newStr = '';
		if (ori != '') {
			newStr = ori;
			if (ori.substr(ori.length - 1) != ',')
				newStr += ','
			newStr += ' ' + $(this).text() + (', ');
		}
		else
			newStr = $(this).text() + (', ');

		$('#tag-canvas').find('#add-tags-input').val(newStr);
		$(this).removeClass('label-info');
		$(this).removeClass('cursor');
		tagsChanged();
	});
		
	$('#add-tags-input').keyup(function(){
		var str = $.trim($(this).val());
		var arr = str.split(',');
		$('.modal-body-tags .pick-tag').each(function () {
			for (var i=0; i< arr.length; i++) {
				var item = $.trim(arr[i]);
				if ($(this).text() == item) {
					$(this).removeClass('label-info');
					break;
				}
			}
			if (i == arr.length)
				$(this).addClass('label-info');
		});
		
		tagsChanged();
	});
		
	function tagsChanged() {
		var s_ori = $.trim($('#add-tags-input_ori').val());
		var s_new = $.trim($('#add-tags-input').val());
		
		if(s_ori.charAt(s_ori.length-1) == ',')
			s_ori = s_ori.substr(0, s_ori.length-1);
		
		if(s_new.charAt(s_new.length-1) == ',')
			s_new = s_new.substr(0, s_new.length-1);
		
		if(s_ori != s_new)
			$('.btn-save-tags').removeClass('disabled');
		else
			$('.btn-save-tags').addClass('disabled');
	}

//********* favorite
		
	$('.btn-favorite').live('click', function(){
		var id = $(this).closest('.post').find('#data_id').val();
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('favorite/favorite')."',
			data: {id: id, of: 'ask'},
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
			data: {id: id, of: 'ask'},
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
		$('#share-form #Feed_of').val('ask');
		$('#share-form #Feed_of_id').val(id);
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

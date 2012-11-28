<div class="well top-panel-fix">
	<div style="margin-bottom:-20px;">
	<?php $this->renderPartial('/ask/_form', array('model' => $newAsk));?>
	</div>
</div><!-- form -->
<ul class="nav nav-tabs">
	<li class="active filter-btn-all"><a class="btn-link">All</a></li>
	<li class="filter-btn-myquestions"><a class="btn-link">My questions</a></li>
	<li class="filter-btn-myanswers"><a class="btn-link">My answers</a></li>
	<li class="filter-btn-tags pull-right"><a class="btn-link" onclick="$('#tags-bar').toggle();">All tags</a></li>
	<li class="filter-btn-concepts pull-right"><a class="btn-link" onclick="$('#concepts-bar').toggle();">All concepts</a></li>
</ul>
<div id="concepts-bar" style="display:none;"></div>
<div id="tags-bar" style="display:none;"></div>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'method' => 'get',
	'id' => 'filter-form',
)); ?>
<input name="filter_by" id="filter_by" type="hidden"/>
<input name="tag" id="tag" type="hidden"/>
<input name="concept_id" id="concept_id" type="hidden"/>
<?php $this->endWidget(); ?>

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
<?php $this->endWidget(); ?>
</div>

<div id="new-ask-count" class="alert alert-info" style="display: none;">
	<div class="btn-show-new-ask btn-link" style="text-align:center;"></div>
</div>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'/ask/_view',
	'summaryText'=>'',
	'emptyText' => 'No Q&amp;A yet.',
	'id'=>'ask-list',
));

Yii::app()->clientScript->registerScript('ask-index-js', "
		
	$('[rel=tooltip]').tooltip();
	
	// init filter bar
	$.ajax({
		type: 'POST',
		url: '".$this->createUrl('ask/updateFiltersBar')."',
		data: $('#filter-form').serialize(),
		success: function (barInfo) {
			$('#tags-bar').html(barInfo.tagsBar);
			$('#concepts-bar').html(barInfo.conceptsBar);
		}
	});
	
//********* if there are any new asks

	var oldCount = ".$dataProvider->totalItemCount.";
	var newCount = oldCount;
	
	setInterval(function(){getNewAskCount();},30000);
	
	function getNewAskCount() {
		$.ajax({
			type: 'post',
			url: '".$this->createUrl('ask/askCount')."',
			success: function (count) {
				newCount = count;
				var diffCount = newCount - oldCount;
				if (diffCount > 0) {
					var str = diffCount + ' new question';
					if (diffCount > 1)
						str += 's';
					str += '. Click to refresh the list.';
					$('.btn-show-new-ask').text(str);
					$('#new-ask-count').attr('style', 'display: block;');
				}
			}
		});
	}

	$('.btn-show-new-ask').click(function(){
		oldCount = newCount;
		setTimeout(function() {
			$.fn.yiiListView.update('ask-list', {
				data: $(this).serialize()
			});
			$('#new-ask-count').attr('style','display: none;');
		}, 400);
	});
		
//********* create ask
	
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
					$.fn.yiiListView.update('ask-list', {
						data: $(this).serialize()
					});
					$('#ask-form').find('input').val('');
					$('#ask-form').find('textarea').val('');
					$('#Ask_title').attr('placeholder','Ask a question');
					$('#ask-form .btn-create').addClass('disabled');
                }, 400);
				
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('ask/updateFiltersBar')."',
					data: $('#filter-form').serialize(),
					success: function (barInfo) {
						$('#tags-bar').html(barInfo.tagsBar);
						$('#concepts-bar').html(barInfo.conceptsBar);
					}
				});
				
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

//********* update ask in the ask-list
			
	function titleClick() {
		if(!$(this).hasClass('edible'))
			return false;
		
		var title_ori=$(this).html();
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><input id=\"title\" type=\"text\" style=\"width: 100%;\"></div>');
		$(this).find('#title').val(htmlDecode(title_ori));
		$(this).find('#title').focus();
		$(this).find('#title').focusout(function(){
			var title = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(title));
			$('.content-title').live('click', titleClick);
			if (title_ori != htmlEncode(title))
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('ask/updateAjax')."',
					data: {id: id, title: title}
				});
		});
		
		$('.content-title').die('click');
	}
	
	function descriptionClick() {
		if(!$(this).hasClass('edible'))
			return false;
		
		var description_ori=$(this).html();
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><textarea rows=\"4\" id=\"description\" style=\"width: 100%;\"></textarea></div>');
		$(this).find('#description').text(htmlDecode(description_ori));
		$(this).find('#description').focus();
		$(this).find('#description').focusout(function(){
			var description = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(description));
			$('.content-description').live('click', descriptionClick);
			if (description_ori != htmlEncode(description))
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('ask/updateAjax')."',
					data: {id: id, description: description}
				});
		});
		
		$('.content-description').die('click');
	}
	
	$('.content-title').live('click',titleClick);
		
	$('.content-description').live('click', descriptionClick);
	
	$('#ask-list .post').live('mouseenter', function (){
		$(this).children('.post-content').children('.social-bar').fadeIn('fast');
	});
	
	$('#ask-list .post').live('mouseleave', function (){
		$(this).children('.post-content').children('.social-bar').fadeOut('fast');
	});
	
	$('#ask-list .delete').live('click', function() {
		\$this=$(this);
		bootbox.confirm('Are you sure?', function(result) {
			if (result) {
				var elem_post = \$this.closest('.post');
				var elem_comment = elem_post.next();
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('ask/delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						setTimeout(function() {
							elem_post.slideUp();
							elem_comment.slideUp();
						}, 500);
		
						$.ajax({
							type: 'POST',
							url: '".$this->createUrl('ask/updateFiltersBar')."',
							data: $('#filter-form').serialize(),
							success: function (barInfo) {
								$('#tags-bar').html(barInfo.tagsBar);
								$('#concepts-bar').html(barInfo.conceptsBar);
							}
						});
					}
				});
				return false;
			}
		});
	});

//********* update answer in the answer-list of an ask		
		
	$('.comment-item').live('mouseenter', function (){
		$(this).children('.content').children('.owner').fadeIn('fast');
	});
	
	$('.comment-item').live('mouseleave', function (){
		$(this).children('.content').children('.owner').fadeOut('fast');
	});
		
	$('.comment-item .btn-edit').live('click', function() {
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
		
	$('.comment-item .btn-update').live('click', function() {
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
		
	$('.comment-item .btn-cancel').live('click', function() {
		\$this=$(this);
		\$str=\$this.prev().prev().prev().children('#Answer_description').val();
		\$this.parent().html(\$str);
	});

	$('.comment-item .btn-delete').live('click', function (){
		\$this=$(this);
		\$commentCount=\$this.closest('.post-comment').children('#comment-count');
		bootbox.confirm('Are you sure?', function(result) {
		    if (result) {
				\$numLen=\$commentCount.text().charAt(\$commentCount.text().length-1)=='s'?(\$commentCount.text().length-7):(\$commentCount.text().length-6);
				\$newCount=parseInt(\$commentCount.text().substr(0,\$numLen))-1;
				if(\$newCount>1)
					\$str=\$newCount.toString().concat(' answers');
				else
					\$str=\$newCount.toString().concat(' answer');
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('/answer/delete/')."'.concat('/').concat(\$this.prev().prev().prev().val()),
					success: function(data) {
						setTimeout(function() {
							\$this.parent().parent().parent().slideUp();
							\$commentCount.html(\$str);
							if (\$newCount==0) {
								\$commentCount.unbind('click');
								\$commentCount.removeClass('btn-link');
							}
						}, 500);
					}
				});
		    }
		});
	});
	
//********* add answer in the answer-list of an ask	
		
	$('#ask-list .post-comment .fake-input').live('click', function(){
		var fakeInput = $(this);
		fakeInput.hide();
		fakeInput.next().show();
		var input = $(this).next().children('#Answer_description');
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
	
	$('#ask-list .post-comment .answer-form-cancel').live('click', function(){
		$(this).prev().prev().val('');
		$(this).parent().hide();
		$(this).parent().prev().show();
		$(this).prev().addClass('disabled');
	})
	
	$('#ask-list .post-comment .answer-form-create').live('click', function(){
		if($(this).hasClass('disabled'))
			return false;
		\$form = $(this).parent();
		var newAnswerDescription = \$form.children('#Answer_description').val();
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/answer/create')."',
			data: \$form.serialize(),
			success: function (data) {
				\$form.children('#Answer_description').val('');
				\$form.children('.answer-form-create').addClass('disabled');
				\$form.hide();
				\$form.prev().show();
				\$commentCount=\$form.closest('.post-comment').children('#comment-count');
				\$numLen=\$commentCount.text().charAt(\$commentCount.text().length-1)=='s'?(\$commentCount.text().length-7):(\$commentCount.text().length-6);
				\$newCount=parseInt(\$commentCount.text().substr(0,\$numLen))+1;
				if(\$newCount>1)
					\$commentCount.text(\$newCount.toString().concat(' answers'));
				else {
					\$commentCount.text(\$newCount.toString().concat(' answer'));
					\$commentCount.addClass('btn-link');
					\$commentCount.attr('onclick', '$(this).next().slideToggle();');
				}
				\$form.closest('.post-comment').find('#answer-list').children('.items').prepend('<div class=\"comment-item clearfix\" style=\"display: none;\"><div class=\"user-avatar\"><img style=\"width:48px; height:48px;\" class=\"img-polaroid\" src=\"".Yii::app()->baseUrl."/uploads/images/profile-avatar/".Yii::app()->user->id."\"/></div><div class=\"content\"><span class=\"user-name\">".CHtml::encode(Yii::app()->user->name)."</span>:<span class=\"pull-right owner\" style=\"display:none;\"><input type=\"hidden\" id=\"answer_id\" value=\"'.concat(data).concat('\"><span class=\"btn-link btn-edit\">edit</span><span style=\"color:grey;\">&nbsp;/&nbsp;</span><span class=\"btn-link btn-delete\">delete</span></span><br><span class=\"description\">').concat(newAnswerDescription).concat('</span></div></div>'));
				\$form.closest('.post-comment').find('#answer-list div:first-child').slideDown();
			}
		});
		
	});

//********* filter the asks in the ask-list
		
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('ask-list', { 
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('ask/updateFiltersBar')."',
			data: $('#filter-form').serialize(),
			success: function (barInfo) {
				$('#tags-bar').html(barInfo.tagsBar);
				$('#concepts-bar').html(barInfo.conceptsBar);
			}
		});
	    return false;
	});
	
	$('.filter-btn-all').click(function(){
		$(this).addClass('active');
		$('.filter-btn-myquestions').removeClass('active');
		$('.filter-btn-myanswers').removeClass('active');
		
		$('#filter-form #filter_by').val('');
		$('#filter-form').submit();
	});
		
	$('.filter-btn-myquestions').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-myanswers').removeClass('active');
		
		$('#filter-form #filter_by').val('myquestions');
		$('#filter-form').submit();
	});
		
	$('.filter-btn-myanswers').click(function(){
		$(this).addClass('active');
		$('.filter-btn-myquestions').removeClass('active');
		$('.filter-btn-all').removeClass('active');
		
		$('#filter-form #filter_by').val('myanswers');
		$('#filter-form').submit();
	});
		
	$('.tag').live('mouseenter', function(){
		$(this).css('cursor','pointer');
		if (!$(this).hasClass('selected'))
			$(this).addClass('label-info');
	});
		
	$('.tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
		if (!$(this).hasClass('selected'))
			$(this).removeClass('label-info');
	});
		
	$('.tag').live('click', function(){
		var tag = htmlEncode($(this).text());
		if (tag.lastIndexOf('(') != -1)
			tag = $(this).text().substr(0, tag.lastIndexOf('('));
		if ($(this).attr('id') == 'all-tag')
			tag = '';
		
		$('#filter-form #tag').val(tag);
		$('#filter-form').submit();
		$('#tags-bar').show();
		$('#tags-bar > span').removeClass('label-info');
		$('#tags-bar > span').removeClass('selected');
		$('#tags-bar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('label-info');
		$('#tags-bar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('selected');
		
		if (tag=='') {
			$(this).addClass('label-info');
			$(this).addClass('selected');
		}
		
		if($('#filter-form #tag').val() != '')
			$('.filter-btn-tags .btn-link').text('Tag: '.concat($('#filter-form #tag').val()));
		else
			$('.filter-btn-tags .btn-link').text('All tags');
	});
		
	$('.concept').live('mouseenter', function(){
		$(this).css('cursor','pointer');
		if (!$(this).hasClass('selected'))
			$(this).addClass('label-success');
	});
		
	$('.concept').live('mouseleave', function(){
		$(this).removeClass('cursor');
		if (!$(this).hasClass('selected'))
			$(this).removeClass('label-success');
	});
		
	$('.concept').live('click', function(){
		var concept_id = $(this).attr('name');
		
		$('#filter-form #concept_id').val(concept_id);
		$('#filter-form').submit();
		$('#concepts-bar').show();
		$('#concepts-bar > span').removeClass('label-success');
		$('#concepts-bar > span').removeClass('selected');
		$('#concepts-bar > span[name='.concat('\"').concat(concept_id).concat('\"').concat(']')).addClass('label-success');
		$('#concepts-bar > span[name='.concat('\"').concat(concept_id).concat('\"').concat(']')).addClass('selected');
		
		var concept_name = htmlEncode($(this).text());
		if (concept_name.lastIndexOf('(') != -1)
			concept_name = concept_name.substr(0, concept_name.lastIndexOf('('));
		if ($(this).attr('id') == 'all-concept') {
			$(this).addClass('selected label-success');
			concept_name = 'All concepts';
		}
		$('.filter-btn-concepts .btn-link').text(concept_name);
		
	});

//********* edit tags of asks

	function tagModal() {
		\$this = $(this);
		var id = $(this).closest('.post').find('#data_id').val();
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('ask/createTagCanvas')."',
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
						url: '".$this->createUrl('ask/updateAjax')."',
						data: {id: id, tags: tags},
						success: function (html) {
							$('#tag-canvas').find('.alert-success').show();
							if (tags != '') {
								$.ajax({
									type: 'GET',
									url: '".$this->createUrl('ask/getTags')."',
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
		
							$.ajax({
								type: 'POST',
								url: '".$this->createUrl('ask/updateFiltersBar')."',
								data: $('#filter-form').serialize(),
								success: function (barInfo) {
									$('#tags-bar').html(barInfo.tagsBar);
									$('#concepts-bar').html(barInfo.conceptsBar);
								}
							});
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
				$.fn.yiiListView.update('ask-list', {
					data: $(this).serialize()
				});
				$('#share-form').find('textarea').val('');
				$('#share-form').find('input').val('');
		
				setTimeout(function() {
					$('.left-main-menu').find('.icon-list-alt').parent().attr('style', 'background-color: #F5A9A9; color: white;');
					setTimeout(function() {
						$('.left-main-menu').find('.icon-list-alt').parent().removeAttr('style');
					}, 600);
				}, 600);
			}
		});
		setTimeout(function() {
			$('.modal.in').modal('hide');
			$('#share-canvas').find('#description').val('');
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
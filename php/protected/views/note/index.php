<div class="well top-panel-fix">
	<div style="margin-bottom:-20px;">
	<?php $this->renderPartial('_form', array('model' => $newNote));?>
	</div>
</div><!-- form -->
<ul class="nav nav-tabs">
	<li class="active filter-btn-all"><a class="btn-link">All</a></li>
	<li class="filter-btn-today"><a class="btn-link">Today</a></li>
	<li class="filter-btn-week"><a class="btn-link">This week</a></li>
	<li class="filter-btn-month"><a class="btn-link">This month</a></li>
	<li class=" filter-btn-tags pull-right"><a class="btn-link" onclick="$('#tags-bar').toggle();">All tags</a></li>
	<li class=" filter-btn-concepts pull-right"><a class="btn-link" onclick="$('#concepts-bar').toggle();">All concepts</a></li>
</ul>
<div id="concepts-bar" style="display:none;">
	<?php echo $this->initConceptBar();?>
</div>
<div id="tags-bar" style="display:none;">
	<?php echo $this->initTagBar();?>
</div>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'method' => 'get',
	'id' => 'filter-form',
)); ?>
<input name="interval" id="interval" type="hidden"/>
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
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'summaryText'=>'',
	'emptyText' => 'No note yet.',
	'id'=>'note-list',
));

Yii::app()->clientScript->registerScript('note-index-js', "
		
//********* create note

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
			url: '".$this->createUrl('create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#note-form .form-rest').slideUp();
				setTimeout(function() {
					$.fn.yiiListView.update('note-list', {
						data: $(this).serialize()
					});
					$('#note-form').find('textarea').val('');
					$('#note-form').find('input').val('');
					$('#Note_title').attr('placeholder','Create a note');
					$('#note-form .btn-create').addClass('disabled');
                }, 400);
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

//********* update note in the note-list
		
	function titleClick() {
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
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id, title: title}
				});
		});
		
		$('.content-title').die('click');
	}
	
	function descriptionClick() {
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
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id, description: description}
				});
		});
		
		$('.content-description').die('click');
	}
	
	$('.content-title').live('click',titleClick);
		
	$('.content-description').live('click', descriptionClick);
		
	$('#note-list .post').live('mouseenter', function (){
		$(this).find('.social-bar').fadeIn('fast');
	});
	
	$('#note-list .post').live('mouseleave', function (){
		$(this).find('.social-bar').fadeOut('fast');
	});
	
	$('#note-list .delete').live('click', function() {
		var elem = $(this).closest('.post');
		\$this=$(this);
		bootbox.confirm('Delete this note?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						setTimeout(function() {
							elem.slideUp();
						}, 500);
					}
				});
				return false;
			}
		});
	});

//********* filter the notes in the note-list
		
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('note-list', {
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateFiltersBar')."',
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
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-today').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('today');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-week').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('week');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-month').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		
		$('#filter-form #interval').val('month');
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

//********* edit tags of a note
	
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
						data: {id: id, tags: tags, returnNote: 'fuck'},
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
			data: {id: id, of: 'note'},
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
			data: {id: id, of: 'note'},
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
		$('#share-form #Feed_of').val('note');
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
				$.fn.yiiListView.update('note-list', {
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

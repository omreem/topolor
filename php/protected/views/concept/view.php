<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><?php echo $breadcrumbs;?></span>
	  	<?php if ($model->isModule()) {?>
	  		<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
	  	<?php } else {?>
	  		<div id="learnt-info" class="pull-right">
	  		<?php if ($learnt_at != null) {?>
	  		<span class="date-time pull-right">Learnt at: <?php echo Helpers::datatime_feed($learnt_at);?></span>
	  		<?php } else { ?>
	  		<?php echo CHtml::ajaxButton ("I've learnt",
							CController::createUrl('hasLearnt'), 
							array('update' => '#learnt-info',
								'type' => 'POST',
								'data' => array(
									'concept_id' => $model->id,
								),
							),
							array('class' =>'btn pull-right',
								'id' => 'hl'.uniqid(),
				));?>
	  		<?php } ?>
	  		</div>
  		<?php }?>
  </div>
  <?php if ($model->isModule()) {?><div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($model->id);?></div><?php }?>
</div>

<div class="well">
	<div style="display:inline;">
		<?php if ($model->isModule()) {?>
			<a class="btn btn-primary">Pre-test</a>
		<?php } else {
			if ($canHasQuiz == 'yes') {
				echo CHtml::link ('Take a Quiz', '',
					array(
							'class' =>'btn btn-primary',
							'id' => 'lq'.uniqid(),
							'submit' => CController::createUrl('/quiz/view'),
							'params' => array('concept_id'=>$model->id),
					));
				} else { ?>
			<button class="btn btn-primary disabled">Take a Quiz</button>
			<?php } ?>
			<div class="btn-group pull-right">
				<?php if ($previousConcept != null):?>
					<a class="btn" href="<?php echo Yii::app()->homeUrl.'/concept/'.$previousConcept->id;?>"><i class="icon-chevron-left"></i> Previous</a>
				<?php else:?>
					<a class="btn disabled"><i class="icon-chevron-left"></i> Previous</a>
				<?php endif;?>
				<?php if ($nextConcept != null):?>
					<a class="btn" href="<?php echo Yii::app()->homeUrl.'/concept/'.$nextConcept->id;?>">Next <i class="icon-chevron-right"></i></a>
				<?php else:?>
					<a class="btn disabled">Next <i class="icon-chevron-right"></i></a>
				<?php endif;?>
			</div>
		<?php } ?>
	</div>
	<hr>
	<div class="concept-description-panel">
		<p><?php echo CHtml::encode($model->description);?></p>
		<?php echo $this->getTags($model->id);?>
	</div>
	<?php if (sizeof($model->resources) != 0) :?>
	<hr>
	<?php $this->renderPartial('/resource/view', array(
				'resources' => $model->resources,
		));?>
	<?php endif; ?>
</div>

<!-- Module Details -->
<?php if ($model->isModule()) {?>
	<?php if ($upNext != null) {?>
	<div class="well">
		<p class="well-title">Up Next</p>
		<p>
			<span class="content-title"><b><?php echo $upNext['title']?></b></span>
			<?php echo CHtml::link('Start',array('concept/'.$upNext['id']), array('class'=>'pull-right btn', 'style'=>'width:40px;')); ?>
		</p>
		<p>
		<?php echo Helpers::string_len($upNext['description']);?>
		</p>
  	</div><!-- /.well -->
  	<?php } ?>
	<div class="well">
		<div class="well-title">Recently Learnt<span class="pull-right" style="; font-weight: normal; font-size: 14px; color: #aaa;">You've learnt <span style="fond-size: 24px; color: #666;"><?php echo $countLearntConcepts;?></span> out of <span style="fond-size: 24px; color: #666;"><?php echo $countConcepts;?></span> concepts</span></div>
		<?php $this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$recentlyLearntConcepts,
			'itemView'=>'/concept/_item',
			'summaryText'=>'',
				'emptyText'=>'No leant concept yet.',
			'pager' => array(
				'header' => '',
				'prevPageLabel' => '&lt;&lt;',
				'nextPageLabel' => '&gt;&gt;',
			),
			'id'=>'recent-list',
		)); ?>
  	</div><!-- /.well -->
	<div class="well">
		<div class="well-title">Quizzes<span class="pull-right" style="; font-weight: normal; font-size: 14px; color: #aaa;">You've done <span style="fond-size: 24px; color: #666;"><?php echo $countquizDone;?></span> out of <span style="fond-size: 24px; color: #666;"><?php echo $countQuizzes;?></span> quizzes</span></div>
		<?php $this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$quizDone,
				'itemView'=>'/quiz/_item',
				'summaryText'=>'',
				'emptyText'=>'Not taken quiz yet.',
				'pager' => array(
					'header' => '',
					'prevPageLabel' => '&lt;&lt;',
					'nextPageLabel' => '&gt;&gt;',
				),
				'id'=>'quiz-list',
			)); ?>
  	</div><!-- /.well -->
<?php } ?>

<!-- Social Panel -->
<div class="well">
	<ul id="myTab" class="nav nav-tabs">
		<li class="active"><a id="tab-comments" href="#comments" data-toggle="tab">Comment<?php echo $model->commentCount > 1 ? 's' : '';?> (<?php echo $model->commentCount;?>)</a></li>
		<li><a id="tab-asks" href="#asks" data-toggle="tab">Q&amp;A<?php echo $model->askCount > 1 ? 's' : '';?> (<?php echo $model->askCount;?>)</a></li>
		<li><a id="tab-notes" href="#notes" data-toggle="tab">My note<?php echo $model->noteCount > 1 ? 's' : '';?> (<?php echo $model->noteCount;?>)</a></li>
		<li><a id="tab-todos" href="#todos" data-toggle="tab">My todo<?php echo $model->todoOwnedCount > 1 ? 's' : '';?> (<?php echo $model->todosOwnedUndoneCount;?>)</a></li>
	</ul>
	<div id="myTabContent" class="tab-content">
		<div class="tab-pane fade in active" id="comments" style="width:99%;">
		<?php $newConceptComment = new ConceptComment;
			$newConceptComment->concept_id = $model->id;
			$this->renderPartial('/conceptComment/_form', array('model' => $newConceptComment));?>
		<?php $this->widget('zii.widgets.CListView', array(
				'id' => 'comment-list',
				'dataProvider'=>$dataProvider_comment,
				'itemView' => '/conceptComment/_view',
				'summaryText' => '',
				'emptyText' => 'No comment yet.',
			));?>
		</div>
		<div class="tab-pane fade" id="asks" style="width:99%;">
			<?php $newAsk = new Ask;
				$newAsk->concept_id = $model->id;
				$this->renderPartial('/ask/_form', array('model' => $newAsk));?>
			<ul class="nav nav-tabs" id="ask-nav-tabs">
				<li class="active ask-filter-btn-all btn-link"><a class="btn-link">All</a></li>
				<li class="ask-filter-btn-myquestions"><a class="btn-link">My questions</a></li>
				<li class="ask-filter-btn-myanswers"><a class="btn-link">My answers</a></li>
			</ul>
			<div id="askTagBar"></div><hr>
			<?php $this->widget('zii.widgets.CListView', array(
					'id' => 'ask-list',
					'dataProvider'=>$dataProvider_ask,
					'itemView' => '/ask/_view',
					'summaryText' => '',
					'emptyText' => 'No ask yet.',
			));?>
		</div>
		<div class="tab-pane fade" id="notes" style="width:99%;">
			<?php $newNote = new Note;
				$newNote->concept_id = $model->id;
				$this->renderPartial('/note/_form', array('model' => $newNote));?>
			<ul class="nav nav-tabs" id="note-nav-tabs">
				<li class="active note-filter-btn-all"><a class="btn-link">All</a></li>
				<li class="note-filter-btn-today"><a class="btn-link">Today</a></li>
				<li class="note-filter-btn-week"><a class="btn-link">This week</a></li>
				<li class="note-filter-btn-month"><a class="btn-link">This month</a></li>
			</ul>
			<div id="noteTagBar"></div><hr>
			<?php $this->widget('zii.widgets.CListView', array(
					'id' => 'note-list',
					'dataProvider'=>$dataProvider_note,
					'itemView' => '/note/_view',
					'summaryText' => '',
					'emptyText' => 'No note yet',
				));?>
		</div>
		<div class="tab-pane fade" id="todos" style="width:99%;">
			<?php $newTodo = new Todo;
				$newTodo->concept_id = $model->id;
				$this->renderPartial('/todo/_form', array('model' => $newTodo));?>
			<ul class="nav nav-tabs" id="todo-nav-tabs">
				<li class="active todo-filter-btn-all"><a class="btn-link">All</a></li>
				<li class="todo-filter-btn-today"><a class="btn-link">Today</a></li>
				<li class="todo-filter-btn-week"><a class="btn-link">This week</a></li>
				<li class="todo-filter-btn-month"><a class="btn-link">This month</a></li>
				<li class="dropdown pull-right">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="text">On going</span> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li class="todo-filter-btn-all_status"><a class="btn-link">All status</a></li>
						<li class="todo-filter-btn-undone" style="display:none;"><a class="btn-link">On going</a></li>
						<li class="todo-filter-btn-done"><a class="btn-link">Done</a></li>
						<li class="todo-filter-btn-canceled"><a class="btn-link">Canceled</a></li>
					</ul>
				</li>
			</ul>
			<div id="todoTagBar"></div><hr>
			<?php $this->widget('zii.widgets.CListView', array(
					'id' => 'todo-list',
					'dataProvider'=>$dataProvider_todo,
					'itemView' => '/todo/_view',
					'summaryText' => '',
					'emptyText' => 'No todo yet',
				));?>
		</div>
	</div>
</div>

<!-- Tag modal -->
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

<!-- Message modal -->
<div id="message-modal" class="modal hide fade in" style="display: none;">
	<div class="modal-body" style="display: table">
		<h5 class="message-send-to"></h5>
		<?php $newMessage = new Message;
		$form = $this->beginWidget('GxActiveForm', array(
			'enableAjaxValidation' => false,
			'id' => 'message-form',
			));?>
			<?php echo $form->textArea($newMessage, 'description', array('placeholder'=>'Send a message', 'rows'=>3, 'style'=>'width: 518px;')); ?>
			<?php echo $form->hiddenField($newMessage, 'to_user_id');?>
		<?php $this->endWidget();?>
	</div>
	<div class="alert alert-success" style="display: none;">Successfully Sent!</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-primary btn-small btn-message-send disabled">Send</a>
		<a class="btn btn-cancel">Cancel</a>
	</div>
</div>

<!-- filter-forms -->
<div style="display: none;">
	<?php $form = $this->beginWidget('GxActiveForm', array(
		'method' => 'get',
		'id' => 'ask-filter-form',
	)); ?>
	<input name="of" id="of" type="hidden" value="ask"/>
	<input name="concept_id" id="concept_id" type="hidden" value="<?php echo $model->id;?>"/>
	<input name="filter_by" id="filter_by" type="hidden"/>
	<input name="tag" id="tag" type="hidden"/>
	<?php $this->endWidget(); ?>
	
	<?php $form = $this->beginWidget('GxActiveForm', array(
		'method' => 'get',
		'id' => 'note-filter-form',
	)); ?>
	<input name="of" id="of" type="hidden" value="note"/>
	<input name="concept_id" id="concept_id" type="hidden" value="<?php echo $model->id;?>"/>
	<input name="interval" id="interval" type="hidden"/>
	<input name="tag" id="tag" type="hidden"/>
	<?php $this->endWidget(); ?>
	
	<?php $form = $this->beginWidget('GxActiveForm', array(
		'method' => 'get',
		'id' => 'todo-filter-form',
	)); ?>
	<input name="of" id="of" type="hidden" value="todo"/>
	<input name="concept_id" id="concept_id" type="hidden" value="<?php echo $model->id;?>"/>
	<input name="status" id="status" type="hidden" value="<?php echo Todo::STATUS_UNDONE;?>"/>
	<input name="interval" id="interval" type="hidden"/>
	<input name="tag" id="tag" type="hidden"/>
	<?php $this->endWidget(); ?>
</div>

<?php Yii::app()->clientScript->registerScript('concept-view-js', "
// init tagBar
	$.ajax({
		type: 'GET',
		url: '".$this->createUrl('initTagBarsAjax')."/".$model->id."',
		success: function(tagBars) {
			$('#askTagBar').html(tagBars.askTagBar);
			$('#noteTagBar').html(tagBars.noteTagBar);
			$('#todoTagBar').html(tagBars.todoTagBar);
			$('.concept-tag').popover();
		}
	});
		
//******************************************
//************** conceptComment
	
	$('#ConceptComment_description').keyup(function(event) {
		if ($('#ConceptComment_description').val() != '')
			$('#concept-comment-form .btn-create').removeClass('disabled')
		else
			$('#concept-comment-form .btn-create').addClass('disabled')
	});

	$('#concept-comment-form .btn-create').live('click', function(){
		if ($(this).hasClass('disabled')) {
			$('#ConceptComment_description').focus();
			return;
		}
		
		\$this=$(this);
		\$form = \$this.parent();
	
		if ($('#ConceptComment_description').val() == '') {
			$('#ConceptComment_description').parent().addClass('error');
			$('#ConceptComment_description').focus();
			$('#ConceptComment_description').attr('placeholder','Please input a description!');
			return false;
		}

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/conceptComment/create')."',
			data: \$form.serialize(),
			success: function (html) {
				var tmp = $('#tab-comments').text();
				var t = tmp.split('(');
				tmp = t[1];
				var sum = parseInt(tmp.substr(0, tmp.length-1)) + 1;
				var str = 'Comment';
				if (sum > 1)
					str += 's (' + sum + ')';
				else
					str += ' (' + sum + ')';
				$('#tab-comments').text(str);

				$('#concept-comment-form .btn-create').addClass('disabled');

				setTimeout(function() {
					$('#concept-comment-form textarea').val('');
					$('#ConceptComment_description').attr('placeholder','Comment');
					$.fn.yiiListView.update('comment-list', {
							data: $(this).serialize()
					});
                }, 400);
			}
		});
		return false;
	});
		
	$('#comment-list .delete').live('click', function() {
		var elem = $(this).closest('.post');
		\$this=$(this);
		bootbox.confirm('Delete this comment?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('conceptcomment/delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						var tmp = $('#tab-comments').text();
						var t = tmp.split('(');
						tmp = t[1];
						var sum = parseInt(tmp.substr(0, tmp.length-1)) - 1;
						var str = 'Comment';
						if (sum > 1)
							str += 's (' + sum + ')';
						else
							str += ' (' + sum + ')';
						$('#tab-comments').text(str);
						setTimeout(function() {
							elem.slideUp();
						}, 500);
					}
				});
				return false;
			}
		});
	});
		
//******************************************
//************** comment ask, note, todo
		
	var homeUrl = '".Yii::app()->homeUrl."';
		
	function titleClick() {
		var title_ori=$(this).html();
		\$this = $(this);
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><input id=\"title\" type=\"text\" style=\"width: 100%;\"></div>');
		$(this).find('#title').val(htmlDecode(title_ori));
		$(this).find('#title').focus();
		$(this).find('#title').focusout(function(){
			var title = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(title));
			$('.content-title').live('click', titleClick);
			if (title_ori != htmlEncode(title)) {
				var url = homeUrl + '/';
				if (\$this.closest('.list-view').attr('id') == 'ask-list')
					url += 'ask';
				else if (\$this.closest('.list-view').attr('id') == 'note-list')
					url += 'note';
				else if (\$this.closest('.list-view').attr('id') == 'todo-list')
					url += 'todo';
				else return false;
				url += '/updateAjax';
				$.ajax({
					type: 'POST',
					url: url,
					data: {id: id, title: title}
				});
			}
		});
		
		$('.content-title').die('click');
	}
	
	function descriptionClick() {
		var description_ori=$(this).html();
		\$this = $(this);
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><textarea rows=\"4\" id=\"description\" style=\"width: 100%;\"></textarea></div>');
		$(this).find('#description').text(htmlDecode(description_ori));
		$(this).find('#description').focus();
		$(this).find('#description').focusout(function(){
			var description = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(description));
			$('.content-description').live('click', descriptionClick);
			if (description_ori != htmlEncode(description)) {
				var url = homeUrl + '/';
				if (\$this.closest('.list-view').attr('id') == 'ask-list')
					url += 'ask';
				else if (\$this.closest('.list-view').attr('id') == 'note-list')
					url += 'note';
				else if (\$this.closest('.list-view').attr('id') == 'todo-list')
					url += 'todo';
				else return false;
				url += '/updateAjax';
				$.ajax({
					type: 'POST',
					url: url,
					data: {id: id, description: description}
				});
			}
		});
		
		$('.content-description').die('click');
	}
	
	$('.content-title').live('click',titleClick);
		
	$('.content-description').live('click', descriptionClick);
		
	function tagModal() {
		\$this = $(this);
		var id = $(this).closest('.post').find('#data_id').val();
		var url = homeUrl + '/';
		if (\$this.closest('.list-view').attr('id') == 'ask-list')
			url += 'ask';
		else if (\$this.closest('.list-view').attr('id') == 'note-list')
			url += 'note';
		else if (\$this.closest('.list-view').attr('id') == 'todo-list')
			url += 'todo';
		else return false;
		url += '/';
		$.ajax({
			type: 'POST',
			url: url + 'createTagCanvas',
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
						url: url + 'updateAjax',
						data: {id: id, tags: tags},
						success: function (html) {
							$('#tag-canvas').find('.alert-success').show();
							if (tags != '') {
								$.ajax({
									type: 'GET',
									url: url + 'getTags',
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
		
//******************************************
//************** ask
	
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
			url: '".$this->createUrl('/ask/create')."',
			data: \$form.serialize(),
			success: function (html) {
				var tmp = $('#tab-asks').text();
				var t = tmp.split('(');
				tmp = t[1];
				var sum = parseInt(tmp.substr(0, tmp.length-1)) + 1;
				var str = 'Q&A';
				if (sum > 1)
					str += 's (' + sum + ')';
				else
					str += ' (' + sum + ')';
				$('#tab-asks').text(str);
		
				$('#ask-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('ask-list', {
						data: $(this).serialize()
					});
					$('#ask-form').find('textarea').val('');
					$('#ask-form').find('input').val('');
					$('#Ask_title').attr('placeholder','Ask a question');
					$('#ask-form .btn-create').addClass('disabled');
                }, 400);
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateAskTagBar')."',
					data: $('#ask-filter-form').serialize(),
					success: function (askTagBar) {
						$('#askTagBar').html(askTagBar);
					}
				});
			}
		});
		return false;
	
	});
	
	$('#ask-form .btn-cancel').click(function (){
		$('#ask-form .form-rest').slideUp();
		$('#ask-form').find('textarea').val('');
		$('#ask-form').find('input').val('');
		$('#ask-form').find('#Ask_title').attr('placeholder','Ask a question');
	});
	
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
					url: '".$this->createUrl('ask/delete')."/'.concat(\$this.closest('.post').find('#data_id').val()),
					success: function(html) {
						var tmp = $('#tab-asks').text();
						var t = tmp.split('(');
						tmp = t[1];
						var sum = parseInt(tmp.substr(0, tmp.length-1)) - 1;
						var str = 'Q&A';
						if (sum > 1)
							str += 's (' + sum + ')';
						else
							str += ' (' + sum + ')';
						$('#tab-asks').text(str);
		
						$.ajax({
							type: 'POST',
							url: '".$this->createUrl('updateAskTagBar')."',
							data: $('#ask-filter-form').serialize(),
							success: function (askTagBar) {
								$('#askTagBar').html(askTagBar);
							}
						});
					}
				});
				elem_post.slideUp();
				elem_comment.slideUp();
				return false;
			}
		});
	});
		
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
	
	// answer
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
				\$form.closest('.post-comment').find('#answer-list').children('.items').prepend('<div class=\"comment-item clearfix\" style=\"display: none;\"><div class=\"user-avatar\"><img style=\"width:48px; height:48px\" class=\"img-polaroid\" src=\"".Yii::app()->baseUrl."/uploads/images/profile-avatar/".Yii::app()->user->id."\"/></div><div class=\"content\"><span class=\"user-name\">admin</span>:<span class=\"pull-right owner\" style=\"display:none;\"><input type=\"hidden\" id=\"answer_id\" value=\"'.concat(data).concat('\"><span class=\"btn-link btn-edit\">edit</span><span style=\"color:grey;\">&nbsp;/&nbsp;</span><span class=\"btn-link btn-delete\">delete</span></span><br><span class=\"description\">').concat(newAnswerDescription).concat('</span></div></div>'));
				\$form.closest('.post-comment').find('#answer-list div:first-child').slideDown();
			}
		});
		
	});
	
	$('.ask-filter-btn-all').click(function(){
		$(this).addClass('active');
		$('.ask-filter-btn-myquestions').removeClass('active');
		$('.ask-filter-btn-myanswers').removeClass('active');
		
		$('#ask-filter-form #filter_by').val('');
		$('#ask-filter-form').submit();
	});
		
	$('.ask-filter-btn-myquestions').click(function(){
		$(this).addClass('active');
		$('.ask-filter-btn-all').removeClass('active');
		$('.ask-filter-btn-myanswers').removeClass('active');
		
		$('#ask-filter-form #filter_by').val('myquestions');
		$('#ask-filter-form').submit();
	});
		
	$('.ask-filter-btn-myanswers').click(function(){
		$(this).addClass('active');
		$('.ask-filter-btn-myquestions').removeClass('active');
		$('.ask-filter-btn-all').removeClass('active');
		
		$('#ask-filter-form #filter_by').val('myanswers');
		$('#ask-filter-form').submit();
	});

	$('#ask-filter-form').submit(function(){
	    $.fn.yiiListView.update('ask-list', { 
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateAskTagBar')."',
			data: $('#ask-filter-form').serialize(),
			success: function (askTagBar) {
				$('#askTagBar').html(askTagBar);
			}
		});
	    return false;
	});
		
	$('#askTagBar .tag, #ask-list .tag').live('click', function(){
		var tag = htmlEncode($(this).text());
		if (tag.lastIndexOf('(') != -1)
			tag = $(this).text().substr(0, tag.lastIndexOf('('));
		if ($(this).attr('id') == 'all-tag')
			tag = '';
		$('#ask-filter-form #tag').val(tag);
		$('#ask-filter-form').submit();
		$('#askTagBar > span').removeClass('label-info');
		$('#askTagBar > span').removeClass('selected');
		$('#askTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('label-info');
		$('#askTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('selected');
		
		if (tag=='') {
			$(this).addClass('label-info');
			$(this).addClass('selected');
		}
	});
		
//******************************************
//************** note
		
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
			url: '".$this->createUrl('/note/create')."',
			data: \$form.serialize(),
			success: function (html) {
				var tmp = $('#tab-notes').text();
				var t = tmp.split('(');
				tmp = t[1];
				var sum = parseInt(tmp.substr(0, tmp.length-1)) + 1;
				var str = 'My note';
				if (sum > 1)
					str += 's (' + sum + ')';
				else
					str += ' (' + sum + ')';
				$('#tab-notes').text(str);
		
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
		
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateNoteTagBar')."',
					data: $('#note-filter-form').serialize(),
					success: function (noteTagBar) {
						$('#noteTagBar').html(noteTagBar);
					}
				});
			}
		});
		return false;
	});
	
	$('#note-form .btn-cancel').click(function (){
		$('#note-form .form-rest').slideUp();
		$('#note-form').find('textarea').val('');
		$('#note-form').find('input').val('');
		$('#note-form').find('#Note_title').attr('placeholder','Create a note');
	});
	
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
					url: '".$this->createUrl('note/delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						var tmp = $('#tab-notes').text();
						var t = tmp.split('(');
						tmp = t[1];
						var sum = parseInt(tmp.substr(0, tmp.length-1)) - 1;
						var str = 'My note';
						if (sum > 1)
							str += 's (' + sum + ')';
						else
							str += ' (' + sum + ')';
						$('#tab-notes').text(str);
						setTimeout(function() {
							elem.slideUp();
						}, 500);
		
						$.ajax({
							type: 'POST',
							url: '".$this->createUrl('updateNoteTagBar')."',
							data: $('#note-filter-form').serialize(),
							success: function (noteTagBar) {
								$('#noteTagBar').html(noteTagBar);
							}
						});
					}
				});
				return false;
			}
		});
	});

	$('.note-filter-btn-all').click(function(){
		$(this).addClass('active');
		$('.note-filter-btn-today').removeClass('active');
		$('.note-filter-btn-week').removeClass('active');
		$('.note-filter-btn-month').removeClass('active');
		
		$('#note-filter-form #interval').val('');
		$('#note-filter-form').submit();
	});
	
	$('.note-filter-btn-today').click(function(){
		$(this).addClass('active');
		$('.note-filter-btn-all').removeClass('active');
		$('.note-filter-btn-week').removeClass('active');
		$('.note-filter-btn-month').removeClass('active');
		
		$('#note-filter-form #interval').val('today');
		$('#note-filter-form').submit();
	});
	
	$('.note-filter-btn-week').click(function(){
		$(this).addClass('active');
		$('.note-filter-btn-all').removeClass('active');
		$('.note-filter-btn-today').removeClass('active');
		$('.note-filter-btn-month').removeClass('active');
		
		$('#note-filter-form #interval').val('week');
		$('#note-filter-form').submit();
	});
	
	$('.note-filter-btn-month').click(function(){
		$(this).addClass('active');
		$('.note-filter-btn-all').removeClass('active');
		$('.note-filter-btn-today').removeClass('active');
		$('.note-filter-btn-week').removeClass('active');
		
		$('#note-filter-form #interval').val('month');
		$('#note-filter-form').submit();
	});

	$('#note-filter-form').submit(function(){
	    $.fn.yiiListView.update('note-list', { 
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateNoteTagBar')."',
			data: $('#note-filter-form').serialize(),
			success: function (noteTagBar) {
				$('#noteTagBar').html(noteTagBar);
			}
		});
	    return false;
	});
		
	$('#noteTagBar .tag, #note-list .tag').live('click', function(){
		var tag = htmlEncode($(this).text());
		if (tag.lastIndexOf('(') != -1)
			tag = $(this).text().substr(0, tag.lastIndexOf('('));
		if ($(this).attr('id') == 'all-tag')
			tag = '';
		$('#note-filter-form #tag').val(tag);
		$('#note-filter-form').submit();
		$('#noteTagBar > span').removeClass('label-info');
		$('#noteTagBar > span').removeClass('selected');
		$('#noteTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('label-info');
		$('#noteTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('selected');
		
		if (tag=='') {
			$(this).addClass('label-info');
			$(this).addClass('selected');
		}
	});
		
//******************************************
//************** todo		
	
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
			url: '".$this->createUrl('/todo/create')."',
			data: \$form.serialize(),
			success: function (html) {
				var tmp = $('#tab-todos').text();
				var t = tmp.split('(');
				tmp = t[1];
				var sum = parseInt(tmp.substr(0, tmp.length-1)) + 1;
				var str = 'My todo';
				if (sum > 1)
					str += 's (' + sum + ')';
				else
					str += ' (' + sum + ')';
				$('#tab-todos').text(str);
		
				$('#todo-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('todo-list', {
						data: $(this).serialize()
					});
					$('#todo-form').find('textarea').val('');
					$('#todo-form').find('input').val('');
					$('#Todo_title').attr('placeholder','Create a todo');
					$('#todo-form .btn-create').addClass('disabled');
                }, 400);
		
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateTodoTagBar')."',
					data: $('#todo-filter-form').serialize(),
					success: function (todoTagBar) {
						$('#todoTagBar').html(todoTagBar);
					}
				});
			}
		});
		return false;
	
	});	
	
	$('#todo-form .btn-cancel').click(function (){
		$('#todo-form .form-rest').slideUp();
		$('#todo-form').find('textarea').val('');
		$('#todo-form').find('input').val('');
		$('#todo-form').find('#Todo_title').attr('placeholder','Create a todo');
	});
	
	$('#todo-list .post').live('mouseenter', function (){
		$(this).children('.item-description').find('.social-bar').fadeIn('fast');
	});
	
	$('#todo-list .post').live('mouseleave', function (){
		$(this).children('.item-description').find('.social-bar').fadeOut('fast');
	});
	
	$('#todo-list .delete').live('click', function() {
		var elem = $(this).closest('.post');
		\$this=$(this);
		bootbox.confirm('Delete this todo?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('todo/delete')."/'.concat(\$this.closest('.post').find('#data_id').val()),
					success: function(data) {
						var tmp = $('#tab-todos').text();
						var t = tmp.split('(');
						tmp = t[1];
						var sum = parseInt(tmp.substr(0, tmp.length-1)) - 1;
						var str = 'My todo';
						if (sum > 1)
							str += 's (' + sum + ')';
						else
							str += ' (' + sum + ')';
						$('#tab-todos').text(str);
		
						setTimeout(function() {
							elem.slideUp();
						}, 500);
		
						$.ajax({
							type: 'POST',
							url: '".$this->createUrl('updateTodoTagBar')."',
							data: $('#todo-filter-form').serialize(),
							success: function (todoTagBar) {
								$('#todoTagBar').html(todoTagBar);
							}
						});
					}
				});
				return false;
			}
		});
	});

	$('#todo-filter-form').submit(function(){
	    $.fn.yiiListView.update('todo-list', { 
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateTodoTagBar')."',
			data: $('#todo-filter-form').serialize(),
			success: function (todoTagBar) {
				$('#todoTagBar').html(todoTagBar);
			}
		});
	    return false;
	});

	$('.todo-filter-btn-all').click(function(){
		$(this).addClass('active');
		$('.todo-filter-btn-today').removeClass('active');
		$('.todo-filter-btn-week').removeClass('active');
		$('.todo-filter-btn-month').removeClass('active');
		
		$('#todo-filter-form #interval').val('');
		$('#todo-filter-form').submit();
	});
	
	$('.todo-filter-btn-today').click(function(){
		$(this).addClass('active');
		$('.todo-filter-btn-all').removeClass('active');
		$('.todo-filter-btn-week').removeClass('active');
		$('.todo-filter-btn-month').removeClass('active');
		
		$('#todo-filter-form #interval').val('today');
		$('#todo-filter-form').submit();
	});
	
	$('.todo-filter-btn-week').click(function(){
		$(this).addClass('active');
		$('.todo-filter-btn-all').removeClass('active');
		$('.todo-filter-btn-today').removeClass('active');
		$('.todo-filter-btn-month').removeClass('active');
		
		$('#todo-filter-form #interval').val('week');
		$('#todo-filter-form').submit();
	});
	
	$('.todo-filter-btn-month').click(function(){
		$(this).addClass('active');
		$('.todo-filter-btn-all').removeClass('active');
		$('.todo-filter-btn-today').removeClass('active');
		$('.todo-filter-btn-week').removeClass('active');
		
		$('#todo-filter-form #interval').val('month');
		$('#todo-filter-form').submit();
	});
		
	$('.todo-filter-btn-all_status').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('All status');
		$(this).parent().children('.todo-filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-done').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#todo-filter-form #status').val('');
		$('#todo-filter-form').submit();
	});
	
	$('.todo-filter-btn-undone').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('On going');
		$(this).parent().children('.todo-filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-done').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#todo-filter-form #status').val('".Todo::STATUS_UNDONE."');
		$('#todo-filter-form').submit();
	
	});
	
	$('.todo-filter-btn-done').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('Done');
		$(this).parent().children('.todo-filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#todo-filter-form #status').val('".Todo::STATUS_DONE."');
		$('#todo-filter-form').submit();
	});
	
	$('.todo-filter-btn-canceled').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('Canceled');
		$(this).parent().children('.todo-filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.todo-filter-btn-done').attr('style', 'display:inline;');
	
		$('#todo-filter-form #status').val('".Todo::STATUS_CANCELED."');
		$('#todo-filter-form').submit();
	});
	
	$('#todoTagBar .tag, #todo-list .tag').live('click', function(){
		var tag = htmlEncode($(this).text());
		if (tag.lastIndexOf('(') != -1)
			tag = $(this).text().substr(0, tag.lastIndexOf('('));
		if ($(this).attr('id') == 'all-tag')
			tag = '';
		$('#todo-filter-form #tag').val(tag);
		$('#todo-filter-form').submit();
		$('#todoTagBar > span').removeClass('label-info');
		$('#todoTagBar > span').removeClass('selected');
		$('#todoTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('label-info');
		$('#todoTagBar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('selected');
		
		if (tag=='') {
			$(this).addClass('label-info');
			$(this).addClass('selected');
		}
	});
			
	$('.item-reactivate-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/todo/updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_UNDONE.",
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-done-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/todo/updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_DONE.",
					done_at: '".date('Y-m-d H:i:s', time())."'
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-redo-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/todo/updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_UNDONE.",
					done_at: ''
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-cancel-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('/todo/updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_CANCELED.",
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.date-time').live('click', timeClick);
		
	function timeClick() {
		$('.date-time').die('click');
		var dtStrArr = $(this).html().split(' ');
		var startTime = dtStrArr[1]+' '+dtStrArr[2];
		var endTime = dtStrArr[5]+' '+dtStrArr[6];
		//if (date('H', time()) == 23 && date('i', time()) >= 30)
		var startDate = dtStrArr[0];
		var endDate = dtStrArr[4];
		
		if (startDate == 'Today')
			startDate = '".date("d-m-Y")."';
		else if (startDate == 'Tomorrow')
			startDate = '".date('d-m-Y',mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."';
		else
			startDate = startDate.substr(3,2)+'-'+startDate.substr(0,2)+'-'+'".date("Y")."'
		
		if (endDate == 'Today')
			endDate = '".date("d-m-Y")."';
		else if (endDate == 'Tomorrow')
			endDate = '".date('d-m-Y',mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."';
		else
			endDate = endDate.substr(3,2)+'-'+endDate.substr(0,2)+'-'+'".date("Y")."';
		
		var str_ori = $(this).html();
		var str = \"<br><input class='start_at_time' name='start_at_time' id='start_at_time' type='text' style='width:75px;'><i class='icon-time' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='start_at_date' name='start_at_date' id='start_at_date' type='text' style='width:90px;' data-date-format='dd-mm-yyyy'><i class='icon-calendar' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='icon-minus' style='margin: -2px 0 0 0; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='end_at_time' name='end_at_time' id='end_at_time' type='text' style='width:75px;'/><i class='icon-time' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='end_at_date' name='end_at_date' id='end_at_date' type='text' style='width:90px;' data-date-format='dd-mm-yyyy'><i class='icon-calendar' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i><a class='btn btn-link' id='btn-confirm' style='margin: -8px 0 0 0;'>Confirm</a><a class='btn btn-link' id='btn-cancel' style='margin: -8px 0 0 -20px;'>Cancel</a>\";
		$(this).html(str);
		$(this).children('#start_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true,
			defaultTime: startTime
		});
		$(this).children('#end_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true,
			defaultTime: endTime
		});
		$(this).children('#start_at_date').val(startDate);
		$(this).children('#end_at_date').val(endDate);
		
		$(this).children('#start_at_date').datepicker();
		$(this).children('#end_at_date').datepicker();
		
		$(this).children('#btn-confirm').click(function(){
			var newStartTime = $(this).parent().children('#start_at_time').val();
			var newEndTime = $(this).parent().children('#end_at_time').val();
			var newStartDate = $(this).parent().children('#start_at_date').val();
			var newEndDate = $(this).parent().children('#end_at_date').val();
			if (newStartTime != startTime || newEndTime != endTime || newStartDate != startDate || newEndDate != endDate) {
				var id = $(this).closest('.post').find('#data_id').val();
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('todo/updateAjax')."',
					data: {id: id,
						startTime: newStartTime,
						startDate: newStartDate,
						endTime: newEndTime,
						endDate: newEndDate
					}
				});
			}
			var str = newStartDate.substr(3,2)+'/'+newStartDate.substr(0,2)+' '+newStartTime+' - '+newEndDate.substr(3,2)+'/'+newEndDate.substr(0,2)+' '+newEndTime;
			$(this).parent().html(str);
			$('.date-time').live('click', timeClick);
		})
		
		$(this).children('#btn-cancel').click(function(){
			$(this).parent().html(str_ori);
			$('.date-time').live('click', timeClick);
		})
	}

//********* left menu: concepts/modules-recommended(related)

	var isModule = ".($model->isModule() ? '1':'0').";
		
	if (isModule == 1) {
		$('#concept-related-content').closest('.left-main-menu').find('.nav-header').text('Recommended modules');
		// ajax -> fetch recommendate modules......
	} else {
		$.ajax({
			data: {concept_id: ".$model->id."},
			type: 'post',
			url: '".$this->createUrl('concept/fetchConceptsRelated')."',
			success: function(html) {
				$('#concept-related-content').html(html);
				$('[rel=tooltip]').tooltip();
			},
		});
	}
//********* left menu: user-ranking
	// init
	$.ajax({
		data: {rank_by: 'answers', concept_id: ".$model->id."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchUsers')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
		
	$.ajax({
		data: {concept_id: ".$model->id."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchUsersLearning')."',
		success: function(html) {
			$('#user-fiter-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
	
	// refresh
	setInterval(function() {
		var rank_by = $('.user-rank-order-by').text() == 'Answers' ? 'answers' : 'questions';
		$.ajax({
			data: {rank_by: rank_by, concept_id: ".$model->id."},
			type: 'post',
			url: '".$this->createUrl('concept/fetchUsers')."',
			success: function(html) {
				$('#user-ranking-content').html(html);
				//$('[rel=tooltip]').tooltip();
			},
		});
		
		var url = $('.user-filter-by').text() == 'Learning' ? '".$this->createUrl('concept/fetchUsersLearning')."' : '".$this->createUrl('concept/fetchUsersLearnt')."';
		$.ajax({
			data: {concept_id: ".$model->id."},
			type: 'post',
			url: url,
			success: function(html) {
				$('#user-fiter-content').html(html);
				//$('[rel=tooltip]').tooltip();
			},
		});
	},30000);
		
	// change rank order by
	$('.user-rank-order-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Questions') {
			$('.user-rank-order-by').text('Questions');
			$(this).text('Answers');
			$.ajax({
				data: {rank_by: 'questions', concept_id: ".$model->id."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsers')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('.user-rank-order-by').text('Answers');
			$(this).text('Questions');
		
			$.ajax({
				data: {rank_by: 'answers', concept_id: ".$model->id."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsers')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		}
	});
		
	// change filter by learnt or learning
	$('.user-filter-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('.user-filter-by').text('Learning');
			$(this).text('Learnt');
			$.ajax({
				data: {concept_id: ".$model->id."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersLearning')."',
				success: function(html) {
					$('#user-fiter-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('.user-filter-by').text('Learnt');
			$(this).text('Learning');
		
			$.ajax({
				data: {concept_id: ".$model->id."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersLearnt')."',
				success: function(html) {
					$('#user-fiter-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		}
	});
		
	// change bg-color
	$('.user-rank-item').live('mouseenter', function() {
		$(this).css('background-color', '#edf3f8');
		$(this).css('cursor', 'pointer');
	});
		
	$('.user-rank-item').live('mouseleave', function() {
		$(this).css('background-color', '');
		$(this).css('cursor', 'default');
	});
		
	// popup modal
	$('.user-rank-item').live('click', function () {
		$('#message-form #Message_to_user_id').val($(this).find('#data_id').val());
		$('#message-modal .message-send-to').text('Send message to: '+ $(this).find('.name-user').html());
	});
	
	$('#Message_description').keyup(function() {
		if ($('#Message_description').val() != '')
			$('.btn-message-send').removeClass('disabled')
		else
			$('.btn-message-send').addClass('disabled')
	});
		
	$('.btn-message-send').click(function(){
		if($(this).hasClass('disabled'))
			return;
		
		$('.btn-message-send').addClass('disabled');
		$('.btn-message-send').text('Sending...');
		
		var form = $('#message-form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('message/create')."',
			data: form.serialize(),
			success: function (html) {
				$('#message-modal .alert-success').show();
				setTimeout(function() {
					$('#message-modal').modal('hide');
					$('#message-form').find('#Message_description').val('');
					$('#message-form').find('#Message_to-user-id').val('');
					$('#message-form').find('#Message_description').attr('placeholder','Send a message');
					$('#message-modal .alert-success').hide();
					$('.btn-message-send').text('Send');
                }, 400);
			}
		});
		return false;
	});
	
	$('#message-modal .btn-cancel').click(function (){
		$('#message-modal').modal('hide');
		$('.btn-message-send').addClass('disabled')
		$('.btn-message-send').text('Send');
		$('#message-form').find('textarea').val('');
		$('#message-form').find('#Message_description').attr('placeholder','Send a message');
	});
		
");

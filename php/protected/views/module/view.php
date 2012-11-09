<div class="well top-panel-fix">
	<div class="module-structure-panel">
		<span class="module-title"><?php echo $model->title;?></span>
		<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
	</div>
	<div class="module-structure-tree" style="display: none;">
		<table class="tree-table"><?php 
		foreach ($concepts as $concept) {
			if ($concept->id == $concept->root)
				continue;
			
			echo "<tr><td class='title' style='padding-left: ".(($concept->level-1)*20)."px;'>".($concept->level==2?"<b>":"").CHtml::encode($concept->title).($concept->level==2?"</b>":"")."</td>"
				."<td class='legend'>legend</td><td class='action'><a href='".Yii::app()->homeUrl."/concept/".$concept->id."'>Get In</a></td></tr>";
		}?></table>
	</div>
</div>
<div class="row-fluid">
	<div class="span8">
	  	<?php if ($upNext != null) {?>
		<div class="well">
			<p class="well-title">Up Next</p>
			<p>
				<span class="content-title"><?php echo $upNext['title']?></span>
				<?php echo CHtml::link('Start',array('concept/'.$upNext['id']), array('class'=>'pull-right btn btn-small', 'style'=>'width:40px;')); ?>
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
	</div>
	
	<div class="span4">
	  	<div class="well">
	  		<div id="note_item_create" class="modal hide fade in" style="display: none;" data-backdrop="static">
				<div class="modal-header">
					<h4>Create a note</h4>
				</div>
				<div class="modal-body">
					<?php
					$newNote = new Note;
					$form = $this->beginWidget('GxActiveForm', array(
						'id' => 'note-form',
						'enableAjaxValidation' => false,
					));
					echo $form->textArea($newNote, 'title', array('class'=>'form-title', 'placeholder'=>'Title', 'rows'=>1));?>
					<label class="error" for="Note[title]" id="title_error" style="color:red;">This field is required.</label>
					<?php echo $form->textArea($newNote, 'description', array('placeholder'=>'Description', 'rows'=>4));?>
					<label class="error" for="Note[description]" id="description_error" style="color:red;">This field is required.</label>
					<?php echo $form->dropDownList($newNote, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true)));?>
					<?php $this->endWidget(); ?>
					<div style="color:green; display:none;" class="success">success</div>
				</div>
				<div class="modal-footer">
					<a class="btn pull-left note-form-create">Create</a>
					<a class="btn pull-left" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn" data-dismiss="modal">Close</a>
				</div>
			</div>
			<div class="well-title">Notes<a data-toggle="modal" class="icon-edit pull-right" rel="tooltip" data-placement="left" title="Create a note" href="#note_item_create"></a></div>
			<?php $this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$notes,
				'itemView'=>'/note/_item',
				'summaryText'=>'',
				'emptyText'=>'No note yet.',
				'pager' => array(
					'header' => '',
					'prevPageLabel' => '&lt;&lt;',
					'nextPageLabel' => '&gt;&gt;',
				),
				'id'=>'note-list',
			)); ?>
	  	</div><!-- /.well -->
	  	<div class="well">
	  		<div id="ask_item_create" class="modal hide fade in" style="display: none;" data-backdrop="static">
	  			<div class="modal-header">
					<h4>Ask a question</h4>
				</div>
				<div class="modal-body">
					<?php
					$newAsk = new Ask;
					$form = $this->beginWidget('GxActiveForm', array(
						'id' => 'ask-form',
						'enableAjaxValidation' => false,
					));
					echo $form->textArea($newAsk, 'title', array('class'=>'form-title', 'placeholder'=>'Title', 'rows'=>1));?>
					<label class="error" for="Ask[title]" id="title_error" style="color:red;">This field is required.</label>
					<?php echo $form->textArea($newAsk, 'description', array('placeholder'=>'Description', 'rows'=>4));?>
					<label class="error" for="Ask[description]" id="description_error" style="color:red;">This field is required.</label>
					<?php echo $form->dropDownList($newAsk, 'concept_id', GxHtml::listDataEx(Concept::model()->findAllAttributes(null, true)));?>
					<?php $this->endWidget(); ?>
					<div style="color:green; display:none;" class="success">success</div>
				</div>
				<div class="modal-footer">
					<a class="btn pull-left note-form-create">Create</a>
					<a class="btn pull-left" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn" data-dismiss="modal">Close</a>
				</div>
			</div>
			<div class="well-title">Q&amp;A<a data-toggle="modal" class="icon-comment pull-right" rel="tooltip" data-placement="left" title="Ask a question" href="#ask_item_create"></a></div>
			<?php $this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$asks,
				'itemView'=>'/ask/_item',
				'summaryText'=>'',
				'emptyText'=>'No Q&A yet.',
				'pager' => array(
					'header' => '',
					'prevPageLabel' => '&lt;&lt;',
					'nextPageLabel' => '&gt;&gt;',
				),
				'id'=>'ask-list',
			)); ?>
	  	</div><!-- /.well -->
	</div><!--/.span-->		
</div><!--/.row-->
<?php 
Yii::app()->clientScript->registerScript('module-view-js', "
	$(document).ready(function() {
		$('[rel=tooltip]').tooltip();
		
		$('.error').hide();
		$('input[type=text], textarea').css({backgroundColor:'#fff'});
		$('input[type=text], textarea').focus(function(){
			$(this).css({backgroundColor: '#ffddaa'});
		});
		$('input[type=text], textarea').blur(function(){
			$(this).css({backgroundColor:'#fff'});
		});
		$('.note-form-create').live('click', function(){
			\$this=$(this);
			\$form = \$this.parent().prev().children('form');
			$('.error').hide();
			
			if (\$form.children('#Note_title').val() == '') {
				\$form.children('#title_error').show();
				\$form.children('#Note_title').focus();
				return false;
			}
		
			if (\$form.children('#Note_description').val() == '') {
				\$form.children('#description_error').show();
				\$form.children('#Note_description').focus();
				return false;
			}
			
			$.ajax({
				type: 'POST',
				url: '".$this->createUrl('/note/create')."',
				data: \$this.parent().prev().children('form').serialize(),
				success: function (html) {
					\$this.parent().prev().children('.success').show();
					
					setTimeout(function() {
						$('.modal.in').modal('hide');
						\$this.parent().prev().children('.success').hide();
						$('input[type=text], textarea').val('');
						$.fn.yiiListView.update('note-list', {
								data: $(this).serialize()
						});
		
					}, 800);
		
				}
			});
			return false;
		
		});
		
		$('.note-update-btn').live('click', function() {
			\$this=$(this);
			$.ajax({
				type: 'GET',
				url: '".$this->createUrl('/note/getNote')."',
				data: {id: \$this.parent().find('#note_id').val()},
				success: function(note) {
					var str = \"<form>\"
						+ \"Title: <textarea class='form-title' placeholder='Title' rows='1' name='title' id='Note_title'>\"+note.title+\"</textarea>\"
						+ \"<label class='error' for='title' id='title_error' style='color:red;display:none;'>This field is required.</label>\"
						+ \"Description: <textarea placeholder='Description' rows='4' name='description' id='Note_description'>\"+note.description+\"</textarea>\"
						+ \"<label class='error' for='Note[description]' id='description_error' style='color:red;display:none;'>This field is required.</label>\"
						+ \"<input type='hidden' name='id' value='\"+note.id+\"'>\"
						+ \"</form>\";	
					\$this.parent().prev().html(str);
					\$this.hide();
					\$this.parent().find('.note-delete-btn').hide();
					\$this.parent().find('.note-form-confirm').show();
					\$this.parent().find('.note-form-cancel').show();
				}
			});
		});
		
		$('.note-form-confirm').live('click', function(){
			\$this=$(this);
			\$form = \$this.parent().prev().children('form');
			$('.error').hide();
			
			if (\$form.children('#Note_title').val() == '') {
				\$form.children('#title_error').show();
				\$form.children('#Note_title').focus();
				return false;
			}
		
			if (\$form.children('#Note_description').val() == '') {
				\$form.children('#description_error').show();
				\$form.children('#Note_description').focus();
				return false;
			}
		
			$.ajax({
				type: 'POST',
				url: '".$this->createUrl('/note/updateAjax')."',
				data: \$this.parent().prev().children('form').serialize(),
				success: function (note) {
					var str = \"<p>\"
						+ \"<span class='content-title'><b>\"+note.title+\"</b></span><br>\"
						+ \"<span class='date-time'>\"+note.create_at+\"</span>\"
						+ \"</p>\"
						+ \"<p>\"+note.description+\"</p>\";
		
					\$this.parent().prev().html(str);
					\$this.next().hide();
					
					setTimeout(function() {
						$('.modal.in').modal('hide');
		
						$.fn.yiiListView.update('note-list', {
								data: $(this).serialize()
						});
					}, 800);
		
				}
			});
		});
		
		$('.note-form-cancel').live('click', function() {
			\$this = $(this);
			$.ajax({
				type: 'GET',
				url: '".$this->createUrl('/note/getNote')."',
				data: {id: \$this.parent().find('#note_id').val()},
				success: function(note) {
					var str = \"<p><span class='content-title'><b>\"+note.title+\"</b></span><br><span class='date-time'>\"+note.create_at+\"</span></p><p>\"+note.description+\"</p>\";
					\$this.parent().prev().html(str);
					\$this.prev().hide();
					\$this.prev().prev().show();
					\$this.prev().prev().prev().show();
					\$this.hide();
				}
			});
		});
		
		$('.note-delete-btn').live('click', function() {
			\$this=$(this);
			if(confirm('Are you sure?')){
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('/note/delete/')."'.concat('/').concat(\$this.prev().prev().val()),
					success: function(data) {
						\$this.parent().prev().html('Deleted!');
						setTimeout(function() {
							$('.modal.in').modal('hide');
							$.fn.yiiListView.update('note-list', {
									data: $(this).serialize()
							});
						}, 800);
					}
				});
				return false;
			}
		});
	});
		
//********* left menu: modules-recommended(related)
/*
	$.ajax({
		data: {module_id: ".$model->id."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchModulesRelated')."',
		success: function(html) {
			$('#concept-related-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
*/
	
//********* left menu: user-in-the-same-module
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

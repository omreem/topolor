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
");
?>

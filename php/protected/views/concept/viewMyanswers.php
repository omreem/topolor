<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleId;?>"><?php echo $moduleTitle;?></a> &raquo; My answers</span>
	  	<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
  </div>
  <div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($moduleId);?></div>
</div>

<ul class="nav nav-tabs top-panel-fix">
	<li class="filter_by-all<?php echo $filter_by == 'all' ? ' active' : '';?>"><a class="btn-link">All</a></li>
	<li class="filter_by-incorrect<?php echo $filter_by == 'incorrect' ? ' active' : '';?>"><a class="btn-link">Incorrectly answered</a></li>
	<li class="filter_by-correct<?php echo $filter_by == 'correct' ? ' active' : '';?>"><a class="btn-link">Correctly answered</a></li>
</ul>

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

<?php $form = $this->beginWidget('GxActiveForm', array(
	'method' => 'get',
	'action' => $this->createUrl('conceptList'),
	'id' => 'filter-form'
)); ?>
<input name="filter_by" id="filter_by" type="hidden"/>
<input name="moduleId" id="moduleId" type="hidden" value="<?php echo $moduleId;?>"/>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewMyanswers',
	'summaryText'=>'',
	'emptyText'=>'No quiz yet.',
	'id'=>'question-list',
));

Yii::app()->clientScript->registerScript('view-quizList-js', "
	$('.concept-tag').popover();

//******** filter
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('question-list', { 
	        data: $(this).serialize(),
			complete: function(){\$('.concept-tag').popover();}
	    });
	    return false;
	});
		
	$('.filter_by-all').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('all');
		$('#filter-form').submit();
	});
		
	$('.filter_by-incorrect').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('incorrect');
		$('#filter-form').submit();
	});
		
	$('.filter_by-correct').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('correct');
		$('#filter-form').submit();
	});
		
//********* left menu: concepts/modules-recommended(related)

	$('#user-ranking-content').parent().find('ul').children('li').eq(1).hide();
	$('#user-fiter-content').parent().hide();
	$('#top-concepts').find('ul').children('li').eq(0).text('Recommended concepts');
	$('#top-concepts').find('ul').children('li').eq(1).hide();
	
	$.ajax({
		data: {module_id: ".$moduleId."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchConceptsByIncorrectAnswers')."',
		success: function(html) {
			$('#concept-related-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
		
//********* left menu: user-ranking
	// init
	$.ajax({
		data: {concept_id: ".$moduleId."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchUsersByQuizScore')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
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
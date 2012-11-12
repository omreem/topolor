<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleId;?>"><?php echo $moduleTitle;?></a> &raquo; Concept list</span>
	  	<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
  </div>
  <div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($moduleId);?></div>
</div>

<ul class="nav nav-tabs top-panel-fix">
	<li class="filter_by-learnt<?php echo $filter_by == 'learnt' ? ' active' : '';?>"><a class="btn-link">Learnt</a></li>
	<li class="filter_by-learning<?php echo $filter_by == 'learning' ? ' active' : '';?>"><a class="btn-link">Learning</a></li>
	<li class="filter_by-upnext<?php echo $filter_by == 'upnext' ? ' active' : '';?>"><a class="btn-link">Up Next</a></li>
	<li class="filter_by-all<?php echo $filter_by == 'all' ? ' active' : '';?>"><a class="btn-link">Learning Path</a></li>
	<li class="filter_by-az<?php echo $filter_by == 'az' ? ' active' : '';?>"><a class="btn-link">A-Z</a></li>
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
	'dataProvider'=>$recentlyLearntConcepts,
	'itemView'=>'/concept/_item2',
	'viewData'=>array('filter_by' => $filter_by),
	'summaryText'=>'',
	'emptyText'=>$filter_by == 'learnt' ? 'No leant concept yet.' : 'No learning concept.',
	'id'=>'concept-list',
));

Yii::app()->clientScript->registerScript('concept-list-js', "
		
$('.concept-tag').popover();
		
//******** filter
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('concept-list', { 
	        data: $(this).serialize(),
			complete: function(){
				$('.concept-tag').popover();
			}
	    });
		
	    return false;
	});
		
	$('.filter_by-learnt').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('learnt');
		$('#filter-form').submit();
	});
		
	$('.filter_by-learning').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('learning');
		$('#filter-form').submit();
	});
		
	$('.filter_by-upnext').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('upnext');
		$('#filter-form').submit();
	});
		
	$('.filter_by-az').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('az');
		$('#filter-form').submit();
	});
		
	$('.filter_by-all').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('');
		$('#filter-form').submit();
	});
		
//********* left menu: concepts/modules-recommended(related)
	$.ajax({
		data: {order_by: 'learning', module_id: ".$moduleId."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchConceptsByLearner')."',
		success: function(html) {
			$('#concept-related-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});

//********* left menu: user-ranking
	// init
	$.ajax({
		data: {rank_by: 'answers', concept_id: ".$moduleId."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchUsers')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
		
	$.ajax({
		data: {concept_id: ".$moduleId."},
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
			data: {rank_by: rank_by, concept_id: ".$moduleId."},
			type: 'post',
			url: '".$this->createUrl('concept/fetchUsers')."',
			success: function(html) {
				$('#user-ranking-content').html(html);
				//$('[rel=tooltip]').tooltip();
			},
		});
		
		var url = $('#study-buddis .user-filter-by').text() == 'Learning' ? '".$this->createUrl('concept/fetchUsersLearning')."' : '".$this->createUrl('concept/fetchUsersLearnt')."';
		$.ajax({
			data: {concept_id: ".$moduleId."},
			type: 'post',
			url: url,
			success: function(html) {
				$('#study-buddis #user-fiter-content').html(html);
				//$('[rel=tooltip]').tooltip();
			},
		});
		
		var order_by = $('#top-concepts .user-filter-by').text() == 'Learning' ? 'learning' : 'learnt';
		$.ajax({
			data: {order_by: order_by, module_id: ".$moduleId."},
			type: 'post',
			url: '".$this->createUrl('concept/fetchConceptsByLearner')."',
			success: function(html) {
				$('#top-concepts #concept-related-content').html(html);
				$('[rel=tooltip]').tooltip();
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
				data: {rank_by: 'questions', concept_id: ".$moduleId."},
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
				data: {rank_by: 'answers', concept_id: ".$moduleId."},
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
	$('#study-buddis .user-filter-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('#study-buddis .user-filter-by').text('Learning');
			$(this).text('Learnt');
			$.ajax({
				data: {concept_id: ".$moduleId."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersLearning')."',
				success: function(html) {
					$('#study-buddis #user-fiter-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('#study-buddis .user-filter-by').text('Learnt');
			$(this).text('Learning');
		
			$.ajax({
				data: {concept_id: ".$moduleId."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersLearnt')."',
				success: function(html) {
					$('#study-buddis #user-fiter-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		}
	});
		
	$('#top-concepts .user-filter-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('#top-concepts .user-filter-by').text('Learning');
			$(this).text('Learnt');
		
			$.ajax({
				data: {order_by: 'learning', module_id: ".$moduleId."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchConceptsByLearner')."',
				success: function(html) {
					$('#top-concepts #concept-related-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('#top-concepts .user-filter-by').text('Learnt');
			$(this).text('Learning');
		
			$.ajax({
				data: {order_by: 'learnt', module_id: ".$moduleId."},
				type: 'post',
				url: '".$this->createUrl('concept/fetchConceptsByLearner')."',
				success: function(html) {
					$('#top-concepts #concept-related-content').html(html);
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
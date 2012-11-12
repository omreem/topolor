<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title">
	  		<a href="<?php echo Yii::app()->baseUrl.'/index.php/concept/'.$model->concept->module->id;?>" class="btn-link"><?php echo $model->concept->module->title;?></a>
  			 &raquo; <a href="<?php echo Yii::app()->baseUrl.'/index.php/concept/'.$model->concept->id;?>" class="btn-link"><?php echo $model->concept->title;?></a>
  			 &raquo; Quiz
  		</span>
  		<div id="learnt-info" class="pull-right">
  		<?php if ($learnt_at != null) {?>
  		<span class="date-time pull-right">Learnt at: <?php echo Helpers::datatime_feed($learnt_at);?></span>
  		<?php } else { ?>
  		<?php echo CHtml::ajaxButton ("I've learnt",
						CController::createUrl('/concept/hasLearnt'), 
						array('update' => '#learnt-info',
							'type' => 'POST',
							'data' => array(
								'concept_id' => $model->id,
							),
						),
						array('class' =>'btn pull-right',
							'id' => 'hl'.uniqid()
			));?>
  		<?php } ?>
  		</div>
  </div>
</div>

<div style="border: solid 1px #ddd; padding: 20px;">
	<?php
		if ($questions == null)
			echo 'no questions for this concept!';
			
		else {	
			if ($quizDoneAt != null) {
				// the learner has answered all the questions in the quiz
				$this->renderPartial('_questionOld', array(
						'questions' => $questions,
						'concept_id'=>$model->concept->id,
						'quizDoneAt' => $quizDoneAt,
				));
			} else {
				// new quiz
				$this->renderPartial('_questionsForm', array(
						'questions'=>$questions,
						'quiz_id'=>$model->id,
						'concept_id'=>$model->concept->id,
				));
			}
		}	
	?>
</div>

<?php Yii::app()->clientScript->registerScript('concept-view-js', "
//********* left menu: concepts/modules-recommended(related)
	$('#top-concepts').find('ul').children('li').eq(1).hide();
	$('#top-concepts').find('ul').children('li').eq(0).text('Recommended concepts');
	$.ajax({
		data: {concept_id: ".$model->id."},
		type: 'post',
		url: '".$this->createUrl('concept/fetchConceptsRelated')."',
		success: function(html) {
			$('#concept-related-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
	
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
		
		var url = $('#study-buddis .user-filter-by').text() == 'Learning' ? '".$this->createUrl('concept/fetchUsersLearning')."' : '".$this->createUrl('concept/fetchUsersLearnt')."';
		$.ajax({
			data: {concept_id: ".$model->id."},
			type: 'post',
			url: url,
			success: function(html) {
				$('#study-buddis #user-fiter-content').html(html);
				//$('[rel=tooltip]').tooltip();
			},
		});
		
		var order_by = $('#top-concepts .user-filter-by').text() == 'Learning' ? 'learning' : 'learnt';
		$.ajax({
			data: {order_by: order_by, module_id: ".$model->id."},
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
	$('#study-buddis .user-filter-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('#study-buddis .user-filter-by').text('Learning');
			$(this).text('Learnt');
			$.ajax({
				data: {concept_id: ".$model->id."},
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
				data: {concept_id: ".$model->id."},
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
				data: {order_by: 'learning', module_id: ".$model->id."},
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
				data: {order_by: 'learnt', module_id: ".$model->id."},
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


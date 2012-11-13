<div class="hero-unit top-panel-fix">
  <h1>Module Center</h1>
  <p>This is balabala...</p>
  <p>Some Info</p>
  <p>Other Instructions</p>
  <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
</div>
<?php $count = count($moduleArr);

for ($i=0;$i<$count;$i++) :
	$description = $moduleArr[$i]['description'];
	
	$descriptionShort = strlen($description) > 160 ? substr($description, 0, 160).'...' : $description;
	
	if ($i % 3 == 0) :?>
	<div class="row-fluid">
		<ul class="thumbnails">
	<?php endif;?>
			<li class="span4">
				<div class="thumbnail">
					<img src="http://placehold.it/300x200" alt="">
					<div class="caption">
						<h3><?php echo $moduleArr[$i]['title'];?></h3>
						<p style='min-height:100px; height:auto !important; height:100px;'><?php echo $descriptionShort;?></p>
						<div id="modal-<?php echo $moduleArr[$i]['id'];?>" class="modal hide fade in" style="display: none; ">
							<div class="modal-header">
								<a class="close" data-dismiss="modal">×</a>
								<h3>Module: <?php echo $moduleArr[$i]['title'];?></h3>
							</div>
							<div class="modal-body">
								<p><?php echo $description;?></p>		        
							</div>
							<div class="modal-footer">
								<?php echo CHtml::ajaxLink ("Confirm",
									CController::createUrl('/concept/register'), 
									array(
											'type' => 'POST',
											'data' => array('id'=>$moduleArr[$i]['id']),
											'success' => 'function(data){
												window.location = "'.Yii::app()->homeUrl.'/concept/'.$moduleArr[$i]['id'].'";
											}',
									),
									array('class' =>'btn btn-primary')
								);?>
								<a href="#" class="btn" data-dismiss="modal">Cancel</a>
							</div>
						</div>
						<p>
						<?php if (LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
																array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$moduleArr[$i]['id'])) == null ) {?>
							<a data-toggle="modal" href="#modal-<?php echo $moduleArr[$i]['id'];?>" class="btn btn-primary">Register</a>
							<a href="<?php echo CController::createUrl('concept/preview/'.$moduleArr[$i]['id']);?>" class="btn">Preview &raquo;</a>
						<?php } else {?>
							<a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleArr[$i]['id'];?>" class="btn btn-primary">Get In</a>
						<?php } ?>
						</p>
					</div>
				</div>
			</li>
	<?php if ($i % 3 == 2) :?>
		</ul>
	</div>
	<?php endif;
endfor;?>

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

<?php Yii::app()->clientScript->registerScript('module-center-js', "
		
//********* left menu:
		
//////////top modules
	$('#top-concepts .nav-header').text('Top modules');
	$.ajax({
		url: '".$this->createUrl('concept/fetchModule')."',
		success: function(html) {
			$('#concept-related-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
	
	$('#top-concepts .user-filter-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('#top-concepts .user-filter-by').text('Learning');
			$(this).text('Learnt');
		
			$.ajax({
				data: {filter_by: 'learning'},
				type: 'post',
				url: '".$this->createUrl('concept/fetchModule')."',
				success: function(html) {
					$('#top-concepts #concept-related-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('#top-concepts .user-filter-by').text('Learnt');
			$(this).text('Learning');
		
			$.ajax({
				data: {filter_by: 'learnt'},
				type: 'post',
				url: '".$this->createUrl('concept/fetchModule')."',
				success: function(html) {
					$('#top-concepts #concept-related-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		}
	});

////////top-users
	$('#top-users .user-rank-order-by').text('Learning');
	$('#top-users .user-rank-order-by-change').text('Learnt');
	$.ajax({
		data: {rank_by: 'learning'},
		type: 'post',
		url: '".$this->createUrl('concept/fetchUsersRankByModule')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
		
	$('.user-rank-order-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'Learning') {
			$('#top-users .user-rank-order-by').text('Learning');
			$(this).text('Learnt');
			$.ajax({
				data: {rank_by: 'learning'},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersRankByModule')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('#top-users .user-rank-order-by').text('Learnt');
			$(this).text('Learning');
		
			$.ajax({
				data: {rank_by: 'learnt'},
				type: 'post',
				url: '".$this->createUrl('concept/fetchUsersRankByModule')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		}
	});
		

//////////study buddies
	$('#study-buddis').hide();
		
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

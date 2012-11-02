<div class="well top-panel-fix">
	<span>Send message to: <?php echo $newMsg->toUser;?></span>
	<div style="margin-bottom:-20px;">
	<?php $form = $this->beginWidget('GxActiveForm', array(
		'enableAjaxValidation' => false,
		'id' => 'message-form',
		'action' => $this->createUrl('create'),
	));?>
		<?php echo $form->textArea($newMsg, 'description');?>
		<?php echo $form->hiddenField($newMsg, 'to_user_id'); ?>
		<?php echo $form->hiddenField($newMsg, 'to_message_id'); ?>
		<div>
			<a class="btn btn-primary btn-create disabled">Send</a>
			<a class="btn btn-cancel">Cancel</a>
		</div>
	<?php $this->endWidget();?>
	</div>
</div><!-- form -->

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>new CArrayDataProvider($model->all, array(
		'pagination'=>array(
			'pageSize'=>20,
		),
	)),
	'itemView'=>'_item',
	'summaryText'=>'',
	'id'=>'message-list',
)); 

Yii::app()->clientScript->registerScript('message-view-js', "
	
	$('#Message_description').focus();
	
	$('#Message_description').keyup(function(event) {
		if ($('#Message_description').val() != '')
			$('#message-form .btn-create').removeClass('disabled')
		else
			$('#message-form .btn-create').addClass('disabled')
	});
	
	$('#message-form .btn-create').click(function(){
		if($(this).hasClass('disabled'))
			return;
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('create')."',
			data: \$form.serialize(),
			success: function (html) {
				setTimeout(function() {
					$.fn.yiiListView.update('message-list', {
						data: $(this).serialize()
					});
					$('#message-form .btn-create').addClass('disabled')
					$('#Message_description').val('');
                }, 400);
			}
		});
		return false;
	});
	
	$('#message-form .btn-cancel').click(function (){
		$('#message-form .btn-create').addClass('disabled')
		$('#Message_description').val('');
	});
	
	$('#message-list .post .btn-reply').live('click', function() {
		$('#Message_description').focus();
	});
");
<div class="well top-panel-fix">
	<div style="margin-bottom:-20px;">
	<?php $this->renderPartial('_form', array('model' => $newMsg));?>
	</div>
</div><!-- form -->

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'summaryText'=>'',
	'emptyText' => 'No message yet.',
	'id'=>'message-list'
));

Yii::app()->clientScript->registerScript('note-index-js', "
		
//****** create
	$('#Message_description').focus(function () {
		$('#message-form .form-rest').slideDown();
	});
		
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
					$('#message-form .form-rest').slideUp();
					$('#message-form .btn-create').addClass('disabled')
					$('#message-form').find('textarea').val('');
					$('#message-form').find('#Message_description').attr('placeholder','Send a message');
                }, 400);
			}
		});
		return false;
	});
	
	$('#message-form .btn-cancel').click(function (){
		$('#message-form .form-rest').slideUp();
		$('#message-form .btn-create').addClass('disabled')
		$('#message-form').find('textarea').val('');
		$('#message-form').find('#Message_description').attr('placeholder','Send a message');
	});
		
//****** message list
	$('.post').live('mouseenter', function() {
		$(this).css('background-color', '#edf3f8');
		$(this).css('cursor', 'pointer');
	});
	
	$('.post').live('mouseleave', function() {
		$(this).css('background-color', '');
		$(this).css('cursor', 'default');
	});
		
	$('.post').live('click', function() {
		window.location = '".Yii::app()->homeUrl."/message/'+$(this).find('#data_id').val();
	});
		
	
");
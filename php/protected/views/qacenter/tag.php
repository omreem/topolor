<ul class="nav nav-tabs top-panel-fix">
	<li class="active btn-order-frequency"><a class="btn-link">Frequency</a></li>
	<li class="btn-order-users"><a class="btn-link">Users</a></li>
	<li class="btn-order-name"><a class="btn-link">Name</a></li>
	<li class="btn-order-recent"><a class="btn-link">Recent</a></li>
</ul>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'method' => 'get',
	'id' => 'order-form'
)); ?>
<input name="order_by" id="order_by" type="hidden"/>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewTag',
	'summaryText'=>'',
	'emptyText' => 'No tag related to questions yet.',
	'id'=>'tag-list',
));

Yii::app()->clientScript->registerScript('ask-index-js', "

//******** filter
	$('#order-form').submit(function(){
	    $.fn.yiiListView.update('tag-list', { 
	        data: $(this).serialize()
	    });
	    return false;
	});
		
	$('.btn-order-frequency').click(function(){
		$(this).addClass('active');
		$('.btn-order-users').removeClass('active');
		$('.btn-order-name').removeClass('active');
		$('.btn-order-recent').removeClass('active');
		
		$('#order-form #order_by').val('frequency');
		$('#order-form').submit();
	});
		
	$('.btn-order-users').click(function(){
		$(this).addClass('active');
		$('.btn-order-frequency').removeClass('active');
		$('.btn-order-name').removeClass('active');
		$('.btn-order-recent').removeClass('active');
		
		$('#order-form #order_by').val('users');
		$('#order-form').submit();
	});
		
	$('.btn-order-name').click(function(){
		$(this).addClass('active');
		$('.btn-order-frequency').removeClass('active');
		$('.btn-order-users').removeClass('active');
		$('.btn-order-recent').removeClass('active');
		
		$('#order-form #order_by').val('name');
		$('#order-form').submit();
	});
		
	$('.btn-order-recent').click(function(){
		$(this).addClass('active');
		$('.btn-order-frequency').removeClass('active');
		$('.btn-order-users').removeClass('active');
		$('.btn-order-name').removeClass('active');
		
		$('#order-form #order_by').val('recent');
		$('#order-form').submit();
	});
		
//****** tag list
	$('.post').live('mouseenter', function() {
		$(this).css('background-color', '#edf3f8');
		$(this).css('cursor', 'pointer');
	});
	
	$('.post').live('mouseleave', function() {
		$(this).css('background-color', '');
		$(this).css('cursor', 'default');
	});
		
	$('.post').live('click', function() {
		window.location = '".$this->createUrl('viewTag')."?tag='+$(this).find('#tag_name').val();
	});
		
");
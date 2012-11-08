<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Topolor for e-learning">
    <meta name="author" content="Shi Lei">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/ext/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/ext/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" rel="stylesheet">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

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

<!-- Top Navbar -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="<?php echo Yii::app()->homeUrl;?>"><?php echo CHtml::encode(Yii::app()->name);?></a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li><a href="<?php echo Yii::app()->homeUrl;?>">Home</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/module';?>">Module Center</a></li>
          <li class="active"><a href="<?php echo Yii::app()->homeUrl.'/qacenter';?>">Q&amp;A Center</a></li>
        </ul>
        <?php if (!Yii::app()->user->isGuest):?>
        <ul class="nav pull-right">
          <li><a rel="tooltip" data-placement="bottom" title="My profile">
          	<?php echo GxHtml::image(
			Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.Yii::app()->user->id,'',
			array('style'=>'height: 20px; width: 20px;'));?>
          	&nbsp;<?php echo Yii::app()->getModule('user')->user();?></a></li>
          <li><a rel="tooltip" data-placement="bottom" title="Log out" href="<?php echo Yii::app()->homeUrl.'/user/logout';?>">Log out</a></li>
        </ul>
        <?php endif;?>
      </div>
    </div>
  </div>
</div>

<!-- Main Container -->
<div class="container">
  <div class="row">
    <div class="span3">
      <div class="well sidebar-nav left-main-menu">
        user info
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
        <ul class="nav nav-list">
          <li class="nav-header">Menu</li>
          <li <?php echo $this->action->id == 'index' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/index');?>"><i class="icon-signal"></i> Trends</a></li>
          <li <?php echo $this->action->id == 'concept' || $this->action->id == 'viewConcept' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/concept');?>"><i class="icon-tasks"></i> Concepts</a></li>
          <li <?php echo $this->action->id == 'tag' || $this->action->id == 'viewTag' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/tag');?>"><i class="icon-tag"></i> Tags</a></li>
          <li <?php echo $this->action->id == 'qas' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/qas');?>"><i class="icon-comment"></i> Q&amp;As</a></li>
		  <li <?php echo $this->action->id == 'myqa' && isset($_GET['filter_by']) && $_GET['filter_by'] == 'myquestions' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/myqa').'?filter_by=myquestions';?>"><i class="icon-question-sign"></i> My questions</a></li>
		  <li <?php echo $this->action->id == 'myqa' && isset($_GET['filter_by']) && $_GET['filter_by'] == 'myanswers' ? ' class="active"' : '';?>><a href="<?php echo $this->createUrl('qacenter/myqa').'?filter_by=myanswers';?>"><i class="icon-info-sign"></i> My answers</a></li>
        </ul>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
      <ul class="nav nav-tabs">
              <li><a class="nav-header">Top Users</a></li>
              <li class="dropdown pull-right">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  <span class="user-rank-order-by">by answers</span>
                  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <li><a class="btn-link user-rank-order-by-change">by questions</a></li>
                </ul>
              </li>
            </ul>
        <div id="user-ranking-content" style="margin-top: -8px;"></div>
      </div><!--/.well -->
    </div><!--/span-->
    <div class="span9">
      <?php echo $content; ?>
    </div><!--/span-->
  </div><!--/row-->
  <hr>
</div>

<!-- Footer -->
<footer class="footer" id="footer">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> Topolor</p>
    <p>Designed by Shi Lei</p>
  </div>
</footer>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootbox.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/topolor.js"></script>
</body>
</html>

<?php Yii::app()->clientScript->registerScript('qacenter-layout-js', "

//********* left menu: user-ranking
	// init
	$.ajax({
		url: '".$this->createUrl('qacenter/fetchUsers')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});
		
	// change rank order by
	$('.user-rank-order-by-change').live('click', function() {
		
		$('[rel=tooltip]').tooltip('disable');
		
		if ($(this).text() == 'by questions') {
			$('.user-rank-order-by').text('by questions');
			$(this).text('by answers');
			$.ajax({
				data: {rank_by: 'questions'},
				type: 'post',
				url: '".$this->createUrl('qacenter/fetchUsers')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
					$('[rel=tooltip]').tooltip();
				},
			});
		
		} else {
			$('.user-rank-order-by').text('by answers');
			$(this).text('by questions');
		
			$.ajax({
				data: {rank_by: 'answers'},
				type: 'post',
				url: '".$this->createUrl('qacenter/fetchUsers')."',
				success: function(html) {
					$('#user-ranking-content').html(html);
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
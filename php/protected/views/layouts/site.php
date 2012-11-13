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
          <li class="active"><a href="<?php echo Yii::app()->homeUrl;?>">Home</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/concept';?>">Module Center</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/qacenter';?>">Q&amp;A Center</a></li>
        </ul>
        <?php if (!Yii::app()->user->isGuest):?>
        <ul class="nav pull-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	            <?php echo GxHtml::image(
				Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.Yii::app()->user->id,'',
				array('style'=>'height: 20px; width: 20px;'));?>
	          	&nbsp;<?php echo Yii::app()->getModule('user')->user();?>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a href="#">Change my avatar</a></li>
              <li><a href="#">Change my password</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo Yii::app()->homeUrl.'/user/logout';?>">Sign Out</a></li>
            </ul>
          </li>
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
      	<div style="display: table;">
	      	<div style="display: table-cell; padding: 2px 12px;">
	        <?php echo GxHtml::image(
					Yii::app()->baseUrl.'/uploads/images/profile-avatar/'.Yii::app()->user->id,'',
					array(
						'style'=>'width: 84px; height: 84px;',
						'class'=>'img-rounded',
					));?>
			</div>
			<div style="display: table-cell; vertical-align: middle;">
				<b><span style="font-size: 18px;"><?php echo CHtml::encode(Yii::app()->user->name);?></span></b><br>
				Shared: <span id="countShared"></span><br>
				Commented: <span id="countCommented"></span><br>
				Favorited: <span id="countFavorited"></span>
			</div>
		</div>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
        <ul class="nav nav-list" id="left-menu-list">
          <li class="nav-header">Menu</li>
          <li <?php echo $this->id == 'feed' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->homeUrl;?>"><i class="icon-list-alt"></i> News Feed</a></li>
          <li <?php echo $this->id == 'message' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('message');?>"><i class="icon-envelope"></i> Messages</a></li>
          <li <?php echo $this->id == 'ask' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('ask');?>"><i class="icon-comment"></i> Q&amp;As</a></li>
          <li <?php echo $this->id == 'note' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('note');?>"><i class="icon-edit"></i> Notes</a></li>
          <li <?php echo $this->id == 'todo' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('todo');?>"><i class="icon-check"></i> To-Do</a></li>
          <li class="nav-header">My Modules</li>
        </ul>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
      	<ul class="nav nav-tabs">
          <li class="nav-header">Top Users</li>
          <li class="dropdown pull-right" style="margin-top: -8px; margin-bottom: 2px;">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span class="user-rank-order-by">Shared</span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a class="btn-link user-rank-order-by-change-commented">Commented</a></li>
              <li><a class="btn-link user-rank-order-by-change-favorited">Favorited</a></li>
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

<!-- Message modal -->
<div id="message-modal" class="modal hide fade in" style="display: none;">
	<div class="modal-body" style="display: table">
		<h5 class="message-send-to"></h5>
		<?php $newMessage = new Message;
		$form = $this->beginWidget('GxActiveForm', array(
			'enableAjaxValidation' => false,
			'id' => 'message-modal-form',
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

<?php Yii::app()->clientScript->registerScript('site-view-js', "
	$.ajax({
		url: '".$this->createUrl('stats/stats')."',
		success: function(stats) {
			$('#countFavorited').html(stats.countFavorited);
			$('#countShared').html(stats.countShared);
			$('#countCommented').html(stats.countCommented);
		}
	});
	
	$.ajax({
		url: '".$this->createUrl('stats/myModules')."',
		success: function(html) {
			$('#left-menu-list').append(html);
		}
	});
//********* left menu: user-ranking
	// init
	$.ajax({
		data: {rank_by: 'shared'},
		type: 'post',
		url: '".$this->createUrl('feed/fetchTopUsers')."',
		success: function(html) {
			$('#user-ranking-content').html(html);
			$('[rel=tooltip]').tooltip();
		},
	});

	// change rank order by
	$('.user-rank-order-by-change-shared').live('click', function() {
		$(this).text($('.user-rank-order-by').text());
		$(this).removeClass('user-rank-order-by-change-shared');
		
		if ($('.user-rank-order-by').text() == 'Commented')
			$(this).addClass('user-rank-order-by-change-commented');
		else
			$(this).addClass('user-rank-order-by-change-favorited');
			
		$('.user-rank-order-by').text('Shared');
		
		$('[rel=tooltip]').tooltip('disable');
		
		$.ajax({
			data: {rank_by: 'shared'},
			type: 'post',
			url: '".$this->createUrl('feed/fetchTopUsers')."',
			success: function(html) {
				$('#user-ranking-content').html(html);
				$('[rel=tooltip]').tooltip();
			},
		});
	});
		
	$('.user-rank-order-by-change-favorited').live('click', function() {
		$(this).text($('.user-rank-order-by').text());
		$(this).removeClass('user-rank-order-by-change-favorited');
		
		if ($('.user-rank-order-by').text() == 'Commented')
			$(this).addClass('user-rank-order-by-change-commented');
		else
			$(this).addClass('user-rank-order-by-change-shared');
		
		$('.user-rank-order-by').text('Favorited');
		
		$('[rel=tooltip]').tooltip('disable');
		
		$.ajax({
			data: {rank_by: 'favorited'},
			type: 'post',
			url: '".$this->createUrl('feed/fetchTopUsers')."',
			success: function(html) {
				$('#user-ranking-content').html(html);
				$('[rel=tooltip]').tooltip();
			},
		});
	});
		
	$('.user-rank-order-by-change-commented').live('click', function() {
		$(this).text($('.user-rank-order-by').text());
		$(this).removeClass('user-rank-order-by-change-commented');
		
		if ($('.user-rank-order-by').text() == 'Favorited')
			$(this).addClass('user-rank-order-by-change-favorited');
		else
			$(this).addClass('user-rank-order-by-change-shared');
		
		$('.user-rank-order-by').text('Commented');
		
		$('[rel=tooltip]').tooltip('disable');
		
		$.ajax({
			data: {rank_by: 'commented'},
			type: 'post',
			url: '".$this->createUrl('feed/fetchTopUsers')."',
			success: function(html) {
				$('#user-ranking-content').html(html);
				$('[rel=tooltip]').tooltip();
			},
		});
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
		$('#message-modal-form #Message_to_user_id').val($(this).find('#data_id').val());
		$('#message-modal .message-send-to').text('Send message to: '+ $(this).find('.name-user').html());
	});
	
	$('#message-modal-form #Message_description').keyup(function() {
		if ($('#message-modal-form#Message_description').val() != '')
			$('#message-modal .btn-message-send').removeClass('disabled')
		else
			$('#message-modal .btn-message-send').addClass('disabled')
	});
		
	$('#message-modal .btn-message-send').click(function(){
		if($(this).hasClass('disabled'))
			return;
		
		$('#message-modal .btn-message-send').addClass('disabled');
		$('#message-modal .btn-message-send').text('Sending...');
		
		var form = $('#message-modal-form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('message/create')."',
			data: form.serialize(),
			success: function (html) {
				$('#message-modal .alert-success').show();
				setTimeout(function() {
					$('#message-modal').modal('hide');
					$('#message-modal-form').find('#Message_description').val('');
					$('#message-modal-form').find('#Message_to-user-id').val('');
					$('#message-modal-form').find('#Message_description').attr('placeholder','Send a message');
					$('#message-modal .alert-success').hide();
					$('#message-modal .btn-message-send').text('Send');
                }, 400);
			}
		});
		return false;
	});
	
	$('#message-modal .btn-cancel').click(function (){
		$('#message-modal').modal('hide');
		$('#message-modal .btn-message-send').addClass('disabled')
		$('#message-modal .btn-message-send').text('Send');
		$('#message-modal-form').find('textarea').val('');
		$('#message-modal-form').find('#Message_description').attr('placeholder','Send a message');
	});
		
		
");

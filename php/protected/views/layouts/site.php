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
          <li<?php echo Yii::app()->controller->id == 'feed' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl;?>">Home</a></li>
          <li<?php echo Yii::app()->controller->id == 'module' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl.'/module';?>">Module Center</a></li>
          <li<?php echo Yii::app()->controller->id == 'ask' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl.'/ask';?>">Q&amp;A Center</a></li>
          
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
        <ul class="nav nav-list">
          <li class="nav-header">Menu</li>
          <li <?php echo $this->id == 'feed' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->homeUrl;?>"><i class="icon-list-alt"></i> News Feed</a></li>
          <li <?php echo $this->id == 'message' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('message');?>"><i class="icon-envelope"></i> Messages</a></li>
          <li <?php echo $this->id == 'ask' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('ask');?>"><i class="icon-comment"></i> Q&amp;As</a></li>
          <li <?php echo $this->id == 'note' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('note');?>"><i class="icon-edit"></i> Notes</a></li>
          <li <?php echo $this->id == 'todo' ? ' class="active"' : '';?>><a href="<?php echo Yii::app()->createUrl('todo');?>"><i class="icon-check"></i> To-Do</a></li>
          <li class="nav-header">My Modules</li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/module/3';?>"><i class="icon-fire"></i> PHP Tutorial</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/module/4';?>"><i class="icon-bullhorn"></i> HTML5 Tutorial</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/module/5';?>"><i class="icon-road"></i> CSS3 Tutorial</a></li>
        </ul>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
        <ul class="nav nav-list">
          <li class="nav-header">People You Might Like</li>
          <li><a href="#"><i class="icon-user"></i> User One</a></li>
          <li><a href="#"><i class="icon-user"></i> User Two</a></li>
          <li class="nav-header">Modules You Might Like</li>
          <li><a href="#"><i class="icon-hdd"></i> HTML Tutorial</a></li>
          <li><a href="#"><i class="icon-hdd"></i> CSS Tutorial</a></li>
        </ul>
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
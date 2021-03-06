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
          <li><a href="<?php echo Yii::app()->homeUrl;?>">Home</a></li>
          <li class="active"><a href="<?php echo Yii::app()->homeUrl.'/module';?>">Module Center</a></li>
          <li><a href="<?php echo Yii::app()->homeUrl.'/qacenter';?>">Q&amp;A Center</a></li>
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
      	<ul class="nav nav-tabs">
          <li class="nav-header" style="margin-bottom: 6px;">Recommended modules</li>
        </ul>
        <div id="modules-related-content" style="margin-top: -8px;"></div>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
      	<ul class="nav nav-tabs">
          <li class="nav-header">Top Users</li>
          <li class="dropdown pull-right" style="margin-top: -8px; margin-bottom: 2px;">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span class="user-rank-order-by">Answers</span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a class="btn-link user-rank-order-by-change">Questions</a></li>
            </ul>
          </li>
        </ul>
        <div id="user-ranking-content" style="margin-top: -8px;"></div>
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
      	<ul class="nav nav-tabs">
          <li class="nav-header">Study Buddis</li>
          <li class="dropdown pull-right" style="margin-top: -8px; margin-bottom: 2px;">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span class="user-filter-by">Learning</span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a class="btn-link user-filter-by-change">Learnt</a></li>
            </ul>
          </li>
        </ul>
        <div id="user-fiter-content" style="margin-top: -8px;"></div>
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
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.validate.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/topolor.js"></script>
</body>
</html>
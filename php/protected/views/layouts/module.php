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
          <li<?php echo Yii::app()->controller->id == 'site' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl;?>">Home</a></li>
          <li<?php echo Yii::app()->controller->id == 'module' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl.'/module';?>">Module Center</a></li>
          <li<?php echo Yii::app()->controller->id == 'ask' ? ' class="active"' : ' '; ?>><a href="<?php echo Yii::app()->homeUrl.'/ask';?>">Q&amp;A Center</a></li>
        </ul>
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
        chat tool
      </div><!--/.well -->
      <div class="well sidebar-nav left-main-menu">
        module recommendation
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
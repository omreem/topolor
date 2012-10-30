<?php

$this->breadcrumbs = array(
	Favorite::label(2),
	Yii::t('app', 'Index'),
);

$this->menu = array(
	array('label'=>Yii::t('app', 'Create') . ' ' . Favorite::label(), 'url' => array('create')),
	array('label'=>Yii::t('app', 'Manage') . ' ' . Favorite::label(2), 'url' => array('admin')),
);
?>

<h1><?php echo GxHtml::encode(Favorite::label(2)); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); 
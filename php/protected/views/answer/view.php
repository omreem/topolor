<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
'id',
array(
			'name' => 'learner',
			'type' => 'raw',
			'value' => $model->learner !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->learner)), array('user/view', 'id' => GxActiveRecord::extractPkValue($model->learner, true))) : null,
			),
'description',
array(
			'name' => 'ask',
			'type' => 'raw',
			'value' => $model->ask !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->ask)), array('ask/view', 'id' => GxActiveRecord::extractPkValue($model->ask, true))) : null,
			),
'is_best',
'useful',
'useless',
'create_at',
'update_at',
	),
)); ?>


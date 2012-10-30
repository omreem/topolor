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
			'name' => 'author',
			'type' => 'raw',
			'value' => $model->author !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->author)), array('user/view', 'id' => GxActiveRecord::extractPkValue($model->author, true))) : null,
			),
array(
			'name' => 'concept',
			'type' => 'raw',
			'value' => $model->concept !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->concept)), array('concept/view', 'id' => GxActiveRecord::extractPkValue($model->concept, true))) : null,
			),
'description',
'correct_answer',
'create_at',
'update_at',
	),
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('options')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->options as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('questionOption/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('quizzes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->quizzes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('quiz/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>
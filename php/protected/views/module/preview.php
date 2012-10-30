<div class="well top-panel-fix module-structure-panel">
	<span class="module-title"><?php echo $model->title;?></span><br><br>
	<?php echo $model->description;?><br><br>
	<?php echo CHtml::ajaxLink ("Register",
		CController::createUrl('register'), 
		array(
			'type' => 'POST',
			'data' => array('id'=>$model->id),
			'success' => 'function(data){
							window.location = "'.Yii::app()->homeUrl.'/module/'.$model->id.'";
						}',
		),
		array('class' =>'btn btn-primary')
	);?>
</div>
<div class="well">
	<table class="tree-table"><?php 
		foreach ($concepts as $concept) {
			if ($concept->id == $concept->root)
				continue;
			
			echo "<tr><td class='title' style='padding-left: ".(($concept->level-1)*20)."px;'>".($concept->level==2?"<b>":"").CHtml::encode($concept->title).($concept->level==2?"</b>":"")."<span class='description'>".CHtml::encode($concept->description)."</span></td></tr>";
	}?></table>
</div>

<?php Yii::app()->clientScript->registerScript('concept-preview-js', "
		$('.tree-table .title').hover(function() {
			$(this).children(':first').fadeIn(50);
		});
		$('.tree-table .title').mouseleave(function() {
			$(this).children('.description').fadeOut(50);
		});
");?>
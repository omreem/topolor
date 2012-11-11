<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleId;?>"><?php echo $moduleTitle;?></a> &raquo; Quiz list</span>
	  	<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
  </div>
  <div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($moduleId);?></div>
</div>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'/quiz/_item2',
	'summaryText'=>'',
	'emptyText'=>'No quiz yet.',
	'id'=>'concept-list',
));

Yii::app()->clientScript->registerScript('view-quizList-js', "
	$('.concept-tag').popover();
");
<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleId;?>"><?php echo $moduleTitle;?></a> &raquo; My answers</span>
	  	<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
  </div>
  <div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($moduleId);?></div>
</div>

<ul class="nav nav-tabs top-panel-fix">
	<li class="filter_by-all<?php echo $filter_by == 'all' ? ' active' : '';?>"><a class="btn-link">All</a></li>
	<li class="filter_by-incorrect<?php echo $filter_by == 'incorrect' ? ' active' : '';?>"><a class="btn-link">Incorrectly answered</a></li>
	<li class="filter_by-correct<?php echo $filter_by == 'correct' ? ' active' : '';?>"><a class="btn-link">Correctly answered</a></li>
</ul>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'method' => 'get',
	'action' => $this->createUrl('conceptList'),
	'id' => 'filter-form'
)); ?>
<input name="filter_by" id="filter_by" type="hidden"/>
<input name="moduleId" id="moduleId" type="hidden" value="<?php echo $moduleId;?>"/>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewMyanswers',
	'summaryText'=>'',
	'emptyText'=>'No quiz yet.',
	'id'=>'question-list',
));

Yii::app()->clientScript->registerScript('view-quizList-js', "
	$('.concept-tag').popover();

//******** filter
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('question-list', { 
	        data: $(this).serialize()
	    });
	    return false;
	});
		
	$('.filter_by-all').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('all');
		$('#filter-form').submit();
	});
		
	$('.filter_by-incorrect').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('incorrect');
		$('#filter-form').submit();
	});
		
	$('.filter_by-correct').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('correct');
		$('#filter-form').submit();
	});
");
<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title"><a href="<?php echo Yii::app()->homeUrl.'/concept/'.$moduleId;?>"><?php echo $moduleTitle;?></a> &raquo; Concept list</span>
	  	<span class="btn btn-link pull-right" onClick='$(".module-structure-tree").slideToggle();'>Module Structure &raquo;</span>
  </div>
  <div class="module-structure-tree" style="display: none;"><?php echo $this->getModuleStructure($moduleId);?></div>
</div>

<ul class="nav nav-tabs top-panel-fix">
	<li class="filter_by-learnt<?php echo $filter_by == 'learnt' ? ' active' : '';?>"><a class="btn-link">Learnt</a></li>
	<li class="filter_by-learning<?php echo $filter_by == 'learning' ? ' active' : '';?>"><a class="btn-link">Learning</a></li>
	<li class="filter_by-upnext<?php echo $filter_by == 'upnext' ? ' active' : '';?>"><a class="btn-link">Up Next</a></li>
	<li class="filter_by-all<?php echo $filter_by == 'all' ? ' active' : '';?>"><a class="btn-link">All Concepts</a></li>
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
	'dataProvider'=>$recentlyLearntConcepts,
	'itemView'=>'/concept/_item2',
	'viewData'=>array('filter_by' => $filter_by),
	'summaryText'=>'',
	'emptyText'=>$filter_by == 'learnt' ? 'No leant concept yet.' : 'No learning concept.',
	'id'=>'concept-list',
));

Yii::app()->clientScript->registerScript('concept-list-js', "
		
$('.concept-tag').popover();
		
//******** filter
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('concept-list', { 
	        data: $(this).serialize()
	    });
	    return false;
	});
		
	$('.filter_by-learnt').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('learnt');
		$('#filter-form').submit();
	});
		
	$('.filter_by-learning').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('learning');
		$('#filter-form').submit();
	});
		
	$('.filter_by-upnext').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('upnext');
		$('#filter-form').submit();
	});
		
	$('.filter_by-all').click(function() {
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		
		$('#filter-form #filter_by').val('');
		$('#filter-form').submit();
	});
		
");
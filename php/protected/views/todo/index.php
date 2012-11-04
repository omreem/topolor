<?php 
		$sql='select'
		.' n.id,'
		.' n.title,'
		.' n.create_at,'
		.' n.description'
		
		.' from'
		.' tpl_note as n'
		.' join tpl_concept as c on c.id = n.concept_id'
		
		.' where'
		.' c.root=1'
		.' and n.learner_id='.Yii::app()->user->id
		.' order by n.create_at desc';
		
		$sql2='select count(n.id)'
		
		.' from'
		.' tpl_note as n'
		.' join tpl_concept as c on c.id = n.concept_id'
		
		.' where'
		.' c.root=1'
		.' and n.learner_id='.Yii::app()->user->id;
		
		$countNote=Yii::app()->db->createCommand($sql2)->queryScalar();
		$notes=new CSqlDataProvider($sql, array(
			'totalItemCount'=>$countNote,
			'keyField'=>'id',
			'pagination'=>array(
					'pageSize'=>2,
			),
		));


$this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$notes,
				'itemView'=>'/note/_item',
				'summaryText'=>'',
				'pager' => array(
					'header' => '',
					'prevPageLabel' => '&lt;&lt;',
					'nextPageLabel' => '&gt;&gt;',
				),
				'id'=>'note-list',
			));

echo $notes == null? 'is null': 'not null';

?>
<div class="well top-panel-fix">
	<div style="margin-bottom:-20px;">
	<?php $this->renderPartial('_form', array('model' => $newTodo));?>
	</div>
</div><!-- form -->
<ul class="nav nav-tabs">
	<li class="active filter-btn-all"><a class="btn-link">All</a></li>
	<li class="filter-btn-today"><a class="btn-link">Today</a></li>
	<li class="filter-btn-week"><a class="btn-link">This week</a></li>
	<li class="filter-btn-month"><a class="btn-link">This month</a></li>
	<li class=" filter-btn-tags pull-right"><a class="btn-link" onclick="$('#tags-bar').toggle();">All tags</a></li>
	<li class=" filter-btn-concepts pull-right"><a class="btn-link" onclick="$('#concepts-bar').toggle();">All concepts</a></li>
	<li class="dropdown pull-right">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="text">On going</span> <b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li class="filter-btn-all_status"><a class="btn-link">All status</a></li>
			<li class="filter-btn-undone" style="display:none;"><a class="btn-link">On going</a></li>
			<li class="filter-btn-done"><a class="btn-link">Done</a></li>
			<li class="filter-btn-canceled"><a class="btn-link">Canceled</a></li>
		</ul>
	</li>
</ul>
<div id="concepts-bar" style="display:none;">
	<?php echo $this->initConceptBar();?>
</div>
<div id="tags-bar" style="display:none;">
	<?php echo $this->initTagBar();?>
</div>

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
	'id' => 'filter-form',
)); ?>
<input name="status" id="status" type="hidden" value="<?php echo Todo::STATUS_UNDONE;?>"/>
<input name="interval" id="interval" type="hidden"/>
<input name="tag" id="tag" type="hidden"/>
<input name="concept_id" id="concept_id" type="hidden"/>
<?php $this->endWidget(); ?>

<div id="tag-canvas" class="modal hide fade in" style="display: none;">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<b>Add tags</b>
	</div>
	<div class="modal-body" style="display: table">
		<div style="display: table-row">
			<div style="display: table-cell; width: 70px;">My tags:</div>
			<div style="display: table-cell" class="modal-body-tags"></div>
		</div><br>
		<div style="display: table-row">
			<div style="display: table-cell;">Tags:</div>
			<div style="display: table-cell">
				<input id="add-tags-input" type="text">
				<input id="data_id" type="hidden">
				<input id="add-tags-input_ori" type="hidden">
			</div>
		</div>
	</div>
	<div class="alert alert-success" style="display: none;">Successfully saved!</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-primary btn-small btn-save-tags disabled">Save</a>
	</div>
</div>
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'summaryText'=>'',
	'emptyText' => 'No todo yet.',
	'id'=>'todo-list',
));

Yii::app()->clientScript->registerScript('todo-index-js', "

//********* create todo
		
	$('#Todo_title').focus(function () {
		$('#todo-form .form-rest').slideDown();
	});
	
	$('#Todo_description').focus(function () {
		$(this).attr('placeholder','Description');
	});
	
	$('#Todo_title, #Todo_description').keyup(function(event) {
		if ($('#Todo_title').val() != '')
			$('#todo-form .btn-create').removeClass('disabled')
		else
			$('#todo-form .btn-create').addClass('disabled')
	});
	
	$('#todo-form .btn-create').live('click', function(){
		if($(this).hasClass('disabled')) {
			if ($('#Todo_title').val() == '') {
				$('#Todo_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Todo_title').attr('placeholder','Title');
					$('#Todo_title').focus();
				}, 400);
			} else {
				$('#Todo_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Todo_description').attr('placeholder','Description');
					$('#Todo_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#todo-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('todo-list', {
						data: $(this).serialize()
					});
					$('#todo-form').find('textarea').val('');
					$('#todo-form').find('#Todo_tags').val('');
					$('#Todo_title').attr('placeholder','Create a todo');
					$('#todo-form .btn-create').addClass('disabled');
                }, 400);
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateFiltersBar')."',
					data: $('#filter-form').serialize(),
					success: function (barInfo) {
						$('#tags-bar').html(barInfo.tagsBar);
						$('#concepts-bar').html(barInfo.conceptsBar);
					}
				});
			}
		});
		return false;
	
	});	
	
	$('#todo-form .btn-cancel').click(function (){
		$('#todo-form .form-rest').slideUp();
		$('#todo-form .btn-create').addClass('disabled')
		$('#todo-form').find('textarea').val('');
		$('#todo-form').find('#Todo_title').attr('placeholder','Create a todo');
	});

//********* update todo in the todo-list
	
	function titleClick() {
		var title_ori=$(this).html();
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><input id=\"title\" type=\"text\" style=\"width: 100%;\"></div>');
		$(this).find('#title').val(htmlDecode(title_ori));
		$(this).find('#title').focus();
		$(this).find('#title').focusout(function(){
			var title = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(title));
			$('.content-title').live('click', titleClick);
			if (title_ori != htmlEncode(title))
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id, title: title}
				});
		});
		
		$('.content-title').die('click');
	}
		
	function timeClick() {
		$('.date-time').die('click');
		var dtStrArr = $(this).html().split(' ');
		var startTime = dtStrArr[1]+' '+dtStrArr[2];
		var endTime = dtStrArr[5]+' '+dtStrArr[6];
		//if (date('H', time()) == 23 && date('i', time()) >= 30)
		var startDate = dtStrArr[0];
		var endDate = dtStrArr[4];
	
		if (startDate == 'Today')
			startDate = '".date("d-m-Y")."';
		else if (startDate == 'Tomorrow')
			startDate = '".date('d-m-Y',mktime(0, 0, 0, date("m"), date("d")+1, date("Y")))."';
		else
			startDate = startDate.substr(3,2)+'-'+startDate.substr(0,2)+'-'+'".date("Y")."';
		
		if (endDate == 'Today')
			endDate = '".date("d-m-Y")."';
		else if (endDate == 'Tomorrow')
			endDate = '".date('d-m-Y',mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."';
		else
			endDate = endDate.substr(3,2)+'-'+endDate.substr(0,2)+'-'+'".date("Y")."';
		
		var str_ori = $(this).html();
		var str = \"<br><input class='start_at_time' name='start_at_time' id='start_at_time' type='text' style='width:75px;'><i class='icon-time' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='start_at_date' name='start_at_date' id='start_at_date' type='text' style='width:90px;' data-date-format='dd-mm-yyyy'><i class='icon-calendar' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='icon-minus' style='margin: -2px 0 0 0; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='end_at_time' name='end_at_time' id='end_at_time' type='text' style='width:75px;'/><i class='icon-time' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i>&nbsp;&nbsp;&nbsp;<input class='end_at_date' name='end_at_date' id='end_at_date' type='text' style='width:90px;' data-date-format='dd-mm-yyyy'><i class='icon-calendar' style='margin: -1px 0 0 -19px; pointer-events: none; position: relative;'></i><a class='btn btn-link' id='btn-confirm' style='margin: -8px 0 0 0;'>Confirm</a><a class='btn btn-link' id='btn-cancel' style='margin: -8px 0 0 -20px;'>Cancel</a>\";
		$(this).html(str);
		$(this).children('#start_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true,
			defaultTime: startTime
		});
		$(this).children('#end_at_time').timepicker({
			minuteStep: 5,
			showInputs: false,
			disableFocus: true,
			defaultTime: endTime
		});
		$(this).children('#start_at_date').val(startDate);
		$(this).children('#end_at_date').val(endDate);
		
		$(this).children('#start_at_date').datepicker();
		$(this).children('#end_at_date').datepicker();
		
		$(this).children('#btn-confirm').click(function(){
			var newStartTime = $(this).parent().children('#start_at_time').val();
			var newEndTime = $(this).parent().children('#end_at_time').val();
			var newStartDate = $(this).parent().children('#start_at_date').val();
			var newEndDate = $(this).parent().children('#end_at_date').val();
			if (newStartTime != startTime || newEndTime != endTime || newStartDate != startDate || newEndDate != endDate) {
				var id = $(this).closest('.post').find('#data_id').val();
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id,
						startTime: newStartTime,
						startDate: newStartDate,
						endTime: newEndTime,
						endDate: newEndDate
					}
				});
			}
			var str = newStartDate.substr(3,2)+'/'+newStartDate.substr(0,2)+' '+newStartTime+' - '+newEndDate.substr(3,2)+'/'+newEndDate.substr(0,2)+' '+newEndTime;
			$(this).parent().html(str);
			$('.date-time').live('click', timeClick);
		})
		
		$(this).children('#btn-cancel').click(function(){
			$(this).parent().html(str_ori);
			$('.date-time').live('click', timeClick);
		})
	}
		
	function descriptionClick() {
		var description_ori=$(this).html();
		$(this).html('<div id=\"wrap\" style=\"margin-right: 20px;\"><textarea rows=\"4\" id=\"description\" style=\"width: 100%;\"></textarea></div>');
		$(this).find('#description').text(htmlDecode(description_ori));
		$(this).find('#description').focus();
		$(this).find('#description').focusout(function(){
			var description = $(this).val();
			var id = $(this).closest('.post').find('#data_id').val();
			$(this).parent().parent().html(htmlEncode(description));
			$('.content-description').live('click', descriptionClick);
			if (description_ori != htmlEncode(description))
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('updateAjax')."',
					data: {id: id, description: description}
				});
		});
		
		$('.content-description').die('click');
	}
		
	$('.content-title').live('click',titleClick);
		
	$('.date-time').live('click', timeClick);
	
	$('.content-description').live('click', descriptionClick);
	
	$('#todo-list .post').live('mouseenter', function (){
		$(this).children('.item-description').find('.social-bar').fadeIn('fast');
	});
	
	$('#todo-list .post').live('mouseleave', function (){
		$(this).children('.item-description').find('.social-bar').fadeOut('fast');
	});
	
	$('#todo-list .delete').live('click', function() {
		var elem = $(this).closest('.post');
		\$this=$(this);
		bootbox.confirm('Delete this todo?', function(result) {
		    if (result) {
				$.ajax({
					type: 'POST',
					url: '".$this->createUrl('delete')."/'+\$this.closest('.post').find('#data_id').val(),
					success: function(data) {
						setTimeout(function() {
							elem.slideUp();
						}, 500);
		
						$.ajax({
							type: 'POST',
							url: '".$this->createUrl('updateFiltersBar')."',
							data: $('#filter-form').serialize(),
							success: function (barInfo) {
								$('#tags-bar').html(barInfo.tagsBar);
								$('#concepts-bar').html(barInfo.conceptsBar);
							}
						});
					}
				});
				return false;
			}
		});
	});

//********* filter the todos in the todo-list
		
	$('#filter-form').submit(function(){
	    $.fn.yiiListView.update('todo-list', { 
	        data: $(this).serialize()
	    });
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateFiltersBar')."',
			data: $('#filter-form').serialize(),
			success: function (barInfo) {
				$('#tags-bar').html(barInfo.tagsBar);
				$('#concepts-bar').html(barInfo.conceptsBar);
			}
		});
	    return false;
	});

	$('.filter-btn-all').click(function(){
		$(this).addClass('active');
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-today').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('today');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-week').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-month').removeClass('active');
		
		$('#filter-form #interval').val('week');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-month').click(function(){
		$(this).addClass('active');
		$('.filter-btn-all').removeClass('active');
		$('.filter-btn-today').removeClass('active');
		$('.filter-btn-week').removeClass('active');
		
		$('#filter-form #interval').val('month');
		$('#filter-form').submit();
	});
		
	$('.filter-btn-all_status').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('All status');
		$(this).parent().children('.filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-done').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#filter-form #status').val('');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-undone').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('On going');
		$(this).parent().children('.filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-done').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#filter-form #status').val('".Todo::STATUS_UNDONE."');
		$('#filter-form').submit();
	
	});
	
	$('.filter-btn-done').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('Done');
		$(this).parent().children('.filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-canceled').attr('style', 'display:inline;');
	
		$('#filter-form #status').val('".Todo::STATUS_DONE."');
		$('#filter-form').submit();
	});
	
	$('.filter-btn-canceled').click(function(){
		$(this).attr('style', 'display:none;');
		$(this).parent().prev().children('.text').text('Canceled');
		$(this).parent().children('.filter-btn-undone').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-all_status').attr('style', 'display:inline;');
		$(this).parent().children('.filter-btn-done').attr('style', 'display:inline;');
	
		$('#filter-form #status').val('".Todo::STATUS_CANCELED."');
		$('#filter-form').submit();
	});
		
	$('.tag').live('mouseenter', function(){
		$(this).css('cursor','pointer');
		if (!$(this).hasClass('selected'))
			$(this).addClass('label-info');
	});
		
	$('.tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
		if (!$(this).hasClass('selected'))
			$(this).removeClass('label-info');
	});
		
	$('.tag').live('click', function(){
		var tag = htmlEncode($(this).text());
		if (tag.lastIndexOf('(') != -1)
			tag = $(this).text().substr(0, tag.lastIndexOf('('));
		if ($(this).attr('id') == 'all-tag')
			tag = '';
		
		$('#filter-form #tag').val(tag);
		$('#filter-form').submit();
		$('#tags-bar').show();
		$('#tags-bar > span').removeClass('label-info');
		$('#tags-bar > span').removeClass('selected');
		$('#tags-bar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('label-info');
		$('#tags-bar > span[name='.concat('\"').concat(tag).concat('\"').concat(']')).addClass('selected');
		
		if (tag=='') {
			$(this).addClass('label-info');
			$(this).addClass('selected');
		}
		
		if($('#filter-form #tag').val() != '')
			$('.filter-btn-tags .btn-link').text('Tag: '.concat($('#filter-form #tag').val()));
		else
			$('.filter-btn-tags .btn-link').text('All tags');
	});
		
	$('.concept').live('mouseenter', function(){
		$(this).css('cursor','pointer');
		if (!$(this).hasClass('selected'))
			$(this).addClass('label-success');
	});
		
	$('.concept').live('mouseleave', function(){
		$(this).removeClass('cursor');
		if (!$(this).hasClass('selected'))
			$(this).removeClass('label-success');
	});
		
	$('.concept').live('click', function(){
		var concept_id = $(this).attr('name');
		
		$('#filter-form #concept_id').val(concept_id);
		$('#filter-form').submit();
		$('#concepts-bar').show();
		$('#concepts-bar > span').removeClass('label-success');
		$('#concepts-bar > span').removeClass('selected');
		$('#concepts-bar > span[name='.concat('\"').concat(concept_id).concat('\"').concat(']')).addClass('label-success');
		$('#concepts-bar > span[name='.concat('\"').concat(concept_id).concat('\"').concat(']')).addClass('selected');
		
		var concept_name = htmlEncode($(this).text());
		if (concept_name.lastIndexOf('(') != -1)
			concept_name = concept_name.substr(0, concept_name.lastIndexOf('('));
		if ($(this).attr('id') == 'all-concept') {
			$(this).addClass('selected label-success');
			concept_name = 'All concepts';
		}
		$('.filter-btn-concepts .btn-link').text(concept_name);
		
	});

//********* edit tags of a todo
	
	function tagModal() {
		\$this = $(this);
		var id = $(this).closest('.post').find('#data_id').val();
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('createTagCanvas')."',
			data: {id: id},
			success: function (tagInfo) {
				$('#tag-canvas').find('.modal-body-tags').html(tagInfo.allTags);
				$('#tag-canvas').find('#add-tags-input').val(tagInfo.thisTag);
				$('#tag-canvas').find('#add-tags-input_ori').val(tagInfo.thisTag);

				var str = $.trim(tagInfo.thisTag);
				var arr = str.split(',');
				$('.modal-body-tags .pick-tag').each(function () {
					for (var i=0; i< arr.length; i++) {
						var item = $.trim(arr[i]);
						if ($(this).text() == item) {
							$(this).removeClass('label-info');
							break;
						}
					}
					if (i == arr.length)
						$(this).addClass('label-info');
				});

				$('#tag-canvas').find('#data_id').val(id);
				$('.btn-save-tags').click(function(){
				
					if($(this).hasClass('disabled'))
						return false;
					
					$('.btn-save-tags').addClass('disabled');
		
					var id = $('#tag-canvas').find('#data_id').val();
					var tags = $('#tag-canvas').find('#add-tags-input').val();

					$.ajax({
						type: 'POST',
						url: '".$this->createUrl('updateAjax')."',
						data: {id: id, tags: tags},
						success: function (html) {
							$('#tag-canvas').find('.alert-success').show();
							if (tags != '') {
								$.ajax({
									type: 'GET',
									url: '".$this->createUrl('getTags')."',
									data: {id: id},
									success: function (tags) {
										\$this.closest('.content-tag').html('<b>Tag:</b> '+tags+' <a data-toggle=\"modal\" href=\"#tag-canvas\"><i class=\"icon-pencil transparent50 btn-tag-edit\" style=\"display: none;\"></i></a>');
									}
								});
							} else {
								\$this.closest('.content-tag').html('<a data-toggle=\"modal\" href=\"#tag-canvas\" class=\"label label-info add-tag\">+ tag</a>');
							}

			                setTimeout(function() {
								$('#tag-canvas').find('.alert-success').hide();
								$('.modal.in').modal('hide');
			                }, 1200);
		
							$.ajax({
								type: 'POST',
								url: '".$this->createUrl('updateFiltersBar')."',
								data: $('#filter-form').serialize(),
								success: function (barInfo) {
									$('#tags-bar').html(barInfo.tagsBar);
									$('#concepts-bar').html(barInfo.conceptsBar);
								}
							});
						}
					});
					return false;
				});
			}
		});
		return false;
	}
		
	$('.add-tag').live('click', tagModal);
		
	$('.add-tag').live('mouseenter', function(){
		$(this).css('cursor','pointer');
	});
		
	$('.add-tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.content-tag').live('mouseenter', function(){
		$(this).find('.icon-pencil').show();
	});
		
	$('.content-tag').live('mouseleave', function(){
		$(this).find('.icon-pencil').hide();
	});
	
	$('.btn-tag-edit').live('click', tagModal);

	$('.btn-tag-edit .icon-pencil').live('mouseenter', function(){
		$(this).css('cursor','pointer');
	});

	$('.btn-tag-edit .icon-pencil').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.modal-body-tags .pick-tag').live('mouseenter', function(){
		if (!$(this).hasClass('label-info'))
			return false;
		$(this).css('cursor','pointer');
	});

	$('.modal-body-tags .pick-tag').live('mouseleave', function(){
		$(this).removeClass('cursor');
	});

	$('.modal-body-tags .pick-tag').live('click', function(){
		if (!$(this).hasClass('label-info'))
			return false;
		
		var ori = $.trim($('#tag-canvas').find('#add-tags-input').val());
		var newStr = '';
		if (ori != '') {
			newStr = ori;
			if (ori.substr(ori.length - 1) != ',')
				newStr += ','
			newStr += ' ' + $(this).text() + (', ');
		}
		else
			newStr = $(this).text() + (', ');

		$('#tag-canvas').find('#add-tags-input').val(newStr);
		$(this).removeClass('label-info');
		$(this).removeClass('cursor');
		tagsChanged();
	});
		
	$('#add-tags-input').keyup(function(){
		var str = $.trim($(this).val());
		var arr = str.split(',');
		$('.modal-body-tags .pick-tag').each(function () {
			for (var i=0; i< arr.length; i++) {
				var item = $.trim(arr[i]);
				if ($(this).text() == item) {
					$(this).removeClass('label-info');
					break;
				}
			}
			if (i == arr.length)
				$(this).addClass('label-info');
		});
		
		tagsChanged();
	});
		
	function tagsChanged() {
		var s_ori = $.trim($('#add-tags-input_ori').val());
		var s_new = $.trim($('#add-tags-input').val());
		
		if(s_ori.charAt(s_ori.length-1) == ',')
			s_ori = s_ori.substr(0, s_ori.length-1);
		
		if(s_new.charAt(s_new.length-1) == ',')
			s_new = s_new.substr(0, s_new.length-1);
		
		if(s_ori != s_new)
			$('.btn-save-tags').removeClass('disabled');
		else
			$('.btn-save-tags').addClass('disabled');
	}

//********* update the status of a todo
		
	$('.item-reactivate-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_UNDONE.",
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-done-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_DONE.",
					done_at: '".date('Y-m-d H:i:s', time())."'
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-redo-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_UNDONE.",
					done_at: ''
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
	
	$('.item-cancel-btn').live('click', function(){
		\$this = $(this);
		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('updateAjax')."',
			data: {
					id: \$this.closest('.post').find('#data_id').val(),
					status: ".Todo::STATUS_CANCELED.",
			},
			success: function (html) {
				$.fn.yiiListView.update('todo-list', {
					data: $(this).serialize()
				});
			},
		});
	});
");
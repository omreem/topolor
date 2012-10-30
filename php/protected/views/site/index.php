<?php $this->pageTitle=Yii::app()->name;?>
<div class="well">
	<ul class="nav nav-pills top-panel-fix" id="create-pills">
		<li class="active btn-status"><a class="btn-link">Status</a></li>
		<li class="btn-message"><a class="btn-link">Message</a></li>
		<li class="btn-ask"><a class="btn-link">Q&amp;A</a></li>
		<li class="btn-note"><a class="btn-link">Note</a></li>
		<li class="btn-todo"><a class="btn-link">Todo</a></li>
	</ul>
	<div id="create-panel-ask" style="margin: -10px 0 -20px 0; display: none;">
	<?php $this->renderPartial('/ask/_form', array('model' => $newAsk));?>
	</div>
	<div id="create-panel-note" style="margin: -10px 0 -20px 0; display: none;">
	<?php $this->renderPartial('/note/_form', array('model' => $newNote));?>
	</div>
	<div id="create-panel-todo" style="margin: -10px 0 -20px 0; display: none;">
	<?php $this->renderPartial('/todo/_form', array('model' => $newTodo));?>
	</div>
</div>

<?php Yii::app()->clientScript->registerScript('site-index-js', "
		
//********* change create-panel
				
	$('.btn-ask').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		$('.form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-ask').show();
			$('#create-panel-note').hide();
			$('#create-panel-todo').hide();
		}, 100);
	});
		
	$('.btn-note').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		$('.form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-ask').hide();
			$('#create-panel-note').show();
			$('#create-panel-todo').hide();
		}, 100);
	});
		
	$('.btn-todo').click(function(){
		$('#create-pills > li').removeClass('active');
		$(this).addClass('active');
		$('.form-rest').slideUp('fast');
		setTimeout(function() {
			$('#create-panel-ask').hide();
			$('#create-panel-note').hide();
			$('#create-panel-todo').show();
		}, 100);
	});
	
//********* create ask, note, todo
		
	$('#Ask_title').focus(function () {
		$('#ask-form .form-rest').slideDown();
	});
	
	$('#Ask_title, #Ask_description').keyup(function(event) {
		if ($('#Ask_title').val() != '' && $('#Ask_description').val() != '')
			$('#ask-form .btn-create').removeClass('disabled')
		else if ($('#Ask_title').val() == '' || $('#Ask_description').val() == '')
			$('#ask-form .btn-create').addClass('disabled')
	});
		
	$('#ask-form .btn-create').live('click', function(){
		if($(this).hasClass('disabled')) {
			if ($('#Ask_title').val() == '') {
				$('#Ask_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Ask_title').attr('placeholder','Title');
					$('#Ask_title').focus();
				}, 400);
			} else {
				$('#Ask_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Ask_description').attr('placeholder','Description');
					$('#Ask_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('ask/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#ask-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('ask-list', {
						data: $(this).serialize()
					});
					$('#ask-form').find('input').val('');
					$('#ask-form').find('textarea').val('');
					$('#Ask_title').attr('placeholder','Ask a question');
					$('#ask-form .btn-create').addClass('disabled');
                }, 400);
			}
		});
		return false;
	
	});
	
	$('#ask-form .btn-cancel').click(function (){
		$('#ask-form .form-rest').slideUp();
		$('#ask-form').find('textarea').val('');
		$('#ask-form').find('input').val('');
		$('#ask-form').find('#Ask_title').attr('placeholder','Ask a question');
	});	$('#Todo_title').focus(function () {
		$('#todo-form .form-rest').slideDown();
	});
//--
	$('#Note_title').focus(function () {
		$('#note-form .form-rest').slideDown();
	});
	
	$('#Note_title, #Note_description').keyup(function(event) {
		if ($('#Note_title').val() != '' && $('#Note_description').val() != '')
			$('#note-form .btn-create').removeClass('disabled')
		else if ($('#Note_title').val() == '' || $('#Note_description').val() == '')
			$('#note-form .btn-create').addClass('disabled')
	});
	
	$('#note-form .btn-create').click(function(){
		if($(this).hasClass('disabled')) {
			if ($('#Note_title').val() == '') {
				$('#Note_title').attr('placeholder','Please input a title!');
				setTimeout(function() {
					$('#Note_title').attr('placeholder','Title');
					$('#Note_title').focus();
				}, 400);
			} else {
				$('#Note_description').attr('placeholder','Please input a description!');
				setTimeout(function() {
					$('#Note_description').attr('placeholder','Description');
					$('#Note_description').focus();
				}, 400);
			}
			return;
		}
		
		\$this=$(this);
		\$form = \$this.closest('form');

		$.ajax({
			type: 'POST',
			url: '".$this->createUrl('note/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#note-form .form-rest').slideUp();
				setTimeout(function() {
					$.fn.yiiListView.update('note-list', {
						data: $(this).serialize()
					});
					$('#note-form').find('textarea').val('');
					$('#note-form').find('input').val('');
					$('#Note_title').attr('placeholder','Create a note');
					$('#note-form .btn-create').addClass('disabled');
                }, 400);
			}
		});
		return false;
	});
	
	$('#note-form .btn-cancel').click(function (){
		$('#note-form .form-rest').slideUp();
		$('#note-form').find('textarea').val('');
		$('#note-form').find('input').val('');
		$('#note-form').find('#Note_title').attr('placeholder','Create a note');
	});
//--	
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
			url: '".$this->createUrl('todo/create')."',
			data: \$form.serialize(),
			success: function (html) {
				$('#todo-form .form-rest').slideUp();
                setTimeout(function() {
					$.fn.yiiListView.update('todo-list', {
						data: $(this).serialize()
					});
					$('#todo-form').find('textarea').val('');
					$('#todo-form').find('input').val('');
					$('#Todo_title').attr('placeholder','Create a todo');
					$('#todo-form .btn-create').addClass('disabled');
                }, 400);
			}
		});
		return false;
	
	});	
	
	$('#todo-form .btn-cancel').click(function (){
		$('#todo-form .form-rest').slideUp();
		$('#todo-form').find('textarea').val('');
		$('#todo-form').find('input').val('');
		$('#todo-form').find('#Todo_title').attr('placeholder','Create a todo');
	});
");
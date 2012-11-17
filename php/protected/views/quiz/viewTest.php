<div class="well top-panel-fix">
	<div class="module-structure-panel">
	  	<span class="module-title">
	  		<a href="<?php echo Yii::app()->baseUrl.'/index.php/concept/'.$moduleId;?>" class="btn-link"><?php echo $moduleTitle;?></a>
  			 &raquo; <?php echo $type;?>
  		</span>
  </div>
</div>

<div class="well">
	<div><?php echo CHtml::link ('Cancel', CController::createUrl('/concept/'.$moduleId), array('class'=>'btn')); ?></div>
	<hr>
	<form novalidate="novalidate" class="quiz-form" autocomplete="off" method="POST" action="<?php echo CController::createUrl('/quiz/quizSubmit');?>">
		<ol>
		<?php foreach ($questions as $question):?>
			<li>
				<div>
					<p><b><?php echo $question['description'];?></b></p>
					<?php foreach ($question['options'] as $option) :?>
					<p>
					<input type="radio" name='<?php echo "q".$question["id"];?>' value='<?php echo $option["opt"]?>'>
					<?php echo $option['val'];?>
					</p>
					<?php endforeach;?>
				</div>
					<span class="error-msg"></span>
				<hr>
			</li>
		<?php endforeach;?>
		</ol>
		<input type="hidden" name="quiz_id" value="<?php echo $quiz_id;?>">
		<button class="btn" type="submit" name="submit" style="margin-left: 10px;">Submit</button>
	</form>
</div>
<div class="hero-unit top-panel-fix">
  <h1>Module Center</h1>
  <p>This is balabala...</p>
  <p>Some Info</p>
  <p>Other Instructions</p>
  <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
</div>
<?php $count = count($moduleArr);

for ($i=0;$i<$count;$i++) :
	$description = $moduleArr[$i]['description'];
	
	$descriptionShort = strlen($description) > 160 ? substr($description, 0, 160).'...' : $description;
	
	if ($i % 3 == 0) :?>
	<div class="row-fluid">
		<ul class="thumbnails">
	<?php endif;?>
			<li class="span4">
				<div class="thumbnail">
					<img src="http://placehold.it/300x200" alt="">
					<div class="caption">
						<h3><?php echo $moduleArr[$i]['title'];?></h3>
						<p style='min-height:100px; height:auto !important; height:100px;'><?php echo $descriptionShort;?></p>
						<div id="modal-<?php echo $moduleArr[$i]['id'];?>" class="modal hide fade in" style="display: none; ">
							<div class="modal-header">
								<a class="close" data-dismiss="modal">×</a>
								<h3>Module: <?php echo $moduleArr[$i]['title'];?></h3>
							</div>
							<div class="modal-body">
								<p><?php echo $description;?></p>		        
							</div>
							<div class="modal-footer">
								<?php echo CHtml::ajaxLink ("Confirm",
									CController::createUrl('/module/register'), 
									array(
											'type' => 'POST',
											'data' => array('id'=>$moduleArr[$i]['id']),
											'success' => 'function(data){
												window.location = "'.Yii::app()->homeUrl.'/module/'.$moduleArr[$i]['id'].'";
											}',
									),
									array('class' =>'btn btn-primary')
								);?>
								<a href="#" class="btn" data-dismiss="modal">Cancel</a>
							</div>
						</div>
						<p>
						<?php if (LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
																array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$moduleArr[$i]['id'])) == null ) {?>
							<a data-toggle="modal" href="#modal-<?php echo $moduleArr[$i]['id'];?>" class="btn btn-primary">Register</a>
							<a href="<?php echo CController::createUrl('module/preview/'.$moduleArr[$i]['id']);?>" class="btn">Preview &raquo;</a>
						<?php } else {?>
							<a href="<?php echo Yii::app()->homeUrl.'/module/'.$moduleArr[$i]['id'];?>" class="btn btn-primary">Get In</a>
						<?php } ?>
						</p>
					</div>
				</div>
			</li>
	<?php if ($i % 3 == 2) :?>
		</ul>
	</div>
	<?php endif;
endfor;?>


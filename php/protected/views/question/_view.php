<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('author_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->author)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('concept_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->concept)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('correct_answer')); ?>:
	<?php echo GxHtml::encode($data->correct_answer); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('create_at')); ?>:
	<?php echo GxHtml::encode($data->create_at); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('update_at')); ?>:
	<?php echo GxHtml::encode($data->update_at); ?>
	<br />

</div>
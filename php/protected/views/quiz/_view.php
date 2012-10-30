<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('learner_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->learner)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('concept_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->concept)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('score')); ?>:
	<?php echo GxHtml::encode($data->score); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('create_at')); ?>:
	<?php echo GxHtml::encode($data->create_at); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('done_at')); ?>:
	<?php echo GxHtml::encode($data->done_at); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('lastaccess_at')); ?>:
	<?php echo GxHtml::encode($data->lastaccess_at); ?>
	<br />

</div>
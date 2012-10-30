<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('question_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->question)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('opt')); ?>:
	<?php echo GxHtml::encode($data->opt); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('val')); ?>:
	<?php echo GxHtml::encode($data->val); ?>
	<br />

</div>
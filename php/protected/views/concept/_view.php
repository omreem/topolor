<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('author_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->author)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('title')); ?>:
	<?php echo GxHtml::encode($data->title); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('root')); ?>:
	<?php echo GxHtml::encode($data->root); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('lft')); ?>:
	<?php echo GxHtml::encode($data->lft); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('rgt')); ?>:
	<?php echo GxHtml::encode($data->rgt); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('level')); ?>:
	<?php echo GxHtml::encode($data->level); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('create_at')); ?>:
	<?php echo GxHtml::encode($data->create_at); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('update_at')); ?>:
	<?php echo GxHtml::encode($data->update_at); ?>
	<br />
	*/ ?>

</div>
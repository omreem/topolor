<div class="post clearfix well" style="margin-bottom: 20px;">
	<span class="label label-info" style="margin-right: 36px;"><?php echo $data['name']?></span>
	<span style="margin-right: 36px;">Frequency: <?php echo $data['frequency']?></span>
	<span style="margin-right: 36px;">Users: <?php echo $data['sum_user']?></span>
	<span>Create At: <?php echo Helpers::datatime_trim($data['create_at']);?></span>
	<input id="tag_name" type="hidden" value='<?php echo $data['name'];?>'/>
</div><!-- /.well -->
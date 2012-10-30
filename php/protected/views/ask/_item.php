<div class="content-title">
	<a href="<?php echo $this->createUrl('/ask').'/'.$data['id'];?>"><?php echo GxHtml::encode($data['title']);?></a>
</div>
<p class="item-description">
	<span class="date-time"><?php echo Helpers::datatime_feed($data['create_at']);?></span>
</p>
<hr>

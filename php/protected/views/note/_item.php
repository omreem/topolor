<div id="note_item_<?php echo $data['id'];?>" class="modal hide fade in" style="display: none;" data-backdrop="static">
	<div class="modal-body">
		<p>
			<span class="content-title"><b><?php echo GxHtml::encode($data['title']);?></b></span><br>
			<span class="date-time"><?php echo Helpers::datatime_feed($data['create_at']);?></span>
		</p>
		<p><?php echo GxHtml::encode($data['description']);?></p>		        
	</div>
	<div class="modal-footer">
		<input type="hidden" id="note_id" value="<?php echo $data['id'];?>">
        <a class="btn pull-left note-update-btn">Update</a>
		<a class="btn pull-left note-delete-btn">Delete</a>
		<a class="btn pull-left note-form-confirm" style="display:none;">Confirm</a>
		<a class="btn pull-left note-form-cancel" style="display:none;">Cancel</a>
		<a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</div>

<div class="content-title">
	<a data-toggle="modal" href="#note_item_<?php echo $data['id'];?>"><?php echo GxHtml::encode($data['title']);?></a>
</div>
<p class="item-description">
	<span class="date-time"><?php echo Helpers::datatime_feed($data['create_at']);?></span>
</p>
<hr>

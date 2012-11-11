<p class="date-time">Done at: <?php echo Helpers::datatime_feed($quizDoneAt);?></p>
<hr>
<ol>
<?php foreach ($questions as $question) {?>
<li>
	<p><b><?php echo $question['description']; ?></b></p>
	<p><?php 
		foreach ($question['options'] as $option) {
			echo $option['opt'].': '.$option['val'].'<br>';
		}
	?></p>
	<p <?php if ($question['answer'] != $question['correct_answer']) echo 'style="color: red;"';?>>You answer is: <?php echo $question['answer'];?><br>
	Correct answer is : <?php echo $question['correct_answer']?>
	</p>
	<hr>
</li>
<?php } ?>
</ol>
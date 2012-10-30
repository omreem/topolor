<?php

Yii::import('application.models._base.BaseLearnerConcept');

class LearnerConcept extends BaseLearnerConcept
{
	const STATUS_INPROGRESS=1;
	const STATUS_COMPLETED=2;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
}
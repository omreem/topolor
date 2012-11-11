<?php

Yii::import('application.models._base.BaseQuiz');

class Quiz extends BaseQuiz
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	protected function beforeSave() {
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->create_at = date('Y-m-d H:i:s', time());
				$this->learner_id = Yii::app()->user->id;
			}
			else
				$this->lastaccess_at = date('Y-m-d H:i:s', time());
			return true;
		}
		else
			return false;
	}
	
	public function getTagLabels() {
		$labels='';
		foreach(Tag::string2array($this->concept->tags) as $tag)
			$labels.=CHtml::tag('span', array('class'=>'label label-info tag selected'), CHtml::encode($tag));
		return $labels;
	}
}
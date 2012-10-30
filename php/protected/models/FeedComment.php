<?php

Yii::import('application.models._base.BaseFeedComment');

class FeedComment extends BaseFeedComment
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
				$this->user_id = Yii::app()->user->id;
			}
			return true;
		}
		else
			return false;
	}
}
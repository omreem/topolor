<?php

Yii::import('application.models._base.BaseFeed');

class Feed extends BaseFeed
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
			else
				$this->update_at = date('Y-m-d H:i:s', time());
			return true;
		}
		else
			return false;
	}
	
	public function getFavoriteCount() {
		$sql='select count(id)'
		
		.' from'
		.' tpl_favorite'
		
		.' where'
		.' of=\'feed\''
		.' and of_id=\''.$this->id.'\'';
		
		$count=Yii::app()->db->createCommand($sql)->queryScalar();
		
		return $count;
	}
	
	public function getShareCount() {
		$sql='select count(id)'
		
		.' from'
		.' tpl_feed'
		
		.' where'
		.' of=\'feed\''
		.' and from_id=\''.$this->id.'\'';
		
		$count=Yii::app()->db->createCommand($sql)->queryScalar();
		
		return $count;
	}
	
	public function isMyFavorite() {
		$sql='select count(id)'
		
		.' from'
		.' tpl_favorite'
		
		.' where'
		.' of=\'feed\''
		.' and of_id=\''.$this->id.'\''
		.' and user_id='.Yii::app()->user->id;
		
		$count=Yii::app()->db->createCommand($sql)->queryScalar();
		
		return $count == 1 ? true : false;
	}
	
	public function getOf() {
		switch ($this->of) {
			case 'feed':
				break;
			case 'ask':
				break;
			case 'note':
				break;
			case 'todo':
				break;
			default:
				return null;
		}
	}
}
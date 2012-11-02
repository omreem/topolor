<?php

Yii::import('application.models._base.BaseMessage');

class Message extends BaseMessage
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	protected function beforeSave() {
		if(parent::beforeSave()) {
			if($this->isNewRecord) {
				$this->create_at = date('Y-m-d H:i:s', time());
				$this->user_id = Yii::app()->user->id;
			}
			return true;
		}
		else
			return false;
	}
	
	public function isFirst() {
		return $this->to_message_id == null;
	}
	
	public function isLast() {
		return $this->order == count($this->all) - 1;
	}
	
	public function getAll() {
		$arr = array();
		if ($this->isFirst()) {
			array_push($arr, $this);
			return array_merge(Message::model()->findAll('to_message_id=:to_message_id order by create_at DESC', array(':to_message_id'=>$this->id)), $arr);
		} else {
			array_push($arr, $this->first);
			return array_merge(Message::model()->findAll('to_message_id=:to_message_id order by create_at DESC', array(':to_message_id'=>$this->to_message_id)), $arr);
		}
	}
	
	public function getOrder() {
		if ($this->isFirst())
			return 0;
		else
			return array_search($this, $this->all);
	}
	
	public function getFirst() {
		if ($this->isFirst())
			return $this;
		else
			return Message::model()->findByPk($this->to_message_id);
	}
	
	public function getLast() {
		if ($this->isLast() || $this->isFirst())
			return $this;
		else
			return Message::model()->find('to_message_id=:to_message_id order by create_at desc', array(':to_message_id'=>$this->to_message_id));
	}
	
	public function getPrev() {
		if ($this->isFirst())
			return null;
		else
			return $this->all[$this->order - 1];
	}
	
	public function getNext() {
		if ($this->isLast())
			return null;
		else
			return $this->all[$this->order + 1];
	}
	
	public function getCount() {
		if ($this->isFirst())
			return 1 + Yii::app()->db->createCommand('select count(*) from tpl_message where to_message_id='.$this->id)->queryScalar();
		else 
			return 1 + Yii::app()->db->createCommand('select count(*) from tpl_message where to_message_id='.$this->to_message_id)->queryScalar();
	}
	
	public function hasNew() {
		if ($this->isFirst())
			return 0 != Yii::app()->db->createCommand('select count(*) from tpl_message where to_message_id='.$this->id)->queryScalar();
		else
			return 0 != Yii::app()->db->createCommand('select count(*) from tpl_message where to_message_id='.$this->to_message_id)->queryScalar();
	}
}
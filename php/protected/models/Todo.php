<?php

Yii::import('application.models._base.BaseTodo');

class Todo extends BaseTodo
{
	private $_oldTags;
	
	const STATUS_UNDONE=0;
	const STATUS_DONE=1;
	const STATUS_CANCELED=2;
	
	const CATEGORY_SCHEDULE=1;
	const CATEGORY_DELEGATE=2;
	
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
				$this->update_at = date('Y-m-d H:i:s', time());
			return true;
		}
		else
			return false;
	}
	
	/**
	 * @return array a list of links that point to the post list filtered by every tag of this post
	 */
	public function getTagLinks() {
		$links=array();
		foreach(Tag::string2array($this->tags) as $tag)
			$links[]=CHtml::link(CHtml::encode($tag), array('todo/index', 'tag'=>$tag), array('class'=>'label label-info tag selected'));
		return $links;
	}
	
	public function getTagLabels() {
		$labels=array();
		foreach(Tag::string2array($this->tags) as $tag)
			$labels[]=CHtml::tag('span', array('class'=>'label label-info tag selected'), CHtml::encode($tag));
		return $labels;
	}
	
	/**
	 * Normalizes the user-entered tags.
	 */
	public function normalizeTags($attribute,$params) {
		$arr = Tag::string2array($this->tags);
		sort($arr);
		$this->tags=Tag::array2string(array_unique($arr));
	}
	
	/**
	 * This is invoked when a record is populated with data from a find() call.
	 */
	protected function afterFind() {
		parent::afterFind();
		$this->_oldTags=$this->tags;
	}
	
	/**
	 * This is invoked after the record is saved.
	 */
	protected function afterSave() {
		parent::afterSave();
		Tag::model()->updateFrequency($this->_oldTags, $this->tags, 'todo');
	}
	
	/**
	 * This is invoked after the record is deleted.
	 */
	protected function afterDelete() {
		parent::afterDelete();
		Tag::model()->updateFrequency($this->tags, '', 'todo');
	}
}
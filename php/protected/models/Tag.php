<?php

Yii::import('application.models._base.BaseTag');

class Tag extends BaseTag
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
	
	/**
	 * Returns tag names and their corresponding weights.
	 * Only the tags with the top weights will be returned.
	 * @param integer the maximum number of tags that should be returned
	 * @param string the entity of tags that should be returned
	 * @param integer the owner id of tags that should be returned
	 * @return array weights indexed by tag names.
	 */
	public function findTagWeights($limit=20, $of='', $owner_id='') {
		$criteria = new CDbCriteria;
		if ($of != '')
			$criteria->compare('of', $of);
		if ($owner_id != '')
			$criteria->compare('user_id', $owner_id);
		$criteria->order = 'frequency DESC';
		$criteria->limit = $limit;
		
		$models=$this->findAll($criteria);
	
		$total=0;
		foreach($models as $model)
			$total+=$model->frequency;
	
		$tags=array();
		if($total>0)
		{
			foreach($models as $model)
				$tags[$model->name]=8+(int)(16*$model->frequency/($total+10));
			ksort($tags);
		}
		return $tags;
	}
	
	public function findTags($of='', $owner_id='', $withFrequency=true, $limit=20) {
		$criteria = new CDbCriteria;
		if ($of != '')
			$criteria->compare('of', $of);
		if ($owner_id != '') {
			$criteria->compare('user_id', $owner_id);
			if ($withFrequency)
				$criteria->select = 'name, frequency';
			else
				$criteria->select = 'name';
			$criteria->order = 'frequency DESC';
		} else {
			$criteria->group = 'name';
			if ($withFrequency)
				$criteria->select = 'name, SUM(frequency) as frequency';
			else
				$criteria->select = 'name';
			$criteria->order = 'frequency DESC';
		}
		
		$criteria->limit = $limit;
		
		return $this->findAll($criteria);
	}
	
	/**
	 * Suggests a list of existing tags matching the specified keyword.
	 * @param string the keyword to be matched
	 * @param integer maximum number of tags to be returned
	 * @return array list of matching tag names
	 */
	public function suggestTags($keyword, $of='', $limit=20) {
		$criteria = new CDbCriteria;
		if ($of != '')
			$criteria->compare('of', $of);
		$criteria->addSearchCondition('name', '%'.strtr($keyword,array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%', false);
		$criteria->compare('user_id', Yii::app()->user->id);
		$criteria->order = 'frequency DESC, Name';
		$criteria->limit = $limit;
		
		$tags=$this->findAll($criteria);
		$names=array();
		foreach($tags as $tag)
			$names[]=$tag->name;
		return $names;
	}
	
	public static function string2array($tags) {
		return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
	}
	
	public static function array2string($tags) {
		return implode(', ',$tags);
	}
	
	public function updateFrequency($oldTags, $newTags, $of='') {
		$oldTags=self::string2array($oldTags);
		$newTags=self::string2array($newTags);
		$this->addTags(array_values(array_diff($newTags,$oldTags)), $of);
		$this->removeTags(array_values(array_diff($oldTags,$newTags)), $of);
	}
	
	public function addTags($tags, $of='') {
		$criteria=new CDbCriteria;
		$criteria->addInCondition('name',$tags);
		if ($of != '')
			$criteria->compare('of', $of);
		$criteria->compare('user_id', Yii::app()->user->id);
		$this->updateCounters(array('frequency'=>1),$criteria);
		foreach($tags as $name)
		{
			if(!$this->exists('name=:name and user_id=:user_id and of=:of',array(':name'=>$name, ':user_id'=>Yii::app()->user->id, ':of'=>$of)))
			{
				$tag=new Tag;
				$tag->name=$name;
				$tag->frequency=1;
				if ($of != '')
					$tag->of=$of;
				$tag->save();
			}
		}
	}
	
	public function removeTags($tags, $of='') {
		if(empty($tags))
			return;
		$criteria=new CDbCriteria;
		$criteria->addInCondition('name',$tags);
		if ($of != '')
			$criteria->compare('of', $of);
		$criteria->compare('user_id', Yii::app()->user->id);
		$this->updateCounters(array('frequency'=>-1),$criteria);
		$this->deleteAll('frequency<=0');
	}
}
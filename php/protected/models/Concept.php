<?php

Yii::import('application.models._base.BaseConcept');

class Concept extends BaseConcept
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function behaviors()
	{
		return array(
			'nestedSetBehavior'=>array(
				'class'=>'ext.behaviors.model.trees.NestedSetBehavior',
				'hasManyRoots'=>true,
			),
		);
	}

	protected function beforeSave() {
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->create_at = date('Y-m-d H:i:s', time());
				$this->author_id = Yii::app()->user->id;
			}
			else
				$this->update_at = date('Y-m-d H:i:s', time());
			return true;
		}
		else
			return false;
	}
	
	public function getTagLabels() {
		$labels=array();
		foreach(Tag::string2array($this->tags) as $tag)
			$labels[]=CHtml::tag('span', array('class'=>'label label-info tag selected'), CHtml::encode($tag));
		return $labels;
	}
	
	public function isModule() {
		return $this->root == $this->id;
	}
	
	public function getModule() {
		if ($this->isModule())
			return $this;
		else
			return Concept::model()->find('root=:root and id=root', array(':root'=>$this->root));
	}
	
	public function getPreviousConcept() {
		if ($this->isRoot()) {
			$prev = null;
		} else {
			$prev = $this->prev()->find();
			if ($prev != null) {
				while (!$prev->isLeaf()) {
					$prev = Concept::model()->find('rgt=:rgt and root=:root', array(':rgt'=>$prev->rgt-1, ':root'=>$this->root));
				}
			} else {
				$prev = $this->parent()->find();
			}
		}
		return $prev->isRoot() ? null : $prev;
	}
	
	public function getNextConcept() {
		if (!$this->isLeaf()) {
			$next = Concept::model()->find('lft=:lft and root=:root', array(':lft'=>$this->lft+1, ':root'=>$this->root));
		} else {
			$next = $this->next()->find();
			$fd = false;
			if ($next == null) {
				$p = $this->parent()->find();
				while (!$p->isRoot()) {
					$next = $p->next()->find();
					if ($next != null) {
						$fd = true;
						break;
					}
					$pp = $p;
					$p = $p->parent()->find();
						
				}
				if (!$fd && $p->rgt-1 != $pp->rgt)
					$next = Concept::model()->find('rgt=:rgt and root=:root', array(':rgt'=>$p->rgt-1, ':root'=>$this->root));
			}
		}
		
		return $next;
	}
}
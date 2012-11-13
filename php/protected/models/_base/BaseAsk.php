<?php

/**
 * This is the model base class for the table "{{ask}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Ask".
 *
 * Columns in table "{{ask}}" available as properties of the model,
 * followed by relations of table "{{ask}}" available as properties of the model.
 *
 * @property string $id
 * @property string $learner_id
 * @property string $title
 * @property string $description
 * @property string $concept_id
 * @property string $tags
 * @property string $create_at
 * @property string $update_at
 *
 * @property Answer[] $answers
 * @property Concept $concept
 * @property User $learner
 */
abstract class BaseAsk extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{ask}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Ask|Asks', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('title, description', 'required'),
			array('learner_id, concept_id', 'length', 'max'=>10),
			array('update_at', 'safe'),
			array('concept_id, update_at', 'default', 'setOnEmpty' => true, 'value' => null),
			array('tags', 'match', 'pattern'=>'/^[\w\s,]+$/', 'message'=>'Tags can only contain word characters.'),
			array('tags', 'normalizeTags'),
			array('tags, update_at', 'safe'),
			array('learner_id, title, description, concept_id, tags, create_at, update_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'answers' => array(self::HAS_MANY, 'Answer', 'ask_id', 'order' => 'answers.create_at DESC', 'condition'=>'answers.learner_id='.Yii::app()->user->id,),
			'answersAll' => array(self::HAS_MANY, 'Answer', 'ask_id', 'order' => 'answersAll.create_at DESC'),
			'answersRecent' => array(self::HAS_MANY, 'Answer', 'ask_id', 'order' => 'answersRecent.create_at DESC', 'limit'=>5),
			'answerCount' => array(self::STAT, 'Answer', 'ask_id'),
			'concept' => array(self::BELONGS_TO, 'Concept', 'concept_id'),
			'learner' => array(self::BELONGS_TO, 'User', 'learner_id'),

// foreign key needed
//			'countFavorited' => array(self::STAT, 'Favorite', 'of_id', 'condition'=>'of=\'ask\''),
//			'countShared' => array(),
//			'countAnswered' => array(),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'learner_id' => null,
			'title' => Yii::t('app', 'Title'),
			'description' => Yii::t('app', 'Description'),
			'concept_id' => null,
			'tags' => Yii::t('app', 'Tags'),
			'create_at' => Yii::t('app', 'Create At'),
			'update_at' => Yii::t('app', 'Update At'),
			'answers' => null,
			'concept' => null,
			'learner' => null,
		);
	}

	public function search($filter_by='', $tag='', $concept_id='', $isAnswered='', $order_by='', $pageSize=20) {
		$criteria = new CDbCriteria;
		
		$criteria->compare('id', $this->id, true);
		$criteria->compare('learner_id', $this->learner_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('concept_id', $this->concept_id);
		$criteria->compare('tags', $this->tags, true);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('update_at', $this->update_at, true);
	
		if ($filter_by != '') {
			if ($filter_by == 'myquestions')
				$criteria->compare('learner_id', Yii::app()->user->id);
			elseif ($filter_by == 'myanswers') {
				$asks = Yii::app()->db->createCommand()
					->selectDistinct('ask_id')
					->from('tpl_answer')
					->where('learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
					->queryAll();
				$askArr=array();
				foreach ($asks as $ask) {
					array_push($askArr,$ask['ask_id']);
				}
				
				$criteria->addInCondition('id', $askArr);
			}
		}
		
		if ($isAnswered != '') {
			$asks = Yii::app()->db->createCommand()
				->selectDistinct('ask_id')
				->from('tpl_answer')
				->queryAll();
			$askArr=array();
			foreach ($asks as $ask) {
				array_push($askArr,$ask['ask_id']);
			}
		
			if ($isAnswered == 'answered')
				$criteria->addInCondition('id', $askArr);
			else if ($isAnswered == 'unanswered')
				$criteria->addNotInCondition('id', $askArr);
		}
		
		if($tag != '')
			$criteria->addSearchCondition('tags',$tag);
		
		if ($concept_id != '') {
			$cArr = array();
			array_push($cArr, $concept_id);
			$criteria->addInCondition('concept_id', $cArr);
		}
		
		$criteria->order = 'create_at DESC';
		
		if ($order_by == 'answered') {
			$criteria->select = 't.*';
			$criteria->join = 'LEFT JOIN tpl_answer ON t.id = tpl_answer.ask_id';
			$criteria->group = 't.id';
			$criteria->order = 'count(t.id) DESC';
		} else if ($order_by == 'shared') {
			$criteria->select = 't.*';
			$criteria->join = 'LEFT JOIN tpl_feed ON t.id = tpl_feed.of_id';
			$criteria->compare('of', 'ask');
			$criteria->group = 't.id';
			$criteria->order = 'count(t.id) DESC';
		} else if ($order_by == 'favorited') {
			$criteria->select = 't.*';
			$criteria->join = 'LEFT JOIN tpl_favorite ON t.id = tpl_favorite.of_id';
			$criteria->compare('of', 'ask');
			$criteria->group = 't.id';
			$criteria->order = 'count(t.id) DESC';
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
					'pageSize' => $pageSize,
			),
		));
	}
}
<?php

/**
 * This is the model base class for the table "{{todo}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Todo".
 *
 * Columns in table "{{todo}}" available as properties of the model,
 * followed by relations of table "{{todo}}" available as properties of the model.
 *
 * @property string $id
 * @property string $learner_id
 * @property string $title
 * @property string $description
 * @property string $concept_id
 * @property string $tags
 * @property integer $category
 * @property integer $status
 * @property string $start_at
 * @property string $end_at
 * @property string $done_at
 * @property string $create_at
 * @property string $update_at
 *
 * @property User $learner
 * @property Concept $concept
 */
abstract class BaseTodo extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{todo}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Todo|Todos', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('title, status', 'required'),
			array('category, status', 'numerical', 'integerOnly'=>true),
			array('learner_id, concept_id', 'length', 'max'=>10),
			array('tags', 'match', 'pattern'=>'/^[\w\s,]+$/', 'message'=>'Tags can only contain word characters.'),
			array('tags', 'normalizeTags'),
			array('tags, update_at', 'safe'),
			array('description, tags, start_at, end_at, done_at, update_at', 'safe'),
			array('description, concept_id, tags, category, status, start_at, end_at, done_at, update_at', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, learner_id, title, description, concept_id, tags, category, status, start_at, end_at, done_at, create_at, update_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'learner' => array(self::BELONGS_TO, 'User', 'learner_id'),
			'concept' => array(self::BELONGS_TO, 'Concept', 'concept_id'),
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
			'category' => Yii::t('app', 'Category'),
			'status' => Yii::t('app', 'Status'),
			'start_at' => Yii::t('app', 'Start At'),
			'end_at' => Yii::t('app', 'End At'),
			'done_at' => Yii::t('app', 'Done At'),
			'create_at' => Yii::t('app', 'Create At'),
			'update_at' => Yii::t('app', 'Update At'),
			'learner' => null,
			'concept' => null,
		);
	}

	public function search($interval, $tag='', $concept_id='', $pageSize=10) {
		$criteria = new CDbCriteria;
		
		if ($interval!='') {
			$now = date('Y-m-d');
			switch ($interval) {
				case 'today': {
					$criteria->addCondition("start_at >= '".date('Y-m-d')."'", 'AND');
					$criteria->addCondition("start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."'", 'AND');
					break;
				}
				case 'week': {
					$criteria->addCondition("start_at >= '".date('Y-m-d', strtotime('last monday'))."'", 'AND');
					$criteria->addCondition("start_at < '".date('Y-m-d', strtotime('monday'))."'", 'AND');
					break;
				}
				case 'month': {
					$criteria->addCondition("start_at >= '".date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y")))."'", 'AND');
					$criteria->addCondition("start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")+1, 1, date("Y")))."'", 'AND');
					break;
				}
			}
		}
		
		if($tag != '')
			$criteria->addSearchCondition('tags',$tag);
		
		if ($concept_id != '') {
			$cArr = array();
			array_push($cArr, $concept_id);
			$criteria->addInCondition('concept_id', $cArr);
		}

		$criteria->compare('id', $this->id, true);
		$criteria->compare('learner_id', $this->learner_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('concept_id', $this->concept_id);
		$criteria->compare('tags', $this->tags, true);
		$criteria->compare('category', $this->category);
		$criteria->compare('status', $this->status);
		$criteria->compare('start_at', $this->start_at, true);
		$criteria->compare('end_at', $this->end_at, true);
		$criteria->compare('done_at', $this->done_at, true);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('update_at', $this->update_at, true);
		
		$criteria->order = 'start_at';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
					'pageSize' => $pageSize,
			),
		));
	}
}
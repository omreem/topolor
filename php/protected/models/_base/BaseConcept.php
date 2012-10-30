<?php

/**
 * This is the model base class for the table "{{concept}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Concept".
 *
 * Columns in table "{{concept}}" available as properties of the model,
 * followed by relations of table "{{concept}}" available as properties of the model.
 *
 * @property string $id
 * @property string $author_id
 * @property string $title
 * @property string $description
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $create_at
 * @property string $update_at
 *
 * @property Ask[] $asksOwned
 * @property Ask[] $asks
 * @property integer $askCount
 * @property User $author
 * @property ConceptComment[] $comments
 * @property mixed $resources
 * @property mixed $learners
 * @property Note[] $notesOwned
 * @property Note[] $notes
 * @property integer $noteCount
 * @property ConceptComment[] $commentsOwned
 * @property ConceptComment[] $comments
 * @property integer $commentCount
 * @property Question[] questionsOwned
 * @property Question[] $questions
 * @property integer questionCount
 * @property Quiz[] $quizzesOwned
 * @property Quiz[] $quizzes
 * @property integer $quizCount
 */
abstract class BaseConcept extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{concept}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Concept|Concepts', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('title, description', 'required'),
			array('level', 'numerical', 'integerOnly'=>true),
			array('author_id, root, lft, rgt', 'length', 'max'=>10),
			array('title', 'length', 'max'=>256),
			array('update_at', 'safe'),
			array('root, update_at', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, author_id, title, description, root, lft, rgt, level, create_at, update_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'todosOwnedUndone' => array(self::HAS_MANY, 'Todo', 'concept_id',
					'order' => 'todosOwnedUndone.start_at',
					'condition'=>'todosOwnedUndone.status='.Todo::STATUS_UNDONE.' and todosOwnedUndone.learner_id='.Yii::app()->user->id,),
			'todosOwnedUndoneCount' => array(self::STAT, 'Todo', 'concept_id',
					'condition'=>'status='.Todo::STATUS_UNDONE.' and learner_id='.Yii::app()->user->id,),
			'todosOwned' => array(self::HAS_MANY, 'Todo', 'concept_id',
					'order' => 'todosOwned.start_at',
					'condition'=>'todosOwned.learner_id='.Yii::app()->user->id,),
			'todoOwnedCount' => array(self::STAT, 'Todo', 'concept_id',
					'condition'=>'learner_id='.Yii::app()->user->id,),
			'asksOwned' => array(self::HAS_MANY, 'Ask', 'concept_id',
					'order' => 'asksOwned.create_at DESC',
					'condition'=>'asksOwned.learner_id='.Yii::app()->user->id,),
			'asks' =>  array(self::HAS_MANY, 'Ask', 'concept_id',
					'order' => 'asks.create_at DESC'),
			'askCount' => array(self::STAT, 'Ask', 'concept_id'),
			'notesOwned' => array(self::HAS_MANY, 'Note', 'concept_id',
					'order' => 'notesOwned.create_at DESC',
					'condition'=>'notesOwned.learner_id='.Yii::app()->user->id,),
			'notes' => array(self::HAS_MANY, 'Note','concept_id',
					'order' => 'notes.create_at DESC'),
			'noteCount' => array(self::STAT, 'Note', 'concept_id'),
			'commentsOwned' => array(self::HAS_MANY, 'ConceptComment', 'concept_id',
					'order' => 'commentsOwned.create_at DESC',
					'condition'=>'commentsOwned.learner_id='.Yii::app()->user->id,),
			'comments' =>  array(self::HAS_MANY, 'ConceptComment', 'concept_id',
					'order' => 'comments.create_at DESC'),
			'commentCount' => array(self::STAT, 'ConceptComment', 'concept_id'),
			'questionsOwned' => array(self::HAS_MANY, 'Question', 'concept_id',
					'order' => 'questionsOwned.create_at DESC',
					'condition'=>'questionsOwned.learner_id='.Yii::app()->user->id,),
			'questions' =>  array(self::HAS_MANY, 'Question', 'concept_id',
					'order' => 'questions.create_at DESC'),
			'questionCount' => array(self::STAT, 'Question', 'concept_id'),
			'quizzesOwned' => array(self::HAS_MANY, 'Quiz', 'concept_id',
					'order' => 'quizzesOwned.create_at DESC',
					'condition'=>'quizzesOwned.learner_id='.Yii::app()->user->id,),
			'quizzes' => array(self::HAS_MANY, 'Quiz', 'concept_id',
					'order' => 'quizzes.create_at DESC'),
			'quizCount' => array(self::STAT, 'Quiz', 'concept_id'),
			'author' => array(self::BELONGS_TO, 'User', 'author_id'),
			'resources' => array(self::MANY_MANY, 'Resource', '{{concept_resource}}(concept_id, resource_id)'),
			'learners' => array(self::MANY_MANY, 'User', '{{learner_concept}}(concept_id, learner_id)'),
		);
	}

	public function pivotModels() {
		return array(
			'resources' => 'ConceptResource',
			'learners' => 'LearnerConcept',
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'author_id' => null,
			'title' => Yii::t('app', 'Title'),
			'description' => Yii::t('app', 'Description'),
			'root' => Yii::t('app', 'Root'),
			'lft' => Yii::t('app', 'Lft'),
			'rgt' => Yii::t('app', 'Rgt'),
			'level' => Yii::t('app', 'Level'),
			'create_at' => Yii::t('app', 'Create At'),
			'update_at' => Yii::t('app', 'Update At'),
			'asks' => null,
			'author' => null,
			'comments' => null,
			'resources' => null,
			'learners' => null,
			'notes' => null,
			'questions' => null,
			'quizzes' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		
		if ($this->tags != '')
			foreach (explode(', ', $this->tags) as $tag)
				$criteria->addSearchCondition('tags',$tag);

		$criteria->compare('id', $this->id, true);
		$criteria->compare('author_id', $this->author_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('root', $this->root, true);
		$criteria->compare('lft', $this->lft, true);
		$criteria->compare('rgt', $this->rgt, true);
		$criteria->compare('level', $this->level);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('update_at', $this->update_at, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}
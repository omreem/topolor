<?php

/**
 * This is the model base class for the table "{{user}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "User".
 *
 * Columns in table "{{user}}" available as properties of the model,
 * followed by relations of table "{{user}}" available as properties of the model.
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $activkey
 * @property string $create_at
 * @property string $lastvisit_at
 * @property integer $superuser
 * @property integer $status
 *
 * @property Answer[] $answers
 * @property Ask[] $asks
 * @property Concept[] $concepts
 * @property ConceptComment[] $conceptComments
 * @property mixed $concepts
 * @property Note[] $notes
 * @property Profile $profile
 * @property Question[] $questions
 * @property Quiz[] $quizzes
 * @property Resource[] $resources
 * @property Metadata[] $metadatas
 * @property Todo[] $todos
 */
abstract class BaseUser extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{user}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'User|Users', $n);
	}

	public static function representingColumn() {
		return 'username';
	}

	public function rules() {
		return array(
			array('username, password, email, create_at', 'required'),
			array('superuser, status', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>20),
			array('password, email, activkey', 'length', 'max'=>128),
			array('lastvisit_at', 'safe'),
			array('activkey, lastvisit_at, superuser, status', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, username, password, email, activkey, create_at, lastvisit_at, superuser, status', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'answers' => array(self::HAS_MANY, 'Answer', 'learner_id'),
			'asks' => array(self::HAS_MANY, 'Ask', 'learner_id'),
			'concepts' => array(self::HAS_MANY, 'Concept', 'author_id'),
			'conceptComments' => array(self::HAS_MANY, 'ConceptComment', 'learner_id'),
			'concepts' => array(self::MANY_MANY, 'Concept', '{{learner_concept}}(learner_id, concept_id)'),
			'notes' => array(self::HAS_MANY, 'Note', 'learner_id'),
			'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
			'questions' => array(self::HAS_MANY, 'Question', 'author_id'),
			'quizzes' => array(self::HAS_MANY, 'Quiz', 'learner_id'),
			'resources' => array(self::HAS_MANY, 'Resource', 'author_id'),
			'metadatas' => array(self::HAS_MANY, 'Metadata', 'user_id'),
			'todos' => array(self::HAS_MANY, 'Todo', 'learner_id'),
		);
	}

	public function pivotModels() {
		return array(
			'concepts' => 'LearnerConcept',
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'username' => Yii::t('app', 'Username'),
			'password' => Yii::t('app', 'Password'),
			'email' => Yii::t('app', 'Email'),
			'activkey' => Yii::t('app', 'Activkey'),
			'create_at' => Yii::t('app', 'Create At'),
			'lastvisit_at' => Yii::t('app', 'Lastvisit At'),
			'superuser' => Yii::t('app', 'Superuser'),
			'status' => Yii::t('app', 'Status'),
			'answers' => null,
			'asks' => null,
			'concepts' => null,
			'conceptComments' => null,
			'concepts' => null,
			'notes' => null,
			'profile' => null,
			'questions' => null,
			'quizzes' => null,
			'resources' => null,
			'metadatas' => null,
			'todos' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('username', $this->username, true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('activkey', $this->activkey, true);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('lastvisit_at', $this->lastvisit_at, true);
		$criteria->compare('superuser', $this->superuser);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}
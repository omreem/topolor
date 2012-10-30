<?php

/**
 * This is the model base class for the table "{{quiz_question}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "QuizQuestion".
 *
 * Columns in table "{{quiz_question}}" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $quiz_id
 * @property string $question_id
 * @property integer $position
 * @property string $answer
 *
 */
abstract class BaseQuizQuestion extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{quiz_question}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'QuizQuestion|QuizQuestions', $n);
	}

	public static function representingColumn() {
		return array(
			'quiz_id',
			'question_id',
		);
	}

	public function rules() {
		return array(
			array('quiz_id, question_id, position', 'required'),
			array('position', 'numerical', 'integerOnly'=>true),
			array('quiz_id, question_id', 'length', 'max'=>10),
			array('answer', 'length', 'max'=>1),
			array('answer', 'default', 'setOnEmpty' => true, 'value' => null),
			array('quiz_id, question_id, position, answer', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'quiz_id' => null,
			'question_id' => null,
			'position' => Yii::t('app', 'Position'),
			'answer' => Yii::t('app', 'Answer'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('quiz_id', $this->quiz_id);
		$criteria->compare('question_id', $this->question_id);
		$criteria->compare('position', $this->position);
		$criteria->compare('answer', $this->answer, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}
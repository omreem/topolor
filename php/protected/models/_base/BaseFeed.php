<?php

/**
 * @property string $id
 * @property string $user_id
 * @property string $of
 * @property string $of_id
 * @property string $from_id
 * @property string $description
 * @property string $create_at
 * @property string $update_at
 *
 * @property Feed $from
 * @property Feed[] $feeds
 * @property User $user
 * @property FeedComment[] $comments
 */
abstract class BaseFeed extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{feed}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Feed|Feeds', $n);
	}

	public static function representingColumn() {
		return 'description';
	}

	public function rules() {
		return array(
			array('description', 'required'),
			array('user_id, of_id, from_id', 'length', 'max'=>10),
			array('of', 'length', 'max'=>255),
			array('update_at', 'safe'),
			array('of, of_id, from_id, update_at', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, user_id, of, of_id, from_id, description, create_at, update_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'from' => array(self::BELONGS_TO, 'Feed', 'from_id'),
			'feeds' => array(self::HAS_MANY, 'Feed', 'from_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'comments' => array(self::HAS_MANY, 'FeedComment', 'feed_id', 'order' => 'comments.create_at DESC'),
			'commentCount' => array(self::STAT, 'FeedComment', 'feed_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'user_id' => null,
			'of' => Yii::t('app', 'Of'),
			'of_id' => Yii::t('app', 'Of'),
			'description' => Yii::t('app', 'Description'),
			'create_at' => Yii::t('app', 'Create At'),
			'update_at' => Yii::t('app', 'Update At'),
			'user' => Yii::t('app', 'User'),
			'feedComments' => Yii::t('app', 'Comment'),
		);
	}

	public function search($pageSize=10) {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('of', $this->of, true);
		$criteria->compare('of_id', $this->of_id, true);
		$criteria->compare('from_id', $this->from_id);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('update_at', $this->update_at, true);

		$criteria->order = 'create_at DESC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
					'pageSize' => $pageSize,
			),
		));
	}
}
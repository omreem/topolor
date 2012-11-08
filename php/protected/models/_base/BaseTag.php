<?php

/**
 * This is the model base class for the table "{{tag}}".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Tag".
 *
 * Columns in table "{{tag}}" available as properties of the model,
 * followed by relations of table "{{tag}}" available as properties of the model.
 *
 * @property string $id
 * @property string $user_id
 * @property string $of
 * @property string $name
 * @property integer $frequency
 * @property string $create_at
 * @property string $update_at
 *
 * @property User $user
 */
abstract class BaseTag extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{tag}}';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Tag|Tags', $n);
	}

	public static function representingColumn() {
		return 'name';
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('frequency', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('update_at', 'safe'),
			array('frequency, update_at', 'default', 'setOnEmpty' => true, 'value' => null),
			array('user_id, of, name, frequency, create_at, update_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
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
			'name' => Yii::t('app', 'Name'),
			'frequency' => Yii::t('app', 'Frequency'),
			'create_at' => Yii::t('app', 'Create At'),
			'update_at' => Yii::t('app', 'Update At'),
			'user' => null,
		);
	}

	public function search($order_by='frequency') {
		$sql = 'select name, sum(frequency) as frequency, count(user_id) as sum_user, min(create_at) as create_at from tpl_tag where of=\'ask\' group by name order by ';
		
		if ($order_by == 'users') {
			$sql .= 'sum_user desc';
		} else if ($order_by == 'name') {
			$sql .= 'name asc';
		} else if ($order_by == 'recent') {
			$sql .= 'create_at desc';
		} else { //frequency
			$sql .= 'frequency desc';
		}
		
		$sql2 = 'select count(name) from tpl_tag where of=\'ask\' group by name';
		
		return new CSqlDataProvider($sql, array(
			'totalItemCount'=>Yii::app()->db->createCommand($sql2)->queryScalar(),
			'keyField'=>'name',
			'pagination'=>array(
					'pageSize'=>20,
			),
		));
		
	}
}
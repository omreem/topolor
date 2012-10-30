<?php

Yii::import('application.models._base.BaseFavorite');

class Favorite extends BaseFavorite
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
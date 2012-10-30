<?php

Yii::import('application.models._base.BaseQuizQuestion');

class QuizQuestion extends BaseQuizQuestion
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
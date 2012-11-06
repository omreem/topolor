<?php

Yii::import('application.models._base.BaseMonitor');

class Monitor extends BaseMonitor
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
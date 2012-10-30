<?php

Yii::import('application.models._base.BaseConceptResource');

class ConceptResource extends BaseConceptResource
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
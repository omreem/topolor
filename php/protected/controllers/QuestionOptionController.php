<?php

class QuestionOptionController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'QuestionOption'),
		));
	}

	public function actionCreate() {
		$model = new QuestionOption;


		if (isset($_POST['QuestionOption'])) {
			$model->setAttributes($_POST['QuestionOption']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'QuestionOption');


		if (isset($_POST['QuestionOption'])) {
			$model->setAttributes($_POST['QuestionOption']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'QuestionOption')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('QuestionOption');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new QuestionOption('search');
		$model->unsetAttributes();

		if (isset($_GET['QuestionOption']))
			$model->setAttributes($_GET['QuestionOption']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
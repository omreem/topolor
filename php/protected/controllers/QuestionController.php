<?php

class QuestionController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Question'),
		));
	}

	public function actionCreate() {
		$model = new Question;


		if (isset($_POST['Question'])) {
			$model->setAttributes($_POST['Question']);
			$relatedData = array(
				'quizzes' => $_POST['Question']['quizzes'] === '' ? null : $_POST['Question']['quizzes'],
				);

			if ($model->saveWithRelated($relatedData)) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Question');
		if (isset($_POST['Question'])) {
			$model->setAttributes($_POST['Question']);
			$relatedData = array(
				'quizzes' => $_POST['Question']['quizzes'] === '' ? null : $_POST['Question']['quizzes'],
				);

			if ($model->saveWithRelated($relatedData)) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Question')->delete();
			
			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		
		$dataProvider = new CActiveDataProvider('Question');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Question('search');
		$model->unsetAttributes();

		if (isset($_GET['Question']))
			$model->setAttributes($_GET['Question']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
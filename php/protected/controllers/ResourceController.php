<?php

class ResourceController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Resource'),
		));
	}

	public function actionCreate() {
		$model = new Resource;


		if (isset($_POST['Resource'])) {
			$model->setAttributes($_POST['Resource']);
			$relatedData = array(
				'concepts' => $_POST['Resource']['concepts'] === '' ? null : $_POST['Resource']['concepts'],
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
		$model = $this->loadModel($id, 'Resource');


		if (isset($_POST['Resource'])) {
			$model->setAttributes($_POST['Resource']);
			$relatedData = array(
				'concepts' => $_POST['Resource']['concepts'] === '' ? null : $_POST['Resource']['concepts'],
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
			$this->loadModel($id, 'Resource')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Resource');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Resource('search');
		$model->unsetAttributes();

		if (isset($_GET['Resource']))
			$model->setAttributes($_GET['Resource']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
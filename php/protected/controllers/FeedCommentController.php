<?php

class FeedCommentController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'FeedComment'),
		));
	}

	public function actionCreate() {
		$model = new FeedComment;

		if (isset($_POST['FeedComment'])) {
			$model->setAttributes($_POST['FeedComment']);
			$model->create_at = date('Y-m-d H:i:s', time());
			$model->user_id = Yii::app()->user->id;
			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest()) {
					echo $model->id;
					Yii::app()->end();
				}
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'FeedComment');

		if (isset($_POST['FeedComment'])) {
			$model->setAttributes($_POST['FeedComment']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}
	
	public function actionUpdateAjax() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_POST['id']) || !isset($_POST['description']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			
		$model = $this->loadModel($_POST['id'], 'FeedComment');
		$model->description = $_POST['description'];
		
		$model->save();
		Yii::app()->end();
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'FeedComment')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}
	
	public function actionDeleteAjax() {
		if (!Yii::app()->getRequest()->getIsPostRequest() || !Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_POST['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$id = $_POST['id'];
		$this->loadModel($id, 'FeedComment')->delete();
		Yii::app()->end();
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('FeedComment');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new FeedComment('search');
		$model->unsetAttributes();

		if (isset($_GET['FeedComment']))
			$model->setAttributes($_GET['FeedComment']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
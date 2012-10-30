<?php

class FavoriteController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Favorite'),
		));
	}

	public function actionCreate() {
		$model = new Favorite;


		if (isset($_POST['Favorite'])) {
			$model->setAttributes($_POST['Favorite']);

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
		$model = $this->loadModel($id, 'Favorite');


		if (isset($_POST['Favorite'])) {
			$model->setAttributes($_POST['Favorite']);

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
			$this->loadModel($id, 'Favorite')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Favorite');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Favorite('search');
		$model->unsetAttributes();

		if (isset($_GET['Favorite']))
			$model->setAttributes($_GET['Favorite']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionFavorite() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !Yii::app()->getRequest()->getIsPostRequest() || !isset($_POST['id']) || !isset($_POST['of']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$id = $_POST['id'];
		$favorite = new Favorite;
		$favorite->of = $_POST['of'];
		$favorite->of_id = $id;
		$favorite->user_id = Yii::app()->user->id;
		$favorite->create_at = date('Y-m-d H:i:s', time());
		$favorite->save();
		Yii::app()->end();
	}
	
	public function actionUnfavorite() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !Yii::app()->getRequest()->getIsPostRequest() || !isset($_POST['id']) || !isset($_POST['of']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$id = $_POST['id'];
		$favorite = Favorite::model()->find('of=:of and of_id=:of_id and user_id=:user_id', array(':of'=>$_POST['of'], ':of_id'=>$id, ':user_id'=>Yii::app()->user->id));
		if ($favorite == null)
			throw new CHttpException(400, Yii::t('app', 'This feed has already been deleted.'));
		if ($favorite->delete())
			echo 'success';
		else
			echo 'fail';
		Yii::app()->end();
	}

}
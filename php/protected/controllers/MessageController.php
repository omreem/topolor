<?php

class MessageController extends GxController {

	public $layout='//layouts/site_column';
	
	public function actionView($id) {
		$model = $this->loadModel($id, 'Message');
		
		if (!$model->isFirst())
			$model = Message::model()->findByPk($model->to_message_id);
		
		$newMsg = new Message;
		$newMsg->to_message_id = $model->id;
		$newMsg->to_user_id = $model->to_user_id == Yii::app()->user->id ? $model->user_id : $model->to_user_id;
		
		$this->render('view', array(
			'model' => $model,
			'newMsg' => $newMsg,
		));
	}

	public function actionCreate() {
		$model = new Message;

		if (isset($_POST['Message'])) {
			$model->setAttributes($_POST['Message']);

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
		$model = $this->loadModel($id, 'Message');


		if (isset($_POST['Message'])) {
			$model->setAttributes($_POST['Message']);

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
			$this->loadModel($id, 'Message')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		//$rawData = Message::model()->findAll('to_message_id IS NULL ORDER BY create_at DESC');
		$rawData = Message::model()->findAll('to_user_id=:id OR user_id=:id ORDER BY create_at DESC', array(':id'=>Yii::app()->user->id));
		$arr = array();
		$diffIdArr = array();
		foreach ($rawData as $msg) {
			if ($msg['to_message_id'] == '') {
				if (!in_array($msg['id'], $diffIdArr)) {
					array_push($arr, $msg);
					array_push($diffIdArr, $msg['id']);
				}
			} else {
				if (!in_array($msg['to_message_id'], $diffIdArr)) {
					array_push($arr, $msg);
					array_push($diffIdArr, $msg['to_message_id']);
				}
			}
					
		}
		
		$dataProvider = new CArrayDataProvider($arr, array(
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
		
		$newMsg = new Message;
		
		$this->render('index', array(
			'dataProvider' => $dataProvider,
			'newMsg' => $newMsg,
		));
	}

	public function actionAdmin() {
		$model = new Message('search');
		$model->unsetAttributes();

		if (isset($_GET['Message']))
			$model->setAttributes($_GET['Message']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
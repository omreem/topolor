<?php

class AnswerController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Answer'),
		));
	}

	public function actionCreate() {
		$model = new Answer;

		if (isset($_POST['Answer'])) {
			$model->setAttributes($_POST['Answer']);
			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest()) {
					echo $model->id;
					Yii::app()->end();
				} else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Answer');


		if (isset($_POST['Answer'])) {
			$model->setAttributes($_POST['Answer']);

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
			
		$model = $this->loadModel($_POST['id'], 'Answer');
		$model->description = $_POST['description'];
	
		if ($model->save()) {
			echo $model->description;
			Yii::app()->end();
		} else {
			echo 'fail';
			Yii::app()->end();
		}
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Answer')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Answer');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Answer('search');
		$model->unsetAttributes();

		if (isset($_GET['Answer']))
			$model->setAttributes($_GET['Answer']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionUpdateView($id) {
		if (!isset($_GET['view']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($id, 'Answer');
		$create_at = Helpers::datatime_feed($model->create_at);
		
		if ($_GET['view'] == 'update')
			$str = <<<EOF
<form id='answer-form'>
	<div class="control-group">
		<textarea name="description" id="Answer_description" placeholder="Answer it">$model->description</textarea>
	</div>
	<input type="hidden" name="id" id="Answer_ask_id" value="$id">
	<a class="btn btn-primary btn-update">Confirm</a>
	<a class="btn btn-cancel">Cancel</a>
</form>
EOF;
		
		echo $str;
		
		Yii::app()->end();
	}

}
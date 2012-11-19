<?php

class ConceptCommentController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'ConceptComment'),
		));
	}

	public function actionCreate() {
		$model = new ConceptComment;

		if (isset($_POST['ConceptComment'])) {
			$model->setAttributes($_POST['ConceptComment']);

			if ($model->save()) {
		
		//monitor=begin
		$this->moniter('conceptComment', 'create', 'concept_id='.$_POST['ConceptComment']['concept_id']);
		//monitor-end
		
		
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'ConceptComment');


		if (isset($_POST['ConceptComment'])) {
			$model->setAttributes($_POST['ConceptComment']);

			if ($model->save()) {
		
		//monitor=begin
		$this->moniter('conceptComment', 'update', 'id='.$id);
		//monitor-end
		
		
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'ConceptComment')->delete();
		
		//monitor=begin
		$this->moniter('conceptComment', 'delete', 'id='.$id);
		//monitor-end
		
		

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		
		//monitor=begin
		$this->moniter('conceptComment', 'index');
		//monitor-end
		
		
		$dataProvider = new CActiveDataProvider('ConceptComment');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new ConceptComment('search');
		$model->unsetAttributes();

		if (isset($_GET['ConceptComment']))
			$model->setAttributes($_GET['ConceptComment']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}
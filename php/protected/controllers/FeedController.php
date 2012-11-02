<?php

class FeedController extends GxController {

	public $layout='//layouts/site_column';

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Feed'),
		));
	}
	
	public function actionSharePrepare() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_POST['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$s = Feed::model()->find('id=:id', array(':id'=>$_POST['id']));
		if ($s != null && $s->of != '')
			echo '//@'.$s->user.': '.$s->description;
		else
			echo '';
	}

	public function actionCreate() {
		$model = new Feed;

		if (isset($_POST['Feed'])) {
			$model->setAttributes($_POST['Feed']);
			$s = Feed::model()->find('of=:of and id=:id', array(':of'=>$model->of, ':id'=>$model->of_id));
			if ($s!= null && $s->of_id != '')
				$model->of_id = $s->of_id;
			
			if ($model->description == '') {
				if ($model->of == 'feed')
					$model->description = 'shared a status';
				else if ($model->of == 'note')
					$model->description = 'shared a note';
				else if ($model->of == 'ask')
					$model->description = 'shared a question';
			}

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
		$model = $this->loadModel($id, 'Feed');


		if (isset($_POST['Feed'])) {
			$model->setAttributes($_POST['Feed']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}
	
	public function actionUpdateAjax() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_POST['id'], 'Feed');
		
		if (isset($_POST['description']))
			$model->description = $_POST['description'];
		
		$model->save();
		Yii::app()->end();
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Feed')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$newFeed = new Feed;
		$newMessage = new Message;
		$newAsk = new Ask;
		$newNote = new Note;
		$newTodo = new Todo;
		
		$model = new Feed('search');
		$model->unsetAttributes();
		
		$this->render('index', array(
			'newFeed'=>$newFeed,
			'newMessage'=>$newMessage,
			'newAsk'=>$newAsk,
			'newNote'=>$newNote,
			'newTodo'=>$newTodo,
			'dataProvider' => $model->search(),
		));
	}

	public function actionAdmin() {
		$model = new Feed('search');
		$model->unsetAttributes();

		if (isset($_GET['Feed']))
			$model->setAttributes($_GET['Feed']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionFeedCount() {
		$sql = 'select count(*) from tpl_feed';
		$count = Yii::app()->db->createCommand($sql)->queryScalar();
		
		echo $count;
		Yii::app()->end();
	}

}
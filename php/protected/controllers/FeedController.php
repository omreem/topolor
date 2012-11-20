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
		
		//monitor=begin
		$this->moniter('feed', 'update', 'id='.$model->id, 'POST');
		//monitor-end
		
		
		if (isset($_POST['description']))
			$model->description = $_POST['description'];
		
		$model->save();
		Yii::app()->end();
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Feed');
			$model->delete();
		
		//monitor=begin
		$this->moniter('feed', 'delete', 'id='.$model->id);
		//monitor-end
		

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('feed', 'index');
		//monitor-end
		
		
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
	
	//for sign-in
	public function actionValidateUP() {
		if (isset($_POST['username']) && isset($_POST['password'])
				&& ( User::model()->find('username=:username AND password=:password', array(':username'=>$_POST['username'], ':password'=>UserModule::encrypting($_POST['password']))) != null
				|| User::model()->find('email=:username AND password=:password', array(':username'=>$_POST['username'], ':password'=>UserModule::encrypting($_POST['password']))) != null
				))
			echo 'success';
		else
			echo 'fail';
		Yii::app()->end();
	}
	
	//for sign-up
	public function actionValidateEmail() {
		if (isset($_POST['email']) && User::model()->find('email=:email', array(':email'=>$_POST['email'])) == null)
			echo 'success';
		else
			echo 'fail';
		Yii::app()->end();
	}
	public function actionValidateUsername() {
		if (isset($_POST['username']) && User::model()->find('username=:username', array(':username'=>$_POST['username'])) == null)
			echo 'success';
		else
			echo 'fail';
		Yii::app()->end();
	}
	
	public function actionRegistration() {
		if (isset($_POST['RegistrationForm'])) {
			$model = new RegistrationForm;
			$model->attributes=$_POST['RegistrationForm'];
			$model->activkey = 'not set yet';
			$model->superuser = 0;
			$model->status = 1;
			$model->save();
		}
	}
	
	public function actionFetchTopUsers() {
		
		$rank_by = 'shared'; //default
		if (isset($_POST['rank_by']))
			$rank_by = $_POST['rank_by'];
		
		$us = User::model()->with('countShare', 'countComment', 'countFavorite')->findAllBySql('select * from tpl_user limit 5');

		$userArr = array();
		if ($rank_by == 'favorited')
			foreach ($us as $user)
				$userArr[$user->countFavorite] = $user;
		else if ($rank_by == 'commented') {
			foreach ($us as $user)
				$userArr[$user->countComment] = $user;
		}else // rank by shared
			foreach ($us as $user)
				$userArr[$user->countShare] = $user;

		krsort($userArr);
	
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		if ($rank_by == 'favorited')
			foreach ($userArr as $user)
				if ($user->id == Yii::app()->user->id)
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It's you.">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countFavorite favorite(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
				else
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countFavorite favorite(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
		else if ($rank_by == 'commented')
			foreach ($userArr as $user)
				if ($user->id == Yii::app()->user->id)
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It's you.">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countComment comment(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
				else
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countComment comment(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
		else // rank by shared
			foreach ($userArr as $user)
				if ($user->id == Yii::app()->user->id)
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It's you.">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countShare share(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
				else
					$rtn .= <<<EOF
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="$baseUrl/uploads/images/profile-avatar/$user->id" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">$user</div>
		<div style="color: #333">$user->countShare share(s)</div>
		<input id="data_id" type="hidden" value="$user->id"/>
	</div>
</div>
EOF;
		
		echo $rtn .'';
		Yii::app()->end();
	}

}
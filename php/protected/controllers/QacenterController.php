<?php

class QacenterController extends GxController {
	
	public $layout='//layouts/qacenter';
	
	public function actionIndex() {
		$model = new Ask('search');
		$model->unsetAttributes();
		
		$filter_by = '';
		if (isset($_GET['filter_by']))
			$filter_by = $_GET['filter_by'];
		
		$isAnswered = '';
		if (isset($_GET['is_answered']))
			$isAnswered = $_GET['is_answered'];

		$order_by = '';
		if(isset($_GET['order_by']))
			$order_by = $_GET['order_by'];
	
		$this->render('index', array(
			'dataProvider' => $model->search($filter_by, '', '', $isAnswered, $order_by),
		));
	}
		
	public function actionQas() {
		$newAsk = new Ask;

		$model = new Ask('search');
		$model->unsetAttributes();
	
		$filter_by = '';
		if (isset($_GET['filter_by']))
			$filter_by = $_GET['filter_by'];
		
		$tag = '';
		if(isset($_GET['tag']))
			$tag = CHtml::decode($_GET['tag']);
		
		$concept_id = '';
		if (isset($_GET['concept_id']))
			$concept_id = $_GET['concept_id'];
	
		$this->render('/ask/index', array(
			'dataProvider' => $model->search($filter_by, $tag, $concept_id),
			'newAsk' => $newAsk
		));
	}
	
	public function actionConcept() {
		
		$sql = 'select c.id as id, c.title as name, count(distinct a.id) as frequency, count(distinct learner_id) as sum_user, min(a.create_at) as create_at from tpl_ask as a join tpl_concept as c on a.concept_id=c.id group by c.id order by ';
		
		$order_by = '';
		if (isset($_GET['order_by']))
			$order_by = $_GET['order_by'];
		
		if ($order_by == 'users') {
			$sql .= 'sum_user desc';
		} else if ($order_by == 'name') {
			$sql .= 'name asc';
		} else if ($order_by == 'recent') {
			$sql .= 'create_at desc';
		} else { //frequency
			$sql .= 'frequency desc';
		}
		
		$sql2 = 'select count(c.id) from tpl_ask as a join tpl_concept as c on a.concept_id=c.id group by c.id';
		
		$this->render('concept', array(
			'dataProvider' => new CSqlDataProvider($sql, array(
				'totalItemCount'=>Yii::app()->db->createCommand($sql2)->queryScalar(),
				'keyField'=>'id',
				'pagination'=>array(
						'pageSize'=>20,
				),
			))
		));
	}
	
	public function actionTag() {
		
		$model = new Tag('search');
		$model->unsetAttributes();
		
		$order_by = '';
		if (isset($_GET['order_by']))
			$order_by = $_GET['order_by'];
		
		$this->render('tag', array(
			'dataProvider' => $model->search($order_by)
		));
	}
	
	public function actionViewTag() {
		if (!isset($_GET['tag']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$tag = $_GET['tag'];
		
		$newAsk = new Ask;
		$newAsk->tags = $tag;
		
		$model = new Ask('search');
		$model->unsetAttributes();
		
		$this->render('viewTag', array(
			'tag' => $tag,
			'newAsk' => $newAsk,
			'dataProvider' => $model->search('', $tag),
		));
	}
	
	public function actionViewConcept() {
		if (!isset($_GET['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_GET['concept_id'];
		
		$newAsk = new Ask;
		$newAsk->concept_id = $concept_id;
		
		$model = new Ask('search');
		$model->unsetAttributes();
		
		$concept = Concept::model()->findByPk($concept_id);
		
		$this->render('viewConcept', array(
			'concept_title' => $concept->title,
			'newAsk' => $newAsk,
			'dataProvider' => $model->search('', '', $concept_id),
		));
	}
	
	public function actionMyqa() {
		$newAsk = new Ask;

		$model = new Ask('search');
		$model->unsetAttributes();
		
		$filter_by = '';
		if (isset($_GET['filter_by']))
			$filter_by = $_GET['filter_by'];
	
		$isAnswered = '';
		if (isset($_GET['is_answered']))
			$isAnswered = $_GET['is_answered'];

		$tag = '';
		if(isset($_GET['tag']))
			$tag = CHtml::decode($_GET['tag']);
		
		$concept_id = '';
		if (isset($_GET['concept_id']))
			$concept_id = $_GET['concept_id'];
		
		$this->render('myqa', array(
			'dataProvider' => $model->search($filter_by, $tag, $concept_id, $isAnswered),
			'newAsk' => $newAsk,
		));
	}
	
	function sortByOneKey(array $array, $key, $asc = true) {
	    $result = array();
	       
	    $values = array();
	    foreach ($array as $id => $value) {
	        $values[$id] = isset($value[$key]) ? $value[$key] : '';
	    }
	       
	    if ($asc) {
	        asort($values);
	    }
	    else {
	        arsort($values);
	    }
	       
	    foreach ($values as $key => $value) {
	        $result[$key] = $array[$key];
	    }
	       
	    return $result;
	}
	
	public function actionFetchUsers() {	
		
		$rank_by = 'answers';
		if (isset($_POST['rank_by']))
			$rank_by = $_POST['rank_by'];

		

/*		
		$us = User::model()->with('countAsk', 'countAnswer')->findAllBySql('select * from tpl_user limit 5');
		
		$userArr = array();
		if ($rank_by == 'questions')
			foreach ($us as $user)
				$userArr[$user->countAsk] = $user;
		else
			foreach ($us as $user)
				$userArr[$user->countAnswer] = $user;
		
		krsort($userArr);
*/		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		if ($rank_by == 'questions') {
			
			$userArr = Yii::app()->db->createCommand('SELECT t.id, username, COUNT(t.id) AS count FROM tpl_user AS t, tpl_ask AS a WHERE t.id=a.learner_id GROUP BY t.id ORDER BY COUNT(t.id)')->queryAll();
			
			foreach ($userArr as $user)
				if ($user['id'] == Yii::app()->user->id)
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' question(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
				else
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' question(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		} else {
			
			$userArr = Yii::app()->db->createCommand('SELECT t.id, username, COUNT(t.id) AS count FROM tpl_user AS t, tpl_answer AS a WHERE t.id=a.learner_id GROUP BY t.id ORDER BY COUNT(t.id)')->queryAll();
							
			foreach ($userArr as $user)
				if ($user['id'] == Yii::app()->user->id)
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' answers(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';				
				else
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' answers(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		}
		
		echo $rtn;
		Yii::app()->end();
			
	}
	
}
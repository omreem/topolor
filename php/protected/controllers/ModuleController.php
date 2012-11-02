<?php

class ModuleController extends GxController {

	public $layout='//layouts/module';
	
	public function actionIndex() {
		$moduleArr = Concept::model()->findAll(array("condition"=>"id = root","order"=>"create_at desc"));
		
		$this->render('index', array(
			'moduleArr' => $moduleArr,
		));
	}
	
	public function actionView($id) {
		//has not registered
		$learnerConcept = LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
				array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$id));
		if ($learnerConcept ==null)
			$this->redirect(Yii::app()->homeUrl.'/module');
		
		$model = $this->loadModel($id, 'Concept');
		if (!$model->isModule())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		//for module structure
		$concepts = Concept::model()->findAll(array("condition"=>"root =  $id","order" => "lft"));
		
		//up next
		$sql='select'
		.' c.id'
		
		.' from'
		.' tpl_concept as c'
		.' join tpl_learner_concept as lc on c.id = lc.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and c.id<>'.$id
		.' and lc.status='.LearnerConcept::STATUS_COMPLETED
		.' and lc.learner_id='.Yii::app()->user->id
		
		.' order by c.lft desc';
		$command = Yii::app()->db->createCommand($sql);
		$row=$command->queryRow();
		if ($row == null)
			$upNext = $model->nextConcept;
		else
			$upNext = Concept::model()->findByPk($row['id'])->nextConcept;
		
		//recently learnt
		$sql='select'
		.' c.id,'
		.' c.title,'
		.' c.description,'
		.' lc.lastaction_at'
		
		.' from'
		.' tpl_concept as c'
		.' join tpl_learner_concept as lc on c.id = lc.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and c.id<>'.$id
		.' and lc.status='.LearnerConcept::STATUS_COMPLETED
		.' and lc.learner_id='.Yii::app()->user->id
		
		.' order by lc.lastaction_at desc';
		
		$sql2='select count(c.id)'
		
		.' from'
		.' tpl_concept as c'
		.' join tpl_learner_concept as lc on c.id = lc.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and c.id<>'.$id
		.' and lc.status='.LearnerConcept::STATUS_COMPLETED
		.' and lc.learner_id='.Yii::app()->user->id;
		
		$countRecentlyLearnt=Yii::app()->db->createCommand($sql2)->queryScalar();
		
		if ($countRecentlyLearnt == 0)
			$recentlyLearntConcepts = null;
		else
			$recentlyLearntConcepts=new CSqlDataProvider($sql, array(
			    'totalItemCount'=>$countRecentlyLearnt,
				'keyField'=>'id',
			    'pagination'=>array(
			        'pageSize'=>5,
			    ),
			));
		
		//quiz
		$sql='select'
		.' q.id,'
		.' c.id as concept_id,'
		.' c.title,'
		.' q.done_at'
		
		.' from'
		.' tpl_concept as c'
		.' join tpl_quiz as q on c.id = q.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and done_at IS NOT NULL'
		.' and q.learner_id='.Yii::app()->user->id
		.' order by q.done_at desc';
		
		$sql2='select count(q.id)'
		
		.' from'
		.' tpl_concept as c'
		.' join tpl_quiz as q on c.id = q.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and done_at IS NOT NULL'
		.' and q.learner_id='.Yii::app()->user->id;
		$countquizDone=Yii::app()->db->createCommand($sql2)->queryScalar();
		if ($countquizDone == 0)
			$quizDone = null;
		else
			$quizDone=new CSqlDataProvider($sql, array(
					'totalItemCount'=>$countquizDone,
					'keyField'=>'id',
					'pagination'=>array(
							'pageSize'=>5,
					),
			));
		
		$sql='select count(concept_id) from tpl_question group by concept_id';
		$countQuizzes = count(Yii::app()->db->createCommand($sql)->queryAll());
		
		//note
		$sql='select'
		.' n.id,'
		.' n.title,'
		.' n.create_at,'
		.' n.description'
		
		.' from'
		.' tpl_note as n'
		.' join tpl_concept as c on c.id = n.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and n.learner_id='.Yii::app()->user->id
		.' order by n.create_at desc';
		
		$sql2='select count(n.id)'
		
		.' from'
		.' tpl_note as n'
		.' join tpl_concept as c on c.id = n.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and n.learner_id='.Yii::app()->user->id;
		
		$countNote=Yii::app()->db->createCommand($sql2)->queryScalar();
		if ($countNote == 0)
			$notes = null;
		else
			$notes=new CSqlDataProvider($sql, array(
					'totalItemCount'=>$countNote>2?2:$countNote,
					'keyField'=>'id',
					'pagination'=>array(
							'pageSize'=>2,
					),
			));
		
		//ask
		$sql='select'
		.' a.id,'
		.' a.title,'
		.' a.create_at,'
		.' a.description'
		
		.' from'
		.' tpl_ask as a'
		.' join tpl_concept as c on c.id = a.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and a.learner_id='.Yii::app()->user->id
		.' order by a.create_at desc';
		
		$sq2='select count(a.id)'
		
		.' from'
		.' tpl_ask as a'
		.' join tpl_concept as c on c.id = a.concept_id'
		
		.' where'
		.' c.root='.$id
		.' and a.learner_id='.Yii::app()->user->id
		.' order by a.create_at desc';
		
		$countAsk=Yii::app()->db->createCommand($sql2)->queryScalar();
		if ($countAsk == 0)
			$asks = null;
		else
			$asks=new CSqlDataProvider($sql, array(
					'totalItemCount'=>$countAsk>2?2:$countAsk,
					'keyField'=>'id',
					'pagination'=>array(
							'pageSize'=>2,
					),
			));
		
		$this->render('view', array(
			'model' => $model,
			'concepts' => $concepts,
			'upNext' => $upNext,
			'recentlyLearntConcepts' => $recentlyLearntConcepts,
			'countConcepts' => count($concepts),
			'countLearntConcepts' => $countRecentlyLearnt,
			'countquizDone' => $countquizDone,
			'countQuizzes' => $countQuizzes,
			'quizDone' => $quizDone,
			'notes' => $notes,
			'asks'=> $asks,
		));
	}
	
	public function actionPreview($id) {
		//has registered
		$learnerConcept = LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
				array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$id));
		if ($learnerConcept !=null)
			$this->redirect(Yii::app()->homeUrl.'/module/'.$id);
		
		$concept = $this->loadModel($id, 'Concept');
		if (!$concept->isModule())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concepts = Concept::model()->findAll(array("condition"=>"root =  $concept->id","order" => "lft"));
		
		$this->render('preview', array(
			'model' => $concept,
			'concepts' => $concepts,
		));
	}
	
	public function actionRegister() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_POST['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$learnerConcept = new LearnerConcept;
		$learnerConcept->concept_id = $_POST['id'];
		$learnerConcept->learner_id = Yii::app()->user->id;
		$learnerConcept->create_at = date('Y-m-d H:i:s', time());
		
		if($learnerConcept->save())
			Yii::app()->end();
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
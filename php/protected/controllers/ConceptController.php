<?php

class ConceptController extends GxController {

	public $layout='//layouts/concept';

	public function actionView($id) {
		$model = $this->loadModel($id, 'Concept');
		$learnerId = Yii::app()->user->id;
		if ($model->isModule()) { // is Module
			$learnerModule = LearnerConcept::model()->findByPk(array('concept_id'=>$model->id, 'learner_id'=>$learnerId));
			if ($learnerModule == null) //has not registered
				$this->redirect(Yii::app()->homeUrl.'/concept');
			
			$breadcrumbs = $model->title;
			
			//module structure
			$concepts = Concept::model()->findAll(array("condition"=>"root =  $id","order" => "lft"));
			
			//up next
			$upNext = Concept::model()->findBySql('SELECT * FROM tpl_concept WHERE root<>id AND root='.$id.' AND id NOT IN (SELECT concept_id FROM tpl_learner_concept WHERE status=2 AND learner_id='.Yii::app()->user->id.') ORDER BY lft');
			
			//recently learnt
			$sql='select c.id as id, c.title as title, c.description as description, c.tags as tags, lc.lastaction_at as lastaction_at'
			
			.' from tpl_concept as c join tpl_learner_concept as lc on c.id = lc.concept_id'
			
			.' where'
			.' c.root='.$id
			.' and c.id<>'.$id
			.' and lc.status='.LearnerConcept::STATUS_COMPLETED
			.' and lc.learner_id='.Yii::app()->user->id
			
			.' order by lc.lastaction_at desc';

			$recentlyLearntConceptArr = Yii::app()->db->createCommand($sql)->queryAll();
			$countRecentlyLearntConcepts = count($recentlyLearntConceptArr);
			
			for ($i=0;$i<$countRecentlyLearntConcepts;$i++) {
				$tagLabels='';
				foreach (explode(', ', $recentlyLearntConceptArr[$i]['tags']) as $tag)
					$tagLabels.=CHtml::tag('span', array('class'=>'label label-info'), CHtml::encode($tag)).'&nbsp;';
				$recentlyLearntConceptArr[$i]['tags'] = $tagLabels;
			}
			
			$recentlyLearntConcepts=new CArrayDataProvider($recentlyLearntConceptArr, array('pagination'=>array('pageSize'=>5)));
			
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
			$quizDone=new CSqlDataProvider($sql, array(
					'totalItemCount'=>$countquizDone,
					'keyField'=>'id',
					'pagination'=>array(
							'pageSize'=>5,
					),
			));
			
			$sql='select count(concept_id) from tpl_question group by concept_id';
			$countQuizzes = count(Yii::app()->db->createCommand($sql)->queryAll());
			
		} else { // not Module
			$learnerModule = LearnerConcept::model()->findByPk(array('concept_id'=>$model->root, 'learner_id'=>$learnerId));
			$learnerModule->lastaction_at = date('Y-m-d H:i:s', time());
			$learnerModule->save();
			
			$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$id, 'learner_id'=>$learnerId));
			if ($learnerConcept == null) {
				$learnerConcept = new LearnerConcept;
				$learnerConcept->concept_id = $id;
				$learnerConcept->learner_id = $learnerId;
				$learnerConcept->create_at = date('Y-m-d H:i:s', time());
			}
			$learnerConcept->lastaction_at = date('Y-m-d H:i:s', time());
			$learnerConcept->save();
			
			$breadcrumbs = '<a href="'.Yii::app()->homeUrl.'/concept/'.$model->module->id.'" class="btn-link">'.$model->module->title.'</a>  &raquo;  '.$model->title;			
			if ($learnerConcept->status == LearnerConcept::STATUS_COMPLETED)
				$learnt_at = $learnerConcept->learnt_at;
			else
				$learnt_at = null;
			
			$canHasQuiz = $model->questionCount == 0 ? 'no' : 'yes';
			$quizDoneAt = Yii::app()->db->createCommand('SELECT done_at FROM tpl_quiz WHERE concept_id='.$model->id.' AND learner_id='.Yii::app()->user->id)->queryScalar();
		}
		
		$dataProvider_comment = new CArrayDataProvider($model->comments, array(
			'keyField'=>'id',
			'pagination' => array(
				'pageSize' => 5,
			),
		));
			
		if (isset($_GET['of']) && $_GET['of'] == 'ask') {
			$ask = new Ask('search');
			$ask->unsetAttributes();
			
			$filter_by = '';
			if (isset($_GET['filter_by']))
				$filter_by = $_GET['filter_by'];
			
			$tag = '';
			if(isset($_GET['tag']))
				$tag = CHtml::decode($_GET['tag']);
			
			$dataProvider_ask = $ask->search($filter_by, $tag, $id, 5);
			
		} else {
		
			$dataProvider_ask = new CArrayDataProvider($model->asks, array(
				'keyField'=>'id',
				'pagination' => array(
					'pageSize' => 5,
				),
			));
		}
		
		if (isset($_GET['of']) && $_GET['of'] == 'note') {
			$note = new Note('search');
			$note->unsetAttributes();
			
			$note->learner_id = Yii::app()->user->id;
			
			$interval = '';
			if (isset($_GET['interval']))
				$interval = $_GET['interval'];
			
			$tag = '';
			if(isset($_GET['tag']))
				$tag = CHtml::decode($_GET['tag']);
			
			$concept_id = '';
			if (isset($_GET['concept_id']))
				$concept_id = $_GET['concept_id'];
			
			$dataProvider_note = $note->search($interval, $tag, $id);
			
		} else {
		
			$dataProvider_note = new CArrayDataProvider($model->notes, array(
				'keyField'=>'id',
				'pagination' => array(
						'pageSize' => 5,
				),
			));
		}
		
		if (isset($_GET['of']) && $_GET['of'] == 'todo') {
			$todo=new Todo('search');
			$todo->unsetAttributes();
			
			$todo->learner_id = Yii::app()->user->id;
			
			if(isset($_GET['status']))
				$todo->status=$_GET['status'];
			else
				$todo->status = Todo::STATUS_UNDONE;
			
			$interval='';
			if (isset($_GET['interval']))
				$interval=$_GET['interval'];
			
			$tag = '';
			if(isset($_GET['tag']))
				$tag = CHtml::decode($_GET['tag']);
			
			$concept_id = '';
			if (isset($_GET['concept_id']))
				$concept_id = $_GET['concept_id'];

			$dataProvider_todo = $todo->search($interval, $tag, $id);
			
		} else {
		
			$dataProvider_todo = new CArrayDataProvider($model->todosOwnedUndone, array(
				'keyField'=>'id',
				'pagination' => array(
					'pageSize' => 5,
				),
			));
		}
		
		// for module: pre-test? test? final-test?
		if ($model->isModule()) {
			$row = Yii::app()->db->createCommand('SELECT COUNT(id) AS count, MIN(done_at) AS min_done_at FROM {{quiz}} WHERE concept_id='.$model->id)->queryRow();
			if ($row['count'] != 0 && $row['min_done_at'] != null)
				$quizType = Quiz::TYPE_MID_TEST;
			else
				$quizType = Quiz::TYPE_PRE_TEST;
		} else {
			$quizType = Quiz::TYPE_QUIZ;
		}

		$params = array(
			'model' => $model,
			'breadcrumbs' => $breadcrumbs,
			'dataProvider_comment' => $dataProvider_comment,
			'dataProvider_ask' => $dataProvider_ask,
			'dataProvider_note' => $dataProvider_note,
			'dataProvider_todo' => $dataProvider_todo,
			'quizType' => $quizType,
		);
		
		if ($model->isModule()) {
			$params['concepts'] = $concepts;
			$params['upNext'] = $upNext;
			$params['recentlyLearntConcepts'] = $recentlyLearntConcepts;
			$params['countConcepts'] = count($concepts) - 1; // without root concept
			$params['countLearntConcepts'] = $countRecentlyLearntConcepts;
			$params['countquizDone'] = $countquizDone;
			$params['countQuizzes'] = $countQuizzes;
			$params['quizDone'] = $quizDone;
		} else {
			$params['previousConcept'] = $model->previousConcept;
			$params['nextConcept'] = $model->nextConcept;
			$params['canHasQuiz'] = $canHasQuiz;
			$params['learnt_at'] = $learnt_at;
			$params['quizDoneAt'] = $quizDoneAt;
		}
		
		$this->render('view', $params);
	}
	
	public function actionConceptList() {

		$moduleId = $_GET['moduleId'];
		if ($moduleId == null || null == LearnerConcept::model()->findByPk(array('concept_id'=>$moduleId, 'learner_id'=>Yii::app()->user->id)))
			$this->redirect(Yii::app()->homeUrl.'/concept');
		
		$filter_by = $_GET['filter_by'];
		
		if ($filter_by == 'learnt') {

			$sql='select c.id as id, c.title as title, c.description as description, c.tags as tags, lc.lastaction_at as lastaction_at'
			
			.' from tpl_concept as c join tpl_learner_concept as lc on c.id = lc.concept_id'
			
			.' where'
			.' c.root='.$moduleId
			.' and c.id<>'.$moduleId
			.' and lc.status='.LearnerConcept::STATUS_COMPLETED // learnt
			.' and lc.learner_id='.Yii::app()->user->id
			
			.' order by lc.lastaction_at desc';
		} else if ($filter_by == 'learning'){
			$sql='select c.id as id, c.title as title, c.description as description, c.tags as tags, lc.lastaction_at as lastaction_at'
				
			.' from tpl_concept as c join tpl_learner_concept as lc on c.id = lc.concept_id'
				
			.' where'
			.' c.root='.$moduleId
			.' and c.id<>'.$moduleId
			.' and lc.status='.LearnerConcept::STATUS_INPROGRESS // learning
			.' and lc.learner_id='.Yii::app()->user->id
				
			.' order by lc.lastaction_at desc';
		} else if ($filter_by == 'upnext'){
			$sql='select c.id as id, c.title as title, c.description as description, c.tags as tags, lc.lastaction_at as lastaction_at'
			
			.' from tpl_concept as c join tpl_learner_concept as lc on c.id = lc.concept_id'
			
			.' where'
			.' c.root='.$moduleId
			.' and c.id<>'.$moduleId
			.' and lc.status='.LearnerConcept::STATUS_INPROGRESS // learning
			.' and lc.learner_id='.Yii::app()->user->id
			
			.' order by c.lft';
		} else if ($filter_by == 'az') {
			$sql='select id, title, description, tags from tpl_concept'
			.' where root='.$moduleId.' and id<>'.$moduleId
			.' order by title';
		} else {// $filter_by == 'all'
			$sql='select id, title, description, tags from tpl_concept'
			.' where root='.$moduleId.' and id<>'.$moduleId
			.' order by lft';
		}

		$conceptArr = Yii::app()->db->createCommand($sql)->queryAll();
				
		$this->render('viewConceptList', array(
			'moduleId' => $moduleId,
			'moduleTitle' => Yii::app()->db->createCommand('SELECT title FROM tpl_concept WHERE id='.$moduleId)->queryScalar(),
			'recentlyLearntConcepts' => new CArrayDataProvider($conceptArr, array('pagination'=>array('pageSize'=>20))),
			'filter_by' => $filter_by
		));
	}
	
	public function actionQuizList() {
		
		$moduleId = $_GET['moduleId'];
		if ($moduleId == null || null == LearnerConcept::model()->findByPk(array('concept_id'=>$moduleId, 'learner_id'=>Yii::app()->user->id)))
			$this->redirect(Yii::app()->homeUrl.'/concept');
		
		$sql='select'
		.' q.id as id,'
		.' q.score as score,'
		.' c.id as concept_id,'
		.' c.title as concept_title,'
		.' q.done_at'
			
		.' from'
		.' tpl_concept as c'
		.' join tpl_quiz as q on c.id = q.concept_id'
			
		.' where'
		.' c.root='.$moduleId
		.' and done_at IS NOT NULL'
		.' and q.learner_id='.Yii::app()->user->id
		.' order by q.done_at desc';
		
		$quizArr = Yii::app()->db->createCommand($sql)->queryAll();
		$count = count($quizArr);
		
		for ($i=0;$i<$count;$i++) {
			$quizArr[$i]['tags'] = $this->getTags($quizArr[$i]['concept_id']);
		}
		
		$this->render('viewQuizList', array(
				'moduleId' => $moduleId,
				'moduleTitle' => Yii::app()->db->createCommand('SELECT title FROM tpl_concept WHERE id='.$moduleId)->queryScalar(),
				'dataProvider' => new CArrayDataProvider($quizArr, array('pagination'=>array('pageSize'=>20))),
		));
	}
	
	public function actionMyanswers() {
		
		$moduleId = $_GET['moduleId'];
		if ($moduleId == null || null == LearnerConcept::model()->findByPk(array('concept_id'=>$moduleId, 'learner_id'=>Yii::app()->user->id)))
			$this->redirect(Yii::app()->homeUrl.'/concept');
		
		$filter_by = $_GET['filter_by'];
		
		if ($filter_by == 'correct')
			$sql='SELECT q.id, q.description, q.correct_answer, qzq.answer, qz.done_at, q.concept_id, c.title AS concept_title FROM tpl_question AS q, tpl_quiz AS qz, tpl_quiz_question AS qzq, tpl_concept AS c WHERE q.id=qzq.question_id AND qzq.quiz_id = qz.id AND qzq.answer IS NOT NULL AND q.concept_id=c.id AND qzq.answer=q.correct_answer AND qz.learner_id='.Yii::app()->user->id.' ORDER BY qz.done_at DESC';
		else if ($filter_by == 'incorrect')
			$sql='SELECT q.id, q.description, q.correct_answer, qzq.answer, qz.done_at, q.concept_id, c.title AS concept_title FROM tpl_question AS q, tpl_quiz AS qz, tpl_quiz_question AS qzq, tpl_concept AS c WHERE q.id=qzq.question_id AND qzq.quiz_id = qz.id AND qzq.answer IS NOT NULL AND q.concept_id=c.id AND qzq.answer<>q.correct_answer AND qz.learner_id='.Yii::app()->user->id.' ORDER BY qz.done_at DESC';
		else // all
			$sql='SELECT q.id, q.description, q.correct_answer, qzq.answer, qz.done_at, q.concept_id, c.title AS concept_title FROM tpl_question AS q, tpl_quiz AS qz, tpl_quiz_question AS qzq, tpl_concept AS c WHERE q.id=qzq.question_id AND qzq.quiz_id = qz.id AND qzq.answer IS NOT NULL AND q.concept_id=c.id AND qz.learner_id='.Yii::app()->user->id.' ORDER BY qz.done_at DESC';
		
		$questionArr = Yii::app()->db->createCommand($sql)->queryAll();
		$count = count($questionArr);
		for($i=0;$i<$count;$i++) {
			$optionArr = Yii::app()->db->createCommand('SELECT opt, val FROM tpl_question_option WHERE question_id='.$questionArr[$i]['id'].' ORDER BY opt')->queryAll();
			$cnt = count($optionArr);
			for ($j=0;$j<$cnt;$j++)
				$questionArr[$i]['option'][$optionArr[$j]['opt']] = $optionArr[$j]['val'];
		}
		
		$this->render('viewMyanswers', array(
				'moduleId' => $moduleId,
				'moduleTitle' => Yii::app()->db->createCommand('SELECT title FROM tpl_concept WHERE id='.$moduleId)->queryScalar(),
				'dataProvider' => new CArrayDataProvider($questionArr, array('pagination'=>array('pageSize'=>20))),
				'filter_by' => $filter_by
		));
	}
	
	public function actionCreate() {
		$model = new Concept;


		if (isset($_POST['Concept'])) {
			$model->setAttributes($_POST['Concept']);
			$relatedData = array(
				'resources' => $_POST['Concept']['resources'] === '' ? null : $_POST['Concept']['resources'],
				'learners' => $_POST['Concept']['learners'] === '' ? null : $_POST['Concept']['learners'],
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
		$model = $this->loadModel($id, 'Concept');


		if (isset($_POST['Concept'])) {
			$model->setAttributes($_POST['Concept']);
			$relatedData = array(
				'resources' => $_POST['Concept']['resources'] === '' ? null : $_POST['Concept']['resources'],
				'learners' => $_POST['Concept']['learners'] === '' ? null : $_POST['Concept']['learners'],
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
			$this->loadModel($id, 'Concept')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$moduleArr = Concept::model()->findAll(array("condition"=>"id = root","order"=>"create_at desc"));
		
		$this->render('index', array(
			'moduleArr' => $moduleArr,
		));
	}

	public function actionAdmin() {
		$model = new Concept('search');
		$model->unsetAttributes();

		if (isset($_GET['Concept']))
			$model->setAttributes($_GET['Concept']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function getModuleStructure($moduleId) {
				
		$root = Yii::app()->db->createCommand('SELECT root FROM tpl_concept WHERE id='.$moduleId)->queryScalar();
		if ($root != $moduleId) // not a module
			return 'not a module';
			
		$concepts = Concept::model()->findAll(array("condition"=>"root =  $moduleId","order" => "lft"));
			
		$rtn = '<table class="tree-table">';
		foreach ($concepts as $concept) {
			if ($concept->id == $concept->root)
				continue;
				
			$rtn .= "<tr><td class='title' style='padding-left: ".(($concept->level-1)*20)."px;'>".($concept->level==2?"<b>":"").CHtml::encode(Helpers::string_len($concept->title, 60)).($concept->level==2?"</b>":"")."</td>"
			."<td class='action'><a href='".Yii::app()->homeUrl."/concept/".$concept->id."'>Get In</a></td></tr>";
		}
			
		return $rtn.'</table>';
	}
	
	public function actionHasLearnt() {
		if (!Yii::app()->request->isAjaxRequest || !isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$_POST['concept_id'], 'learner_id'=>Yii::app()->user->id));
		$learnerConcept->learnt_at = date('Y-m-d H:i:s', time());
		$learnerConcept->status = LearnerConcept::STATUS_COMPLETED;
		if ($learnerConcept->save()) {
			$concept = $this->loadModel($_POST['concept_id'], 'Concept');
			$sumHasLearnt = Yii::app()->db->createCommand('SELECT COUNT(concept_id) FROM tpl_learner_concept WHERE status=2 AND learner_id='.Yii::app()->user->id.' AND concept_id IN (SELECT id FROM tpl_concept WHERE root<>id AND root='.$concept->root.')')->queryScalar();
			$sumConcept = Yii::app()->db->createCommand('SELECT COUNT(id) FROM tpl_concept WHERE root<>id AND root='.$concept->root)->queryScalar();
			if ($sumConcept == $sumHasLearnt) {
				$lm = LearnerConcept::model()->findByPk(array('concept_id'=>$concept->root, 'learner_id'=>Yii::app()->user->id));
				$lm->learnt_at = date('Y-m-d H:i:s', time());;
				$lm->status = 2;
				$lm->save();
			}
		}
			
		
		echo '<span class="date-time pull-right">Learnt at: '.Helpers::datatime_feed($learnerConcept->learnt_at).'</span>';
		Yii::app()->end();
	}
	
	public function getTags($id) {
		if ($id == '')
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($id, 'Concept');
		
		if ($model->isModule()) {
			$tagsArr = Yii::app()->db->createCommand('SELECT tags FROM tpl_concept WHERE root='.$model->id)->queryAll();
			
			if ($tagsArr == null) {
				if ($model->tags == '')
					return '';
				else {
					$tagArr = explode(', ', $model->tags);
					sort($tagArr);
				}
			} else {
				$tagArray = array();
				foreach ($tagsArr as $tags) {
					$tArr = explode(', ', $tags['tags']);
					foreach ($tArr as $key => $value) {
						if (array_key_exists($value, $tagArray))
							$tagArray[$value]++;
						else
							$tagArray[$value] = 1;
					}
				}
				arsort($tagArray);
				// tags directly belong to the module should be in the first order
				if ($model->tags != '')
					$tagArr = explode(', ', $model->tags);
				else
					$tagArr = array();
				
				foreach ($tagArray as $key => $value)
					if (!in_array($key, $tagArr))
						array_push($tagArr, $key);
			} 
		} else {
			if ($model->tags == '')
				return '';
			$tagArr = explode(', ', $model->tags);
			sort($tagArr);
		}
		
		$str = '';
		foreach ($tagArr as $tag) {
			
			$content = '';
			
			$conceptArr = Yii::app()->db->createCommand()
				->select('id, title')
				->from('tpl_concept')
				->where('tags LIKE \'%'.$tag.'%\'')
				->order('lft')
				->queryAll();
			
			foreach ($conceptArr as $concept) {
				$content .= CHtml::tag('a', array('class'=>'label label-success', 'href'=>$this->createUrl($concept['id'])), $concept['title']).' ';
			}
			
			$str .= ' '.CHtml::tag('a', array(
					'class'=>'label label-info concept-tag',
					'rel'=>'popover',
					'data-placement'=>'bottom',
					'data-title'=>'<b>Relative concepts</b>',
					'data-content'=>$content,
					), $tag);
		}
		return $str;
	}
	
	public function actionPreview($id) { // preview for module
		//has registered
		$learnerConcept = LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
				array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$id));
		if ($learnerConcept !=null)
			$this->redirect(Yii::app()->homeUrl.'/concept/'.$id);
	
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
	
	public function actionInitTagBarsAjax($id) {
		$arr = array();
		
		// askTag
		$askTagArr = Yii::app()->db->createCommand()
			->select('tags')
			->from('tpl_ask')
			->where('concept_id=:concept_id', array(':concept_id'=>$id))
			->queryAll();
		
		$askTags = array();
		foreach ($askTagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $askTags))
					$askTags[$t]++;
				else
					$askTags[$t] = 1;
			}
		}
		
		if (array_key_exists('', $askTags))
			unset($askTags['']);
		
		arsort($askTags);
		
		$askTagBarStr = '<b>Tag:</b> '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all');
		foreach ($askTags as $key => $value)
			$askTagBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
		
		$arr['askTagBar'] = $askTagBarStr;
		// end-askTag
		
		// noteTag
		$noteTagArr = Yii::app()->db->createCommand()
			->select('tags')
			->from('tpl_note')
			->where('concept_id=:concept_id AND learner_id=:learner_id', array(':concept_id'=>$id, ':learner_id'=>Yii::app()->user->id))
			->queryAll();
		
		$noteTags = array();
		foreach ($noteTagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $noteTags))
					$noteTags[$t]++;
				else
					$noteTags[$t] = 1;
			}
		}
		
		if (array_key_exists('', $noteTags))
			unset($noteTags['']);
		
		arsort($noteTags);
		
		$noteTagBarStr = '<b>Tag:</b> '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all');
		foreach ($noteTags as $key => $value)
			$noteTagBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
		
		$arr['noteTagBar'] = $noteTagBarStr;
		// end-noteTag
		
		// todoTag
		$todoTagArr = Yii::app()->db->createCommand()
			->select('tags')
			->from('tpl_todo')
			->where('concept_id=:concept_id and status=:status and learner_id=:learner_id', array(':concept_id'=>$id, ':status'=>Todo::STATUS_UNDONE, ':learner_id'=>Yii::app()->user->id))
			->queryAll();
		
		$todoTags = array();
		foreach ($todoTagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $todoTags))
					$todoTags[$t]++;
				else
					$todoTags[$t] = 1;
			}
		}
		
		if (array_key_exists('', $todoTags))
			unset($todoTags['']);
		
		arsort($todoTags);
		
		$todoTagBarStr = '<b>Tag:</b> '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all');
		foreach ($todoTags as $key => $value)
			$todoTagBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
	
		$arr['todoTagBar'] = $todoTagBarStr;
		// end-todoTag
		
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function actionUpdateAskTagBar() {
		
		$tag='';
		if(isset($_POST['tag']))
			$tag=CHtml::decode($_POST['tag']);
	
		$filter_by='';
		if (isset($_POST['filter_by']))
			$filter_by=$_POST['filter_by'];
	
		$concept_id='';
		if (isset($_POST['concept_id']))
			$concept_id=$_POST['concept_id'];
	
		$tagsBarStr = '<b>Tag:</b> ';
		$tagsBarStr .= $tag == '' ? CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all') : CHtml::tag('span', array('class'=>'label tag', 'id'=>'all-tag'), 'all');
	
	
		$hasTagInTagsBar = false;
	
		if ($filter_by == '') {
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_ask')
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_ask')
				->where('concept_id=:concept_id', array(':concept_id'=>$concept_id))
				->queryAll();
				
		} elseif ($filter_by == 'myquestions') {
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_ask')
				->where('learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_ask')
				->where('concept_id=:concept_id and learner_id=:learner_id', array(':concept_id'=>$concept_id,':learner_id'=>Yii::app()->user->id))
				->queryAll();
		} elseif ($filter_by == 'myanswers') {
			$asks = Yii::app()->db->createCommand()
			->selectDistinct('ask_id')
			->from('tpl_answer')
			->where('learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
			->queryAll();
			if (count($asks) != 0) {
				$askIdStr = '(';
				foreach ($asks as $ask)
					$askIdStr .= '"'.$ask['ask_id'].'",';
				$askIdStr = substr($askIdStr, 0, -1);
				$askIdStr .= ')';
	
				$tagArr = array();
				if ($concept_id == '')
					$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where('id IN '.$askIdStr)
					->queryAll();
				else
					$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where('concept_id=:concept_id and id IN '.$askIdStr, array(':concept_id'=>$concept_id))
					->queryAll();
			}
		}
	
		$tags = array();
		foreach ($tagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $tags))
					$tags[$t]++;
				else
					$tags[$t] = 1;
			}
		}
		arsort($tags);
		if (array_key_exists('', $tags))
			unset($tags['']);
	
		foreach ($tags as $key=>$value) {
			if ($key == $tag) {
				$hasTagInTagsBar = true;
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$key), $key.'('.$value.')');
			} else
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
		}
		if (!$hasTagInTagsBar && $tag != '')
			$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$tag), $tag.'(0)');

		echo $tagsBarStr;
		Yii::app()->end();
	}
	
	public function actionUpdateNoteTagBar() {
		$tag='';
		if(isset($_POST['tag']))
			$tag=CHtml::decode($_POST['tag']);
		
		$interval='';
		if (isset($_POST['interval']))
			$interval=$_POST['interval'];
		
		$concept_id='';
		if (isset($_POST['concept_id']))
			$concept_id=$_POST['concept_id'];
		
		$tagsBarStr = '<b>Tag:</b> ';
		$tagsBarStr .= $tag == '' ? CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all') : CHtml::tag('span', array('class'=>'label tag', 'id'=>'all-tag'), 'all');
		
		$hasTagInTagsBar = false;
		
		if ($interval == '') {
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_note')
				->where('learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_note')
				->where('concept_id=:concept_id AND learner_id=:learner_id', array(':concept_id'=>$concept_id, ':learner_id'=>Yii::app()->user->id))
				->queryAll();
		} else {
			$whereInterval = '';
				
			$now = date('Y-m-d');
			switch ($interval) {
				case 'today': {
					$whereInterval = "tpl_note.create_at >= '".date('Y-m-d')."' AND tpl_note.create_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."'";
					break;
				}
				case 'week': {
					$whereInterval = "tpl_note.create_at >= '".date('Y-m-d', strtotime('this week'))."' AND tpl_note.create_at < '".date('Y-m-d', strtotime('next week'))."'";
					break;
				}
				case 'month': {
					$whereInterval = "tpl_note.create_at >= '".date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y")))."' AND tpl_note.create_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")+1, 1, date("Y")))."'";
					break;
				}
			}
		
			$tagArr = array();
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_note')
				->where($whereInterval.' AND learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_note')
				->where($whereInterval.' AND concept_id=:concept_id AND learner_id=:learner_id', array(':concept_id'=>$concept_id, ':learner_id'=>Yii::app()->user->id))
				->queryAll();
		}
		
		$tags = array();
		foreach ($tagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $tags))
					$tags[$t]++;
				else
					$tags[$t] = 1;
			}
		}
		arsort($tags);
		if (array_key_exists('', $tags))
			unset($tags['']);
		
		foreach ($tags as $key=>$value) {
			if ($key == $tag) {
				$hasTagInTagsBar = true;
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$key), $key.'('.$value.')');
			} else
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
		}
		if (!$hasTagInTagsBar && $tag != '')
			$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$tag), $tag.'(0)');
		
		echo $tagsBarStr;
		Yii::app()->end();
	}
	
	public function actionUpdateTodoTagBar() {
		$tag='';
		if(isset($_POST['tag']))
			$tag=CHtml::decode($_POST['tag']);
		
		$interval='';
		if (isset($_POST['interval']))
			$interval=$_POST['interval'];
		
		$concept_id='';
		if (isset($_POST['concept_id']))
			$concept_id=$_POST['concept_id'];
		
		$whereStatus='';
		if (isset($_POST['status'])) {
			if ($_POST['status'] == '')
				$whereStatus = 'status= \'\''.$_POST['status'].' AND ';
			else
				$whereStatus = 'status='.$_POST['status'].' AND ';
		}
		
		$tagsBarStr = '<b>Tag:</b> ';
		$tagsBarStr .= $tag == '' ? CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all') : CHtml::tag('span', array('class'=>'label tag', 'id'=>'all-tag'), 'all');
		
		$hasTagInTagsBar = false;
		
		if ($interval == '') {
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_todo')
				->where($whereStatus.'learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_todo')
				->where($whereStatus.'concept_id=:concept_id AND learner_id=:learner_id', array(':concept_id'=>$concept_id, ':learner_id'=>Yii::app()->user->id))
				->queryAll();
		} else {
			$whereInterval = '';
		
			$now = date('Y-m-d');
			switch ($interval) {
				case 'today': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d')."' AND tpl_todo.start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."' AND ";
					break;
				}
				case 'week': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d', strtotime('this week'))."' AND tpl_todo.start_at < '".date('Y-m-d', strtotime('next week'))."' AND ";
					break;
				}
				case 'month': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y")))."' AND tpl_todo.start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")+1, 1, date("Y")))."' AND ";
					break;
				}
			}
		
			$tagArr = array();
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_todo')
				->where($whereStatus.$whereInterval.'learner_id=:learner_id', array(':learner_id'=>Yii::app()->user->id))
				->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
				->select('tags')
				->from('tpl_todo')
				->where($whereStatus.$whereInterval.'concept_id=:concept_id AND learner_id=:learner_id', array(':concept_id'=>$concept_id, ':learner_id'=>Yii::app()->user->id))
				->queryAll();
		}
		
		$tags = array();
		foreach ($tagArr as $tg) {
			$ts = explode(', ', $tg['tags']);
			foreach ($ts as $t) {
				if (array_key_exists($t, $tags))
					$tags[$t]++;
				else
					$tags[$t] = 1;
			}
		}
		arsort($tags);
		if (array_key_exists('', $tags))
			unset($tags['']);
		
		foreach ($tags as $key=>$value) {
			if ($key == $tag) {
				$hasTagInTagsBar = true;
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$key), $key.'('.$value.')');
			} else {
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
			}
		}
		if (!$hasTagInTagsBar && $tag != '')
			$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$tag), $tag.'(0)');
		
		echo $tagsBarStr;
		Yii::app()->end();
	}
	
	public function actionFetchUsers() {
		if (!isset($_POST['concept_id']) || !isset($_POST['rank_by']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		$rank_by = $_POST['rank_by'];
		
		$isRoot = $concept_id == Yii::app()->db->createCommand('SELECT root FROM tpl_concept WHERE id='.$concept_id)->queryScalar();
		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
	
		if ($rank_by == 'questions') {
			if ($isRoot)
				$sql = 'SELECT t.id AS id, t.username AS username, COUNT(a.learner_id) AS countAsk from tpl_user AS t JOIN tpl_ask AS a ON a.learner_id=t.id WHERE a.concept_id IN (SELECT id FROM tpl_concept WHERE root='.$concept_id.') GROUP BY a.learner_id ORDER BY COUNT(a.learner_id) DESC';
			else
				$sql = 'SELECT t.id AS id, t.username AS username, COUNT(a.learner_id) AS countAsk from tpl_user AS t JOIN tpl_ask AS a ON a.learner_id=t.id WHERE a.concept_id='.$concept_id.' GROUP BY a.learner_id ORDER BY COUNT(a.learner_id) DESC';
			$userArr = Yii::app()->db->createCommand($sql)->queryAll();
			
			if (count($userArr) == 0) {
				echo '<div style="margin: 16px 16px 8px 16px;">No learner asked yet.</div>';
				Yii::app()->end();
			}
			
			foreach ($userArr as $user)
				if ($user['id'] == Yii::app()->user->id)
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['countAsk'].' question(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
				else
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['countAsk'].' question(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		} else { // $rank_by == 'answers'
			if ($isRoot)
				$sql = 'SELECT t.id AS id, t.username AS username, COUNT(a.learner_id) AS countAnswer FROM tpl_user AS t JOIN tpl_answer AS a ON t.id=a.learner_id WHERE a.ask_id IN (SELECT id FROM tpl_ask where concept_id IN (SELECT id FROM tpl_concept WHERE root='.$concept_id.')) GROUP BY a.learner_id ORDER BY COUNT(a.learner_id) DESC';
			else
				$sql = 'SELECT t.id AS id, t.username AS username, COUNT(a.learner_id) AS countAnswer FROM tpl_user AS t JOIN tpl_answer AS a ON t.id=a.learner_id WHERE a.ask_id IN (SELECT id FROM tpl_ask where concept_id='.$concept_id.') GROUP BY a.learner_id ORDER BY COUNT(a.learner_id) DESC';
			$userArr = Yii::app()->db->createCommand($sql)->queryAll();
			
			if (count($userArr) == 0) {
				echo '<div style="margin: 16px 16px 8px 16px;">No learner answered yet.</div>';
				Yii::app()->end();
			}
			
			foreach ($userArr as $user)
				if ($user['id'] == Yii::app()->user->id)
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['countAnswer'].' answer(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
				else
					$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['countAnswer'].' answer(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		}
		echo $rtn;
		Yii::app()->end();
			
	}
	
	public function actionFetchUsersLearning() {
		if (!isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		$sql = 'SELECT t.id AS id, t.username AS username, lc.lastaction_at AS lastaction_at from tpl_user AS t JOIN tpl_learner_concept AS lc ON t.id=lc.learner_id WHERE lc.concept_id='.$concept_id.' AND lc.status=1 ORDER BY lc.lastaction_at DESC limit 5';
		$userArr = Yii::app()->db->createCommand($sql)->queryAll();
			
		if (count($userArr) == 0) {
			echo '<div style="margin: 16px 16px 8px 16px;">No learner is learning.</div>';
			Yii::app()->end();
		}
		
		foreach ($userArr as $user)
			if ($user['id'] == Yii::app()->user->id)
				$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">At '.Helpers::datatime_trim($user['lastaction_at']).'</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
			else
				$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">At '.Helpers::datatime_trim($user['lastaction_at']).'</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
				
		echo $rtn;
		Yii::app()->end();
		
	}
	
	public function actionFetchUsersLearnt() {
		if (!isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		$sql = 'SELECT t.id AS id, t.username AS username, lc.learnt_at AS lastaction_at from tpl_user AS t JOIN tpl_learner_concept AS lc ON t.id=lc.learner_id WHERE lc.concept_id='.$concept_id.' AND lc.status=2 ORDER BY lc.learnt_at limit 5';
		$userArr = Yii::app()->db->createCommand($sql)->queryAll();
			
		if (count($userArr) == 0) {
			echo '<div style="margin: 16px 16px 8px 16px;">No learner has learnt.</div>';
			Yii::app()->end();
		}
		
		foreach ($userArr as $user)
			if ($user['id'] == Yii::app()->user->id)
			$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">By '.Helpers::datatime_trim($user['lastaction_at']).'</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		else
			$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">By '.Helpers::datatime_trim($user['lastaction_at']).'</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		
		echo $rtn;
		Yii::app()->end();
	}
	
	public function actionFetchUsersByQuizScore() {
		if (!isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		$isRoot = $concept_id == Yii::app()->db->createCommand('SELECT root FROM tpl_concept WHERE id='.$concept_id)->queryScalar();
		
		if ($isRoot) {
			// isModule
			$sql = 'SELECT learner_id, username, score FROM tpl_quiz AS q, tpl_user AS u WHERE u.id=learner_id AND done_at IS NOT NULL AND concept_id IN (SELECT id FROM tpl_concept WHERE id<>root AND root='.$concept_id.')';
		} else {
			// is not module
			$sql = 'SELECT learner_id, username, score FROM tpl_quiz AS q, tpl_user AS u WHERE u.id=learner_id AND done_at IS NOT NULL AND concept_id='.$concept_id;
		}
		
		$quizArr = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($quizArr) == 0) {
			echo '<div style="margin: 16px 16px 8px 16px;">No learner\'s taken quizs.</div>';
			Yii::app()->end();
		}	
		$userArr = array();
		foreach ($quizArr as $quiz) {
			$s = explode('/', $quiz['score']);
			if (array_key_exists($quiz['learner_id'], $userArr)) {
				$userArr[$quiz['learner_id']]['correct'] += intval($s[0]);
				$userArr[$quiz['learner_id']]['total'] += intval($s[1]);
			} else {
				$userArr[$quiz['learner_id']]['correct'] = intval($s[0]);
				$userArr[$quiz['learner_id']]['total'] = intval($s[1]);
				$userArr[$quiz['learner_id']]['username'] = $quiz['username'];
			}
			$userArr[$quiz['learner_id']]['score'] = $userArr[$quiz['learner_id']]['correct'] * 100 / $userArr[$quiz['learner_id']]['total'];
		}
		
		uksort($userArr, function($a, $b){
			return $a['score'] > $b['score'];
		});
	
		$userId = Yii::app()->user->id;
		
		foreach ($userArr as $id => $attr)
			if ($id == $userId)
				$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$id.'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$attr['username'].'</div>
		<div style="color: #333">Correct rate: '.round($attr['score'], 1).'%</div>
		<input id="data_id" type="hidden" value="'.$id.'"/>
	</div>
</div>';
			else
				$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$id.'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$attr['username'].'</div>
		<div style="color: #333">Correct rate: '.round($attr['score'], 1).'%</div>
		<input id="data_id" type="hidden" value="'.$id.'"/>
	</div>
</div>';
		
		echo $rtn;
		Yii::app()->end();
		
	}
	
	public function actionFetchConceptsByIncorrectAnswers() {
		// order by incorrectly answered questions
		if (!isset($_POST['module_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$module_id = $_POST['module_id'];
		
		$sql = 'SELECT concept_id, title, c.description, FORMAT(SUBSTRING(score, 1, LENGTH(score)-LOCATE(\'/\', score))/SUBSTRING(score, LOCATE(\'/\', score)+1), 2) AS rate FROM tpl_quiz AS q, tpl_concept AS c WHERE q.concept_id=c.id AND c.root<>c.id AND score IS NOT NULL AND c.root='.$module_id.' AND q.learner_id='.Yii::app()->user->id.' ORDER BY rate LIMIT 5';
		$conceptArr = Yii::app()->db->createCommand($sql)->queryAll();
		
		$rtn = '';
		foreach ($conceptArr as $concept)
			$rtn .= '<div><span style="margin: 0 0 6px 16px; line-height: 28px;" class="concepts-related-item"><a class="label label-success" rel="tooltip" data-placement="right" title="Correct rate: '.$concept['rate'].'" href="'.Yii::app()->homeUrl.'/concept/'.$concept['concept_id'].'">'.Helpers::string_len($concept['title'], 40).'</a></span></div>';
		
		echo $rtn .'';
		Yii::app()->end();
	}
	
	public function actionFetchConceptsRelated() {
		// order by how many tags mached
		if (!isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		
		$concept = $this->loadModel($concept_id, 'Concept');
		$concepts = $concept->getConceptsRelated();
	
		$rtn = '';
		foreach ($concepts as $concept)
			$rtn .= '<div style="margin: 0 0 6px 16px;" class="concepts-related-item"><a class="label label-success" rel="tooltip" data-placement="right" title="'.$concept->title.'" href="'.Yii::app()->homeUrl.'/concept/'.$concept->id.'">'.Helpers::string_len($concept->title, 40).'</a></div>';
		
		echo $rtn .'';
		Yii::app()->end();
	}
	
	public function actionFetchConceptsByLearner() {
		// order by how many users are learning / have learnt
		if (!isset($_POST['module_id']) || !isset($_POST['order_by']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		$module_id = $_POST['module_id'];
		$order_by = $_POST['order_by'];
		
		$sql = 'SELECT id, title, description, COUNT(id) AS count FROM tpl_concept, tpl_learner_concept WHERE id=concept_id AND root<>id AND root='.$module_id.' AND status='.($order_by == 'learning' ? LearnerConcept::STATUS_INPROGRESS : LearnerConcept::STATUS_COMPLETED).' GROUP BY concept_id ORDER BY count(id) DESC LIMIT 5';
		
		$conceptArr = Yii::app()->db->createCommand($sql)->queryAll();
		
		$rtn = '';
		foreach ($conceptArr as $concept)
			$rtn .= '<div><span style="margin: 0 0 6px 16px; line-height: 28px;" class="concepts-related-item"><a class="label label-success" rel="tooltip" data-placement="right" title="'.$concept['count'].' learner(s)" href="'.Yii::app()->homeUrl.'/concept/'.$concept['id'].'">'.Helpers::string_len($concept['title'], 40).'</a></span></div>';
		
		echo $rtn .'';
		Yii::app()->end();
	}
	
	public function actionFetchModule() {
		// order by how many users have registered
		$filter_by = 'learning';
		if (isset($_POST['filter_by']))
			$filter_by = $_POST['filter_by'];
		
		$sql = 'SELECT c.id, title, COUNT(learner_id) AS count FROM tpl_learner_concept AS lc, tpl_concept AS c WHERE c.root=c.id AND status='.($filter_by == 'learning' ? LearnerConcept::STATUS_INPROGRESS : LearnerConcept::STATUS_COMPLETED).' AND c.id=lc.concept_id GROUP BY concept_id ORDER BY COUNT(learner_id) DESC LIMIT 5';
		
		$moduleArr = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (count($moduleArr) == 0) {
			echo '<div style="margin: 16px 16px 8px 16px;">No module is found.</div>';
			Yii::app()->end();
		}
		
		$rtn = '';
		foreach ($moduleArr as $module) {
			// has registered?
			if (null == Yii::app()->db->createCommand('SELECT learner_id FROM {{learner_concept}} WHERE learner_id='.Yii::app()->user->id.' AND concept_id='.$module['id'])->queryScalar())
				$rtn .= '<div><span style="margin: 0 0 6px 16px; line-height: 28px;" class="concepts-related-item" rel="tooltip" data-placement="right" title="'.$module['count'].' learner(s)"><a class="label label-success" href="'.Yii::app()->homeUrl.'/concept/preview/'.$module['id'].'">'.Helpers::string_len($module['title'], 40).'</a></span></div>';
			else
				$rtn .= '<div><span style="margin: 0 0 6px 16px; line-height: 28px;" class="concepts-related-item" rel="tooltip" data-placement="right" title="'.$module['count'].' learner(s)"><a class="label label-success" href="'.Yii::app()->homeUrl.'/concept/'.$module['id'].'">'.Helpers::string_len($module['title'], 40).'</a></span></div>';
		}						
		echo $rtn .'';
		Yii::app()->end();
	}
	
	public function actionFetchUsersRankByModule() {
		// order by how many modules users are learning / have learnt
		$rank_by = 'learning';
		if (isset($_POST['rank_by']))
			$rank_by = $_POST['rank_by'];
		
		$baseUrl = Yii::app()->baseUrl;
		$rtn = '';
		
		$sql = 'SELECT t.id AS id, t.username, COUNT(lc.concept_id) AS count FROM tpl_user AS t, tpl_learner_concept AS lc WHERE t.id=lc.learner_id AND lc.concept_id IN (SELECT id FROM tpl_concept AS c WHERE c.id=c.root ) AND lc.status='.($rank_by == 'learning' ? LearnerConcept::STATUS_INPROGRESS : LearnerConcept::STATUS_COMPLETED).' GROUP BY learner_id ORDER BY COUNT(concept_id) limit 5';
		$userArr = Yii::app()->db->createCommand($sql)->queryAll();
			
		if (count($userArr) == 0) {
			echo '<div style="margin: 16px 16px 8px 16px;">No learner has learnt.</div>';
			Yii::app()->end();
		}
		
		foreach ($userArr as $user)
			if ($user['id'] == Yii::app()->user->id)
			$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="It\'s you.">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' module(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		else
			$rtn .= '
<div style="margin: 16px 16px 8px 16px;" class="user-rank-item" rel="tooltip" data-placement="right" title="Send a message" data-toggle="modal" href="#message-modal">
	<img src="'.$baseUrl.'/uploads/images/profile-avatar/'.$user['id'].'" style="height: 44px; width: 44px;">
	<div style="margin-left: 60px; margin-top: -44px;">
		<div class="name-user">'.$user['username'].'</div>
		<div style="color: #333">'.$user['count'].' module(s)</div>
		<input id="data_id" type="hidden" value="'.$user['id'].'"/>
	</div>
</div>';
		
		echo $rtn;
		Yii::app()->end();
		}

}
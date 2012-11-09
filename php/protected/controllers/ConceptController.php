<?php

class ConceptController extends GxController {

	public $layout='//layouts/concept';

	public function actionView($id) {
		$model = $this->loadModel($id, 'Concept');
		
		if ($model->isModule())
			$this->redirect(Yii::app()->homeUrl.'/module/'.$id);
		
		$learnerModule = LearnerConcept::model()->find('learner_id=:learnerID and concept_id=:conceptID',
				array(':learnerID'=>Yii::app()->user->id, ':conceptID'=>$model->root));
		
		if ($learnerModule == null) {
			$this->redirect(Yii::app()->homeUrl.'/module/'.$model->root);
		} else {
			$learnerID = Yii::app()->user->id;
			$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$id, 'learner_id'=>$learnerID));
			if ($learnerConcept != null) {
				$learnerConcept->lastaction_at = date('Y-m-d H:i:s', time());
				$learnerConcept->save();
			} else {
				$learnerConcept = new LearnerConcept;
				$learnerConcept->concept_id = $id;
				$learnerConcept->learner_id = $learnerID;
				$learnerConcept->create_at = date('Y-m-d H:i:s', time());
				$learnerConcept->lastaction_at = $learnerConcept->create_at;
				$learnerConcept->save();	
			}
		}	
		
		if ($learnerConcept->status == LearnerConcept::STATUS_COMPLETED)
			$learnt_at = $learnerConcept->learnt_at;
		else
			$learnt_at = null;
		
		$canHasQuiz = $model->questionCount == 0 ? 'no' : 'yes';
		
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
		
		$this->render('view', array(
			'model' => $model,
			'previousConcept' => $model->previousConcept,
			'nextConcept'=> $model->nextConcept,
			'canHasQuiz' => $canHasQuiz,
			'learnt_at' => $learnt_at,
			'dataProvider_ask' => $dataProvider_ask,
			'dataProvider_note' => $dataProvider_note,
			'dataProvider_todo' => $dataProvider_todo,
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
		$dataProvider = new CActiveDataProvider('Concept');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
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
		
		if ($model->tags == '')
			return '';
		
		$tagArr = explode(', ', $model->tags);
		$str = '<hr><p><b>Tag:</b>';
		sort($tagArr);
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
		return $str.'</p>';
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
					$whereInterval = "tpl_note.create_at >= '".date('Y-m-d', strtotime('monday'))."' AND tpl_note.create_at < '".date('Y-m-d', strtotime('next monday'))."'";
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
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d', strtotime('monday'))."' AND tpl_todo.start_at < '".date('Y-m-d', strtotime('next monday'))."' AND ";
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
	
	public function actionFetchConceptsRelated() {
		
		// if only they have same tag(s)
		// how many tags mached
		if (!isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$concept_id = $_POST['concept_id'];
		
		$concept = $this->loadModel($concept_id, 'Concept');
		$concepts = $concept->getConceptsRelated();
	
		$rtn = '';
		foreach ($concepts as $concept)
			$rtn .= '<div style="margin: 0 0 6px 16px;" class="concepts-related-item"><a class="label label-success" rel="tooltip" data-placement="right" title="'.$concept->description.'" href="'.Yii::app()->homeUrl.'/concept/'.$concept->id.'">'.$concept->title.'</a></div>';
		
		echo $rtn .'';
		Yii::app()->end();
		
	}

}
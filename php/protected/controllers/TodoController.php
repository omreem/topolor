<?php

class TodoController extends GxController {

	public $layout='//layouts/site_column';

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Todo'),
		));
	}

	public function actionCreate() {
		$model = new Todo;

		if (isset($_POST['Todo'])) {
			$model->setAttributes($_POST['Todo']);
			
			$startAtDate = $_POST['start_at_date'];
			$startAtTime = $_POST['start_at_time'];
			
			$endAtDate = $_POST['end_at_date'];
			$endAtTime = $_POST['end_at_time'];
			
			$startH = intval(substr($startAtTime, 0, 2));
			if(substr($startAtTime, 0, 2) == '12' && substr($startAtTime, -2, 1) == 'A')
				$startH = '00';
			elseif (substr($startAtTime, 0, 2) != '12' && substr($startAtTime, -2, 1) == 'P')
				$startH += 12;
			
			$model->start_at = substr($startAtDate, 6,4).'-'.substr($startAtDate, 3,2).'-'.substr($startAtDate, 0,2).' '.$startH.substr($startAtTime, 2,3).':00';
			
			$endH = intval(substr($endAtTime, 0, 2));
			if(substr($endAtTime, 0, 2) == '12' && substr($endAtTime, -2, 1) == 'A')
				$endH = '00';
			elseif (substr($endAtTime, 0, 2) != '12' && substr($endAtTime, -2, 1) == 'P')
			$endH += 12;
				
			$model->end_at = substr($endAtDate, 6,4).'-'.substr($endAtDate, 3,2).'-'.substr($endAtDate, 0,2).' '.$endH.substr($endAtTime, 2,3).':00';

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
		$model = $this->loadModel($id, 'Todo');

		if (isset($_POST['Todo'])) {
			$model->setAttributes($_POST['Todo']);

			if ($model->save())
				$this->redirect(array('view', 'id' => $model->id));
		}

		$this->render('update', array('model' => $model));
	}
	
	public function actionUpdateAjax() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_POST['id'], 'Todo');
		
		if (isset($_POST['status']))
			$model->status = $_POST['status'];
		
		if (isset($_POST['done_at']))
			$model->done_at = $_POST['done_at'];
		
		if (isset($_POST['title']))
			$model->title = $_POST['title'];
		
		if (isset($_POST['description']))
			$model->description = $_POST['description'];
		
		if (isset($_POST['tags']))
			$model->tags = $_POST['tags'];
		
		if (isset($_POST['startTime']) && isset($_POST['startDate']) && isset($_POST['endTime']) && isset($_POST['endDate'])) {
			
			$startAtDate = $_POST['startDate'];
			$startAtTime = $_POST['startTime'];
				
			$endAtDate = $_POST['endDate'];
			$endAtTime = $_POST['endTime'];
			
			$startH = intval(substr($startAtTime, 0, 2));
			if(substr($startAtTime, 0, 2) == '12' && substr($startAtTime, -2, 1) == 'A')
				$startH = '00';
			elseif (substr($startAtTime, 0, 2) != '12' && substr($startAtTime, -2, 1) == 'P')
			$startH += 12;
				
			$model->start_at = substr($startAtDate, 6,4).'-'.substr($startAtDate, 3,2).'-'.substr($startAtDate, 0,2).' '.$startH.substr($startAtTime, 2,3).':00';
				
			$endH = intval(substr($endAtTime, 0, 2));
			if(substr($endAtTime, 0, 2) == '12' && substr($endAtTime, -2, 1) == 'A')
				$endH = '00';
			elseif (substr($endAtTime, 0, 2) != '12' && substr($endAtTime, -2, 1) == 'P')
			$endH += 12;
			
			$model->end_at = substr($endAtDate, 6,4).'-'.substr($endAtDate, 3,2).'-'.substr($endAtDate, 0,2).' '.$endH.substr($endAtTime, 2,3).':00';
		}
		
		if ($model->save())
			echo 'success';
		Yii::app()->end();
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Todo')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$newTodo=new Todo;
		
		$model=new Todo('search');
		$model->unsetAttributes();
		
		$model->learner_id = Yii::app()->user->id;
		
		if(isset($_GET['status']))
			$model->status=$_GET['status'];
		else
			$model->status = Todo::STATUS_UNDONE;
		
		$interval='';
		if (isset($_GET['interval']))
			$interval=$_GET['interval'];
		
		$tag = '';
		if(isset($_GET['tag']))
			$tag = CHtml::decode($_GET['tag']);
		
		$concept_id = '';
		if (isset($_GET['concept_id']))
			$concept_id = $_GET['concept_id'];
	
		$this->render('index',array(
			'dataProvider'=>$model->search($interval, $tag, $concept_id),
			'model'=>$model,
			'newTodo'=>$newTodo,
		));
	}

	public function actionAdmin() {
		$model = new Todo('search');
		$model->unsetAttributes();

		if (isset($_GET['Todo']))
			$model->setAttributes($_GET['Todo']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionGetTags() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_GET['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	
		$model = $this->loadModel($_GET['id'], 'Todo');
		echo implode(' ', $model->tagLabels);
		Yii::app()->end();
	}
	
	/**
	 * Suggests tags based on the current user input.
	 * This is called via AJAX when the user is entering the tags input.
	 */
	public function actionSuggestTags() {
		if(isset($_GET['q']) && ($keyword=trim($_GET['q']))!=='')
		{
			$tags=Tag::model()->suggestTags($keyword, 'todo');
			if($tags!==array())
				echo implode("\n",$tags);
		}
	}
	
	public function actionUpdateFiltersBar() {
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
	
		$ConceptsBarStr = '<b>Concept:</b> ';
		$ConceptsBarStr .= $concept_id == '' ? CHtml::tag('span', array('class'=>'label label-success concept selected', 'id'=>'all-concept'), 'all') : CHtml::tag('span', array('class'=>'label concept', 'id'=>'all-concept'), 'all');
	
		$hasTagInTagsBar = false;
		$hasConceptInConceptsBar = false;
	
		if ($interval == '') {
			// for-tagsBar
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
			// end-for-tagsBar
	
			// for-conceptsBar
			if ($tag == '') {
				$sql = 'select concept_id as id, tpl_concept.title as title, count(*) as frequency 
					from tpl_todo 
					join tpl_concept on tpl_concept.id=tpl_todo.concept_id 
					where learner_id=1 AND concept_id<>root 
					group by concept_id
					order by frequency DESC, title 
					';
				$concepts = Yii::app()->db->createCommand($sql)->queryAll();		
			} else
				$concepts = Yii::app()->db->createCommand()
				->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
				->from('tpl_todo')
				->join('tpl_concept', 'tpl_concept.id=tpl_todo.concept_id')
				->where($whereStatus.'tpl_todo.tags LIKE "%'.$tag.'%" AND learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
				->group('concept_id')
				->order('frequency DESC, title')
				->queryAll();
			// end-for-conceptsBar
		} else {
			$whereInterval = '';
				
			$now = date('Y-m-d');
			switch ($interval) {
				case 'today': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d')."' AND tpl_todo.start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")))."' AND ";
					break;
				}
				case 'week': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d', strtotime('last monday'))."' AND tpl_todo.start_at < '".date('Y-m-d', strtotime('monday'))."' AND ";
					break;
				}
				case 'month': {
					$whereInterval = "tpl_todo.start_at >= '".date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y")))."' AND tpl_todo.start_at < '".date('Y-m-d', mktime(0, 0, 0, date("m")+1, 1, date("Y")))."' AND ";
					break;
				}
			}
	
			// for-tagsBar
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
			// end-for-tagsBar
	
			// for-conceptsBar
			if ($tag == '')
				$concepts = Yii::app()->db->createCommand()
				->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
				->from('tpl_todo')
				->join('tpl_concept', 'tpl_concept.id=tpl_todo.concept_id')
				->group('concept_id')
				->where($whereStatus.$whereInterval.'learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
				->order('frequency DESC, title')
				->queryAll();
			else
				$concepts = Yii::app()->db->createCommand()
				->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
				->from('tpl_todo')
				->join('tpl_concept', 'tpl_concept.id=tpl_todo.concept_id')
				->where($whereStatus.$whereInterval.'tpl_todo.tags LIKE "%'.$tag.'%" AND learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
				->group('concept_id')
				->order('frequency DESC, title')
				->queryAll();
			// end-for-conceptsBar
		}
	
		// for-tagsBar
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
		// end-for-tagsBar
	
		// for-conceptsBar
		foreach ($concepts as $concept) {
			$ConceptsBarStr .= $concept_id != $concept['id'] ? ' '.CHtml::tag('span', array('class'=>'label concept', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')') : ' '.CHtml::tag('span', array('class'=>'label label-success concept selected', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')');
			if ($concept_id == $concept['id'])
				$hasConceptInConceptsBar = true;
		}
	
		if (!$hasConceptInConceptsBar && $concept_id != '')
			$ConceptsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-success concept selected', 'name'=>$concept_id), Concept::model()->findByPk($concept_id)->title.'(0)');
		// end-for-conceptsBar
	
		$arr = array();
		$arr['tagsBar'] = $tagsBarStr;
		$arr['conceptsBar'] = $ConceptsBarStr;
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function initTagBar() {
		$str = '<b>Tag:</b> ';
		$str .= CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all');
		$tagArr = Yii::app()->db->createCommand()
			->select('tags')
			->from('tpl_todo')
			->where('status=:status and learner_id=:learner_id', array(':status'=>Todo::STATUS_UNDONE, ':learner_id'=>Yii::app()->user->id))
			->queryAll();
		
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
		
		if (array_key_exists('', $tags))
			unset($tags['']);
		
		arsort($tags);
		
		foreach ($tags as $key => $value)
			$str .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
	
		return $str;
	}
	
	public function initConceptBar() {
		$str = '<b>Concept:</b> ';
		$str .= CHtml::tag('span', array('class'=>'label label-success concept selected', 'id'=>'all-concept'), 'all');
		$concepts = Yii::app()->db->createCommand()
			->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
			->from('tpl_todo')
			->join('tpl_concept', 'tpl_concept.id=tpl_todo.concept_id')
			->group('concept_id')
			->where('learner_id=:learner_id AND status=:status AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id, ':status'=>Todo::STATUS_UNDONE))
			->order('frequency DESC')
			->queryAll();
	
		foreach ($concepts as $concept)
			$str .= ' '.CHtml::tag('span', array('class'=>'label concept', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')');
	
		return $str;
	}
	
	public function actionCreateTagCanvas() {
		$allStr = '';
		$tagArr =  Tag::model()->findTags('todo', Yii::app()->user->id, false);
		foreach ($tagArr as $tag) {
			$allStr .= ' '.CHtml::tag('span', array('class'=>'label label-info pick-tag'), $tag->name);
		}
		
		$arr = array();
		$arr['allTags'] = $allStr;
		
		if (isset($_POST['id'])) {
			$thisTag = $this->loadModel($_POST['id'], 'Todo')->tags;
			if ($thisTag != '')
				$thisTag .= ', ';
			$arr['thisTag'] = $thisTag;
		}
		
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
}
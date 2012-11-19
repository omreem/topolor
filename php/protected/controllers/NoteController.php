<?php

class NoteController extends GxController {

	public $layout='//layouts/site_column';

	public function actionView($id) {
		
		//monitor=begin
		$this->moniter('note', 'view', 'id='.$id);
		//monitor-end
		
		
		$this->render('view', array('model' => $this->loadModel($id, 'Note')));
	}

	public function actionCreate() {
		$model = new Note;

		if (isset($_POST['Note'])) {
			$model->setAttributes($_POST['Note']);

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
		$model = $this->loadModel($id, 'Note');

		if (isset($_POST['Note'])) {
			$model->setAttributes($_POST['Note']);

			if ($model->save())
				$this->redirect(array('view', 'id' => $model->id));
		}

		$this->render('update', array('model' => $model));
	}
	
	public function actionUpdateAjax() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_POST['id'], 'Note');
		
		//monitor=begin
		$this->moniter('note', 'update', 'id='.$model->id, 'POST');
		//monitor-end
		
		
		if (isset($_POST['title']))
			$model->title = $_POST['title'];
		
		if (isset($_POST['description']))
			$model->description = $_POST['description'];
		
		if (isset($_POST['tags']))
			$model->tags = $_POST['tags'];
		
		if ($model->save()) {
			if (!isset($_POST['returnNote'])) {
				$arr = array();
				$arr['id'] = $model->id;
				$arr['title'] = $model->title;
				$arr['description'] = $model->description;
				$arr['create_at'] = Helpers::datatime_feed($model->create_at);
				header('Content-type: application/json');
				echo json_encode($arr);
			} else {
				echo 'success';
			}
		}
		Yii::app()->end();
	}

	public function actionDelete($id) {
		
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Note');
			$model->delete();
		
		//monitor=begin
		$this->moniter('note', 'delete', 'id='.$model->id);
		//monitor-end
		

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		
		//monitor=begin
		$this->moniter('note', 'index');
		//monitor-end
		
		$newNote = new Note;
		
		$model = new Note('search');
		$model->unsetAttributes();
		
		$model->learner_id = Yii::app()->user->id;
		
		$interval = '';
		if (isset($_GET['interval']))
			$interval = $_GET['interval'];
		
		$tag = '';
		if(isset($_GET['tag']))
			$tag = CHtml::decode($_GET['tag']);
		
		$concept_id = '';
		if (isset($_GET['concept_id']))
			$concept_id = $_GET['concept_id'];
		
		$this->render('index', array(
			'dataProvider' => $model->search($interval, $tag, $concept_id),
			'model' => $model,
			'newNote' => $newNote,
		));
	}

	public function actionAdmin() {
		$model = new Note('search');
		$model->unsetAttributes();

		if (isset($_GET['Note']))
			$model->setAttributes($_GET['Note']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionGetNote() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_GET['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_GET['id'], 'Note');
		$arr = array();
		$arr['id'] = $model->id;
		$arr['title'] = $model->title;
		$arr['description'] = $model->description;
		$arr['create_at'] = $model->create_at;
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();		
	}
	
	public function actionGetTags() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_GET['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_GET['id'], 'Note');
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
			$tags=Tag::model()->suggestTags($keyword, 'note');
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
		
		//monitor=begin
		$this->moniter('note', 'updateFilterBar', 'tag='.$tag.'&interval='.$interval.'&concept_id='.$concept_id, 'POST');
		//monitor-end
		

		$tagsBarStr = '<b>Tag:</b> ';
		$tagsBarStr .= $tag == '' ? CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all') : CHtml::tag('span', array('class'=>'label tag', 'id'=>'all-tag'), 'all');
	
		$conceptsBarStr = '<b>Concept:</b> ';
		$conceptsBarStr .= $concept_id == '' ? CHtml::tag('span', array('class'=>'label label-success concept selected', 'id'=>'all-concept'), 'all') : CHtml::tag('span', array('class'=>'label concept', 'id'=>'all-concept'), 'all');
	
		$hasTagInTagsBar = false;
		$hasConceptInConceptsBar = false;
	
		if ($interval == '') {
			// for-tagsBar
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
			// end-for-tagsBar
				
			// for-conceptsBar
			if ($tag == '')
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_note')
					->join('tpl_concept', 'tpl_concept.id=tpl_note.concept_id')
					->group('concept_id')
					->order('frequency DESC, title')
					->where('learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
					->queryAll();
			else
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_note')
					->join('tpl_concept', 'tpl_concept.id=tpl_note.concept_id')
					->where('tpl_note.tags LIKE "%'.$tag.'%" AND learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
					->group('concept_id')
					->order('frequency DESC, title')
					->queryAll();
			// end-for-conceptsBar
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

			// for-tagsBar
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
			// end-for-tagsBar
				
			// for-conceptsBar
			if ($tag == '')
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_note')
					->join('tpl_concept', 'tpl_concept.id=tpl_note.concept_id')
					->group('concept_id')
					->where($whereInterval.' AND learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
					->order('frequency DESC, title')
					->queryAll();
			else
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_note')
					->join('tpl_concept', 'tpl_concept.id=tpl_note.concept_id')
					->where($whereInterval.' AND tpl_note.tags LIKE "%'.$tag.'%" AND learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
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
			} else
				$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$key), $key.'('.$value.')');
		}
		if (!$hasTagInTagsBar && $tag != '')
			$tagsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-info tag selected', 'name'=>$tag), $tag.'(0)');
		// end-for-tagsBar
	
		// for-conceptsBar
		foreach ($concepts as $concept) {
			$conceptsBarStr .= $concept_id != $concept['id'] ? ' '.CHtml::tag('span', array('class'=>'label concept', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')') : ' '.CHtml::tag('span', array('class'=>'label label-success concept selected', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')');
			if ($concept_id == $concept['id'])
				$hasConceptInConceptsBar = true;
		}
	
		if (!$hasConceptInConceptsBar && $concept_id != '')
			$conceptsBarStr .= ' '.CHtml::tag('span', array('class'=>'label label-success concept selected', 'name'=>$concept_id), Concept::model()->findByPk($concept_id)->title.'(0)');
		// end-for-conceptsBar
	
		$arr = array();
		$arr['tagsBar'] = $tagsBarStr;
		$arr['conceptsBar'] = $conceptsBarStr;
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function initTagBar() {
		$str = '<b>Tag:</b> ';
		$str .= CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all');
		$tags = Tag::model()->findTags('note', Yii::app()->user->id);
		
		if (array_key_exists('', $tags))
			unset($tags['']);
		
		arsort($tags);
		
		foreach ($tags as $tag)
			$str .= ' '.CHtml::tag('span', array('class'=>'label tag', 'name'=>$tag['name']), $tag['name'].'('.$tag['frequency'].')');
	
		return $str;
	}
	
	public function initConceptBar() {
		$str = '<b>Concept:</b> ';
		$str .= CHtml::tag('span', array('class'=>'label label-success concept selected', 'id'=>'all-concept'), 'all');
		$concepts = Yii::app()->db->createCommand()
			->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
			->from('tpl_note')
			->join('tpl_concept', 'tpl_concept.id=tpl_note.concept_id')
			->group('concept_id')
			->where('learner_id=:learner_id AND concept_id<>root', array(':learner_id'=>Yii::app()->user->id))
			->order('frequency DESC')
			->queryAll();
	
		foreach ($concepts as $concept)
			$str .= ' '.CHtml::tag('span', array('class'=>'label concept', 'name'=>$concept['id']), $concept['title'].'('.$concept['frequency'].')');
	
		return $str;
	}
	
	public function actionCreateTagCanvas() {
		$allStr = '';
		$tagArr =  Tag::model()->findTags('note', Yii::app()->user->id, false);
		foreach ($tagArr as $tag) {
			$allStr .= ' '.CHtml::tag('span', array('class'=>'label label-info pick-tag'), $tag->name);
		}
		
		$arr = array();
		$arr['allTags'] = $allStr;
		
		if (isset($_POST['id'])) {
			$thisTag = $this->loadModel($_POST['id'], 'Note')->tags;
			if ($thisTag != '')
				$thisTag .= ', ';
			$arr['thisTag'] = $thisTag;
		}
		
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
}
<?php

class AskController extends GxController {

	public $layout='//layouts/site_column';
		
	public function actionView($id) {
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'view', 'id='.$id);
		//monitor-end
		
		$model = Ask::model()->findByPk($id);
		if ($model == null) {
			$this->redirect($this->createUrl('index'));
		}
		else
			$this->render('view', array('model' => $model));
	}

	public function actionCreate() {
		$model = new Ask;

		if (isset($_POST['Ask'])) {
			$model->setAttributes($_POST['Ask']);

			if ($model->save()) {
				
				$feed = new Feed;
				$feed->user_id = Yii::app()->user->id;
				$feed->of = 'ask';
				$feed->of_id = $model->id;
				$feed->description = 'asks a question';
				$feed->create_at = date('Y-m-d H:i:s', time());
				$feed->save();
				
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'update', 'id='.$id);
		//monitor-end
		
		$model = $this->loadModel($id, 'Ask');

		if (isset($_POST['Ask'])) {
			$model->setAttributes($_POST['Ask']);

			if ($model->save())
				$this->redirect(array('view', 'id' => $model->id));
		}

		$this->render('update', array('model' => $model));
	}
	
	public function actionUpdateAjax() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest())
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$model = $this->loadModel($_POST['id'], 'Ask');
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'update', 'id='.$_POST['id'], 'POST');
		//monitor-end
		
		if (isset($_POST['title']))
			$model->title = $_POST['title'];
		
		if (isset($_POST['description']))
			$model->description = $_POST['description'];
		
		if (isset($_POST['tags']))
			$model->tags = $_POST['tags'];
	
		$model->save();
		Yii::app()->end();
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Ask')->delete();
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'update', 'id='.$id);
		//monitor-end
		

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}
	
	public function actionIndex() {
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'index');
		//monitor-end
		
		
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
	
		$this->render('index', array(
			'dataProvider' => $model->search($filter_by, $tag, $concept_id),
			'newAsk' => $newAsk,
		));
	}

	public function actionAdmin() {
		$model = new Ask('search');
		$model->unsetAttributes();

		if (isset($_GET['Ask']))
			$model->setAttributes($_GET['Ask']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionGetTags() {
		if (!Yii::app()->getRequest()->getIsAjaxRequest() || !isset($_GET['id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	
		$model = $this->loadModel($_GET['id'], 'Ask');
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
			$tags=Tag::model()->suggestTags($keyword, 'ask');
			if($tags!==array())
				echo implode("\n",$tags);
		}
	}
	
	public function actionUpdateFiltersBar() {
		$tag='';
		if(isset($_POST['tag']))
			$tag=CHtml::decode($_POST['tag']);
	
		$filter_by='';
		if (isset($_POST['filter_by']))
			$filter_by=$_POST['filter_by'];
		
		$concept_id='';
		if (isset($_POST['concept_id']))
			$concept_id=$_POST['concept_id'];
		
		$isAnswered = '';
		if (isset($_POST['is_answered']))
			$isAnswered = $_POST['is_answered'];
		
		//monitor=begin
		if (!Yii::app()->user->isGuest)
			$this->moniter('ask', 'index', 'tag='.$tag.'&filter_by='.$filter_by.'&concept_id='.$concept_id.'&is_answered='.$isAnswered, 'POST');
		//monitor-end
		
		
		
		$tagsBarStr = '<b>Tag:</b> ';
		$tagsBarStr .= $tag == '' ? CHtml::tag('span', array('class'=>'label label-info tag selected', 'id'=>'all-tag'), 'all') : CHtml::tag('span', array('class'=>'label tag', 'id'=>'all-tag'), 'all');
		
		$ConceptsBarStr = '<b>Concept:</b> ';
		$ConceptsBarStr .= $concept_id == '' ? CHtml::tag('span', array('class'=>'label label-success concept selected', 'id'=>'all-concept'), 'all') : CHtml::tag('span', array('class'=>'label concept', 'id'=>'all-concept'), 'all');
		
		$hasTagInTagsBar = false;
		$hasConceptInConceptsBar = false;
		
		// for-answered/unanswered
		$aIdStr = '';
		if ($isAnswered != '') {
			$asks = Yii::app()->db->createCommand()
				->selectDistinct('ask_id')
				->from('tpl_answer')
				->queryAll();
			
			if (count($asks) != 0) {
				$aIdStr = '(';
				foreach ($asks as $ask)
					$aIdStr .= '"'.$ask['ask_id'].'",';
				$aIdStr = substr($aIdStr, 0, -1);
				$aIdStr .= ')';
				
				if ($isAnswered == 'answered')
					$aIdStr = ' AND tpl_ask.id IN '.$aIdStr;
				else if ($isAnswered == 'unanswered')
					$aIdStr = ' AND tpl_ask.id NOT IN '.$aIdStr;
			}
		}
		// end-answered/unanswered
		
		if ($filter_by == '') {
			// for-tagsBar
			if ($concept_id == '') {
				if ($aIdStr == '')
					$tagArr = Yii::app()->db->createCommand()
						->select('tags')
						->from('tpl_ask')
						->queryAll();
				else 
					$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where(substr($aIdStr, 5))
					->queryAll();
			} else
				$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where('concept_id=:concept_id'.$aIdStr, array(':concept_id'=>$concept_id))
					->queryAll();
			// end-for-tagsBar
			
			// for-conceptsBar
			if ($tag == '')
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_ask')
					->where('concept_id<>root'.$aIdStr)
					->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
					->group('concept_id')
					->order('frequency DESC, title')
					->queryAll();
			else
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_ask')
					->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
					->where('tpl_ask.tags LIKE "%'.$tag.'%" AND concept_id<>root'.$aIdStr)
					->group('concept_id')
					->order('frequency DESC, title')
					->queryAll();
			// end-for-conceptsBar
		} elseif ($filter_by == 'myquestions') {
			// for-tagsBar
			if ($concept_id == '')
				$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where('learner_id=:learner_id'.$aIdStr, array(':learner_id'=>Yii::app()->user->id))
					->queryAll();
			else
				$tagArr = Yii::app()->db->createCommand()
					->select('tags')
					->from('tpl_ask')
					->where('concept_id=:concept_id and learner_id=:learner_id'.$aIdStr, array(':concept_id'=>$concept_id,':learner_id'=>Yii::app()->user->id))
					->queryAll();
			// end-for-tagsBar
			
			// for-conceptsBar
			if ($tag == '')
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_ask')
					->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
					->group('concept_id')
					->where('concept_id<>root AND learner_id='.Yii::app()->user->id.$aIdStr)
					->order('frequency DESC, title')
					->queryAll();
			else
				$concepts = Yii::app()->db->createCommand()
					->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
					->from('tpl_ask')
					->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
					->where('concept_id<>root AND tpl_ask.tags LIKE "%'.$tag.'%" and learner_id='.Yii::app()->user->id.$aIdStr)
					->group('concept_id')
					->order('frequency DESC, title')
					->queryAll();
			// end-for-conceptsBar
			
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
		
				// for-tagsBar
				$tagArr = array();
				if ($concept_id == '')
					$tagArr = Yii::app()->db->createCommand()
						->select('tags')
						->from('tpl_ask')
						->where('id IN '.$askIdStr.$aIdStr)
						->queryAll();
				else 
					$tagArr = Yii::app()->db->createCommand()
						->select('tags')
						->from('tpl_ask')
						->where('concept_id=:concept_id and id IN '.$askIdStr.$aIdStr, array(':concept_id'=>$concept_id))
						->queryAll();
				// end-for-tagsBar
			
				// for-conceptsBar
				if ($tag == '')
					$concepts = Yii::app()->db->createCommand()
						->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
						->from('tpl_ask')
						->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
						->group('concept_id')
						->where('tpl_ask.id IN '.$askIdStr.$aIdStr)
						->order('frequency DESC, title')
						->queryAll();
				else
					$concepts = Yii::app()->db->createCommand()
						->select('concept_id as id, tpl_concept.title as title, count(*) as frequency')
						->from('tpl_ask')
						->join('tpl_concept', 'tpl_concept.id=tpl_ask.concept_id')
						->where('tpl_ask.tags LIKE "%'.$tag.'%" and tpl_ask.id IN '.$askIdStr.$aIdStr)
						->group('concept_id')
						->order('frequency DESC, title')
						->queryAll();
				// end-for-conceptsBar
			} else {
				$tagArr = array();
				$concepts = array();
			}
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
	
	public function actionCreateTagCanvas() {
		$allStr = '';
		$tagArr =  Tag::model()->findTags('ask', Yii::app()->user->id, false);
		foreach ($tagArr as $tag) {
			$allStr .= ' '.CHtml::tag('span', array('class'=>'label label-info pick-tag'), $tag->name);
		}
		
		$arr = array();
		$arr['allTags'] = $allStr;
		
		if (isset($_POST['id'])) {
			$thisTag = $this->loadModel($_POST['id'], 'Ask')->tags;
			if ($thisTag != '')
				$thisTag .= ', ';
			$arr['thisTag'] = $thisTag;
		}
		
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function actionAskCount() {
		$sql = 'select count(*) from tpl_ask';
		$count = Yii::app()->db->createCommand($sql)->queryScalar();
		
		echo $count;
		Yii::app()->end();
	}

}
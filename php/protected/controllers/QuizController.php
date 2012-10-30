<?php

class QuizController extends GxController {
	
	public $layout='//layouts/module';

	var $questionSum = 3;// 3 questions for each quiz, if the number questions for concept is enough
	
	public function actionView() {
		if (!Yii::app()->getRequest()->getIsPostRequest() || !isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
		
		$learner_id = Yii::app()->user->id;
		$concept_id = $_POST['concept_id'];
		
		$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$concept_id, 'learner_id'=>$learner_id));
		if ($learnerConcept->status == LearnerConcept::STATUS_COMPLETED)
			$learnt_at = $learnerConcept->learnt_at;
		else
			$learnt_at = null;
		
		$questions = null;
		$connection = Yii::app()->db;
		
		$quiz = Quiz::model()->findByAttributes(array(
				'learner_id'=>$learner_id,
				'concept_id'=>$concept_id,
		));
		
		$quizDoneAt = null;
		
		if ($quiz != null) {
			if ($quiz->done_at != null) // the learner has answered all the questions in the quiz
				$quizDoneAt = $quiz->done_at;
		
			$sql = 'select'
			.' q.id,'
			.' position,'
			.' description,'
			.' correct_answer,'
			.' answer,'
			.' quiz_id'
			.' done_at'
		
			.' from {{question}} as q'
			.' join {{quiz_question}} as qq'
			.' on q.id = qq.question_id'
			.' where'
			.' quiz_id='.$quiz->id
			.' order by position';
		
			$questionArr = $connection->createCommand($sql)->queryAll();
			$count = count($questionArr);
			$questions = array($count);
			for ($i=0;$i<$count;$i++) {
				$optionArr = $connection->createCommand()
				->select('id, question_id, opt, val')
				->from('{{question_option}}')
				->where('question_id = :question_id', array(':question_id'=>$questionArr[$i]['id']))
				->order('opt')
				->queryAll();
				$questions[$i] = $questionArr[$i];
				$questions[$i]['options'] = $optionArr;
			}
		
		} else {
			// generate a new quiz
			$questionArr = $connection->createCommand()
			->select('id, description, correct_answer')
			->from('{{question}}')
			->where('concept_id = :concept_id', array(':concept_id'=>$concept_id))
			->queryAll();
		
			$count = count($questionArr);
		
			if ($count > 1)
				shuffle($questionArr);// random order
		
			$transaction = $connection->beginTransaction();
			try {
				$quiz = new Quiz;
				$quiz->learner_id = $learner_id;
				$quiz->concept_id = $concept_id;
				$quiz->create_at = date('Y-m-d H:i:s', time());
				$quiz->save();
		
				for($i=0;$i<$this->questionSum && $i<$count;) {
					$sql="INSERT INTO {{quiz_question}} (quiz_id, question_id, position) VALUES(".$quiz->id.",".$questionArr[$i]['id'].",".++$i.")";
					$connection->createCommand($sql)->execute();
				}
				
				$transaction->commit();
		
				$questions = array();
				foreach ($questionArr as $key => $value) {
					$questions[$key] = $value;
				}
		
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new CHttpException(400, "{$e->getMessage()}");
			} //.try-catch
		
		} //.if-else
		
		$this->render('view', array(
			'model' => $quiz,
			'learnt_at' => $learnt_at,
			'concept_id'=>$concept_id,
			'questions'=>$questions,
			'quizDoneAt'=>$quizDoneAt,
		));
	}

	public function actionCreate() {
		$model = new Quiz;


		if (isset($_POST['Quiz'])) {
			$model->setAttributes($_POST['Quiz']);
			$relatedData = array(
				'questions' => $_POST['Quiz']['questions'] === '' ? null : $_POST['Quiz']['questions'],
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
		$model = $this->loadModel($id, 'Quiz');


		if (isset($_POST['Quiz'])) {
			$model->setAttributes($_POST['Quiz']);
			$relatedData = array(
				'questions' => $_POST['Quiz']['questions'] === '' ? null : $_POST['Quiz']['questions'],
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
			$this->loadModel($id, 'Quiz')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Quiz');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Quiz('search');
		$model->unsetAttributes();

		if (isset($_GET['Quiz']))
			$model->setAttributes($_GET['Quiz']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	public function actionLoadQuiz() {
	
		if (!Yii::app()->request->isAjaxRequest || !isset($_POST['concept_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			
		$questions = null;
		$connection = Yii::app()->db;
	
		$learner_id = Yii::app()->user->id;
		$concept_id = $_POST['concept_id'];
	
		$quiz = Quiz::model()->findByAttributes(array(
				'learner_id'=>$learner_id,
				'concept_id'=>$concept_id,
		));
	
		$quizDoneAt = null;
	
		if ($quiz != null) {
			if ($quiz->done_at != null) // the learner has answered all the questions in the quiz
				$quizDoneAt = $quiz->done_at;
				
			$sql = 'select'
			.' q.id,'
			.' position,'
			.' description,'
			.' correct_answer,'
			.' answer,'
			.' quiz_id'
			.' done_at'
				
			.' from {{question}} as q'
			.' join {{quiz_question}} as qq'
			.' on q.id = qq.question_id'
			.' where'
			.' quiz_id='.$quiz->id
			.' order by position';
	
			$questionArr = $connection->createCommand($sql)->queryAll();
			$count = count($questionArr);
			$questions = array($count);
			for ($i=0;$i<$count;$i++) {
				$optionArr = $connection->createCommand()
				->select('id, question_id, opt, val')
				->from('{{question_option}}')
				->where('question_id = :question_id', array(':question_id'=>$questionArr[$i]['id']))
				->order('opt')
				->queryAll();
				$questions[$i] = $questionArr[$i];
				$questions[$i]['options'] = $optionArr;
			}
				
		} else {
			// generate a new quiz
			$questionArr = $connection->createCommand()
			->select('id, description, correct_answer')
			->from('{{question}}')
			->where('concept_id = :concept_id', array(':concept_id'=>$concept_id))
			->queryAll();
	
			$count = count($questionArr);
	
			if ($count > 1)
				shuffle($questionArr);// random order
			 
			$transaction = $connection->beginTransaction();
			try {
				$quiz = new Quiz;
				$quiz->learner_id = $learner_id;
				$quiz->concept_id = $concept_id;
				$quiz->create_at = date('Y-m-d H:i:s', time());
				$quiz->save();
	
				for($i=0;$i<$this->questionSum && $i<$count;) {
					$sql="INSERT INTO {{quiz_question}} (quiz_id, question_id, position) VALUES(".$quiz->id.",".$questionArr[$i]['id'].",".++$i.")";
					$connection->createCommand($sql)->execute();
				}
				/*
					$sql="INSERT INTO {{quiz_question}} (quiz_id, question_id, position) VALUES(:quiz_id,:question_id,:position)";
				$command=$connection->createCommand($sql);
				for($i=0;$i<3;$i++) {
				$command->bindParam(":quiz_id",$quiz_id,PDO::PARAM_INT);
				$command->bindParam(":question_id",$questionArr[$i]['question_id'],PDO::PARAM_INT);
				$command->bindParam(":position",$i,PDO::PARAM_INT);
				$command->execute();
				}
				*/
				$transaction->commit();
	
				$questions = array();
				foreach ($questionArr as $key => $value) {
					$questions[$key] = $value;
				}
	
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new CHttpException(400, "{$e->getMessage()}");
			} //.try-catch
			 
		} //.if-else
	
		$this->renderPartial('_view', array(
				'concept_id'=>$concept_id,
				'questions'=>$questions,
				'quizDoneAt'=>$quizDoneAt,
				'quiz_id'=>$quiz->id,
				'error_msg'=>'',
		), false, true);
	
	}
	
	public function actionQuizSubmit() {
	
		$time_now = date('Y-m-d H:i:s', time());
		$quiz_id = $_POST['quiz_id'];
		$correctAnswer = 0;
		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			$qqArr = QuizQuestion::model()->findAllByAttributes(
					array(),
					$condition = 'quiz_id = :quiz_id',
					$params = array(':quiz_id' => $quiz_id)
			);
	
			foreach ($qqArr as $qq) {
				$qq->answer = $_POST['q'.$qq->question_id];
				$qq->save();
	
				if (Question::model()->findByPk($qq->question_id)->correct_answer == $qq->answer)
					$correctAnswer++;
			}
	
			$questionSum = count($qqArr) < $this->questionSum ? count($qqArr) : $this->questionSum;
	
			$quiz = Quiz::model()->findByPk($quiz_id);
			$quiz->score = $correctAnswer.'/'.$questionSum;
			$quiz->done_at = $time_now;
			$quiz->lastaccess_at = $time_now;
			$quiz->save();

			// -> has learnt this concept
			$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$quiz->concept_id, 'learner_id'=>Yii::app()->user->id));
			$learnerConcept->learnt_at = $time_now;
			$learnerConcept->status = LearnerConcept::STATUS_COMPLETED;
			$learnerConcept->save();

			$transaction->commit();
	
		} catch (Exception $e) {
			$transaction->rollBack();
			throw new CHttpException(400, "{$e->getMessage()}");
		} //.try-catch
	
		$sql = 'select'
		.' q.id,'
		.' concept_id,'
		.' position,'
		.' description,'
		.' correct_answer,'
		.' answer,'
		.' quiz_id'
		.' done_at'
	
		.' from {{question}} as q'
		.' join {{quiz_question}} as qq'
		.' on q.id = qq.question_id'
		.' where'
		.' quiz_id='.$quiz_id
		.' order by position';
			
		$questionArr = $connection->createCommand($sql)->queryAll();
		$count = count($questionArr);
		$questions = array($count);
		for ($i=0;$i<$count;$i++) {
			$optionArr = $connection->createCommand()
			->select('id, question_id, opt, val')
			->from('{{question_option}}')
			->where('question_id = :question_id', array(':question_id'=>$questionArr[$i]['id']))
			->order('opt')
			->queryAll();
			$questions[$i] = $questionArr[$i];
			$questions[$i]['options'] = $optionArr;
		}
	
		$this->render('view', array(
				'model' => $quiz,
				'concept_id'=>$questions[0]['concept_id'],
				'questions'=>$questions,
				'quizDoneAt'=>$time_now,
				'learnt_at'=>$time_now,
		), false, true);

	
	}
/*	
	public function actionQuizSubmit() {
	
		if (!Yii::app()->request->isAjaxRequest || !isset($_POST['quiz_id']))
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	
		$time_now = date('Y-m-d H:i:s', time());
		$quiz_id = $_POST['quiz_id'];
		$correctAnswer = 0;
		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			$qqArr = QuizQuestion::model()->findAllByAttributes(
					array(),
					$condition = 'quiz_id = :quiz_id',
					$params = array(':quiz_id' => $quiz_id)
			);
				
			foreach ($qqArr as $qq) {
				
				//validate form failed
				if (!isset($_POST['q'.$qq->question_id])) {
					$sql = 'select'
					.' q.id,'
					.' position,'
					.' description,'
					.' correct_answer,'
					.' answer,'
					.' quiz_id'
					.' done_at'
					
					.' from {{question}} as q'
					.' join {{quiz_question}} as qq'
					.' on q.id = qq.question_id'
					.' where'
					.' quiz_id='.$quiz_id
					.' order by position';
					
					$questionArr = $connection->createCommand($sql)->queryAll();
					$count = count($questionArr);
					$questions = array($count);
					for ($i=0;$i<$count;$i++) {
						$optionArr = $connection->createCommand()
						->select('id, question_id, opt, val')
						->from('{{question_option}}')
						->where('question_id = :question_id', array(':question_id'=>$questionArr[$i]['id']))
						->order('opt')
						->queryAll();
						$questions[$i] = $questionArr[$i];
						$questions[$i]['options'] = $optionArr;
					}
					
					$this->renderPartial('_view', array(
							'concept_id'=>5,
							'questions'=>$questions,
							'quizDoneAt'=>null,
							'quiz_id'=>$quiz_id,
							'error_msg'=>'Please choose an option for each question!',
					), false, true);
					
					Yii::app()->end();
					
				}
				
				$qq->answer = $_POST['q'.$qq->question_id];
				$qq->save();
	
				if (Question::model()->findByPk($qq->question_id)->correct_answer == $qq->answer)
					$correctAnswer++;
			}
				
			$questionSum = count($qqArr) < $this->questionSum ? count($qqArr) : $this->questionSum;
				
			$quiz = Quiz::model()->findByPk($quiz_id);
			$quiz->score = $correctAnswer.'/'.$questionSum;
			$quiz->done_at = $time_now;
			$quiz->lastaccess_at = $time_now;
			$quiz->save();
			
			// -> has learnt this concept
			$learnerConcept = LearnerConcept::model()->findByPk(array('concept_id'=>$quiz->concept_id, 'learner_id'=>Yii::app()->user->id));
			$learnerConcept->learnt_at = date('Y-m-d H:i:s', time());
			$learnerConcept->status = LearnerConcept::STATUS_COMPLETED;
			$learnerConcept->save();
			
	
			$transaction->commit();
				
		} catch (Exception $e) {
			$transaction->rollBack();
			throw new CHttpException(400, "{$e->getMessage()}");
		} //.try-catch
	
		$sql = 'select'
		.' q.id,'
		.' concept_id,'
		.' position,'
		.' description,'
		.' correct_answer,'
		.' answer,'
		.' quiz_id'
		.' done_at'
	
		.' from {{question}} as q'
		.' join {{quiz_question}} as qq'
		.' on q.id = qq.question_id'
		.' where'
		.' quiz_id='.$quiz_id
		.' order by position';
			
		$questionArr = $connection->createCommand($sql)->queryAll();
		$count = count($questionArr);
		$questions = array($count);
		for ($i=0;$i<$count;$i++) {
			$optionArr = $connection->createCommand()
			->select('id, question_id, opt, val')
			->from('{{question_option}}')
			->where('question_id = :question_id', array(':question_id'=>$questionArr[$i]['id']))
			->order('opt')
			->queryAll();
			$questions[$i] = $questionArr[$i];
			$questions[$i]['options'] = $optionArr;
		}
	
		$this->renderPartial('_view', array(
				'concept_id'=>$questions[0]['concept_id'],
				'questions'=>$questions,
				'quizDoneAt'=>$time_now,
				'quiz_id'=>$quiz_id,
				'error_msg'=>'',
		), false, true);
	
	}
*/
}
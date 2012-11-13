<?php

class StatsController extends GxController {
	public function actionStats() {
		$arr = array();
		$arr['countFavorited'] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM tpl_favorite WHERE user_id='.Yii::app()->user->id)->queryScalar();
		$arr['countShared'] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM tpl_feed WHERE (of IS NOT NULL OR from_id IS NOT NULL) AND user_id='.Yii::app()->user->id)->queryScalar();
		$arr['countCommented'] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM tpl_feed_comment WHERE user_id='.Yii::app()->user->id)->queryScalar();
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function actionStatsQa() {
		$arr = array();
		$arr['countMyQuestions'] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM tpl_ask WHERE learner_id='.Yii::app()->user->id)->queryScalar();
		$arr['countMyAnswers'] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM tpl_answer WHERE learner_id='.Yii::app()->user->id)->queryScalar();
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
	public function actionMyModules() {
		$sql = 'SELECT c.id, title FROM tpl_concept AS c, tpl_learner_concept AS lc WHERE c.id=root AND c.id=lc.concept_id AND lc.learner_id='.Yii::app()->user->id.' ORDER BY lc.lastaction_at';
		$myModuleArr = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (count($myModuleArr) == 0) {
			$rtn = '<li><a href="'.Yii::app()->homeUrl.'/concept">Register a module</a></li>';
		} else {
			$rtn = '';
			foreach ($myModuleArr as $myModule)
				$rtn .= '<li><a href="'.Yii::app()->homeUrl.'/concept/'.$myModule['id'].'"><i class="icon-fire"></i> '.$myModule['title'].'</a></li>';
		}
		
		echo $rtn;
		Yii::app()->end();
	}
	
	public function actionStatsConcept() {
		$arr = array();
		$arr['countMyModules'] = Yii::app()->db->createCommand('SELECT COUNT(concept_id) FROM tpl_learner_concept WHERE concept_id IN (SELECT id FROM tpl_concept WHERE id=root) AND learner_id='.Yii::app()->user->id)->queryScalar();
		$arr['countConceptsLearning'] = Yii::app()->db->createCommand('SELECT COUNT(concept_id) FROM tpl_learner_concept WHERE concept_id NOT IN (SELECT id FROM tpl_concept WHERE id=root) AND status='.LearnerConcept::STATUS_INPROGRESS.' AND learner_id='.Yii::app()->user->id.' GROUP BY learner_id')->queryScalar();
		$arr['countConceptsLearnt'] = Yii::app()->db->createCommand('SELECT COUNT(concept_id) FROM tpl_learner_concept WHERE concept_id NOT IN (SELECT id FROM tpl_concept WHERE id=root) AND status='.LearnerConcept::STATUS_COMPLETED.' AND learner_id='.Yii::app()->user->id.' GROUP BY learner_id')->queryScalar();
		header('Content-type: application/json');
		echo json_encode($arr);
		Yii::app()->end();
	}
	
}
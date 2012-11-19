<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public function actionError() {
		echo 'Server is busy...';
		Yii::app()->end();
	}
	
	public function moniter($controllor, $action, $request='', $type='GET') {
		$monitor = new Monitor;
		$monitor->user_id = Yii::app()->user->id;
		$monitor->controllor = $controllor;
		$monitor->action = $action;
		$monitor->create_at = date('Y-m-d H:i:s', time());
		$monitor->request = $request;
		$monitor->type = $type;
		
		$monitor->save();
	}
	
	
	/*!
	 * @brief The function
	 * @param $action does something
	 * @return
	 * 
	 */
/*	protected function beforeAction($action) {
		if (!Yii::app()->user->isGuest
//				&& $action->id != 'updateFiltersBar'
				&& $action->id != 'initTagBarsAjax'
				&& $action->id != 'askCount'
				&& $action->id != 'updateView'
				&& $action->id != 'validateUP'
				&& $action->id != 'validateEmail'
				&& $action->id != 'validateUsername'
				&& $action->id != 'feedCount'
				&& $action->id != 'sharePrepare'
				&& $action->id != 'statsConcept'
				&& $action->id != 'statsQa'
				&& $action->id != 'stats'
				&& $action->id != 'myModules'
				&& $action->id != 'messageCount'
				&& $action->id != 'createTagCanvas'
				&& $action->id != 'updateView'
				&& $action->id != 'getTags'
				&& $action->id != 'fetchUsers'
				&& $action->id != 'fetchTopUsers'
				&& $action->id != 'fetchUsersLearning'
				&& $action->id != 'fetchUsersLearnt'
				&& $action->id != 'fetchConceptsByLearner'
				&& $action->id != 'fetchUsersRankByModule'
				&& $action->id != 'fetchModule'
				&& $action->id != 'suggestTags'
			) {
			
			$request = '';
			
			if (!$action->id == 'create' && isset($_REQUEST)) {
				foreach ($_REQUEST as $key=>$value) {
					if ($key == 'ajax')
						return true;
					$request .= $key.'='.$value.'&';
				}
			}
			
			$monitor = new Monitor;
			$monitor->user_id = Yii::app()->user->id;
			$monitor->controllor = $this->id;
			$monitor->action = $action->id;
			$monitor->create_at = date('Y-m-d H:i:s', time());
			
			$monitor->request_value = $request;

			$monitor->save();
		}
	
		return true;
	}
*/
}
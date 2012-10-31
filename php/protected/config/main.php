<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Topolor',

	// preloading 'log' component
	'preload'=>array('log'),
	'defaultController' => 'feed',
		
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

		'ext.giix-components.*', // giix components
	
		'application.modules.user.models.*', // yii-user
        'application.modules.user.components.*', // yii-user
	),

	'modules'=>array(
	
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'gii',
			'generatorPaths' => array(
				'ext.giix-core', // giix generators
			),
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
		// yii-user
		'user'=>array(
            # encrypting method (php hash function)
            'hash' => 'md5',

            # send activation email
            'sendActivationMail' => true,

            # allow access for non-activated users
            'loginNotActiv' => false,

            # activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => false,

            # automatically login from registration
            'autoLogin' => true,

            # registration path
            'registrationUrl' => array('/user/registration'),

            # recovery password path
            'recoveryUrl' => array('/user/recovery'),

            # login form path
            'loginUrl' => array('/user/login'),

            # page after login
            'returnUrl' => array('/user/profile'),

            # page after logout
            'returnLogoutUrl' => array('/user/login'),
        ),
			
		'opauth' => array(
			'opauthParams' => array(
				'Security.salt' => 'LDFmiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m',
				'Strategy' => array(
					'Facebook' => array(
						'app_id' => '287934914658104',
						'app_secret' => 'e57518c2fb355808601962b97d6a2513',
					),
					'LinkedIn' => array(
						'api_key' => 'v3erksao4f80',
						'secret_key' => 'CJesGscPgLDtgB4h'
					),
					'Twitter' => array(
						'key' => '1QhyT1JkncJhmfxVtbYGA',
						'secret' => 'sjHEWQ7Ajpm7rKLaAfS4pRLVC91XynyH9G6bVVTsc'
					)
				),
			),
		),
        
	),

	// application components
	'components'=>array(
/*		'clientScript'=>array(
			'packages'=>array(
				'jquery'=>array(
					'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jquery/1.7/',
					'js'=>array('jquery.min.js'),
				)
			),
		),
*/					
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
	
			'class' => 'WebUser', // yii-user
            'loginUrl' => array('/user/login'), // yii-user
		),
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		

		'db'=>array(
//			'connectionString' => 'mysql:host=127.0.0.1;dbname=topolor',
			'connectionString' => 'mysql:host=127.2.144.129;dbname=topolor',
			'emulatePrepare' => true,
//			'username' => 'root',
//			'password' => '123456',
			'username' => 'admin',
			'password' => 'wIjt3DRXvHR4',
			'charset' => 'utf8',
			'tablePrefix' => 'tpl_',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
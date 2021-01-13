<?php

namespace App;

class Bootstrap {

	/**
	 * @return \MvcCore\Application
	 */
	public static function Init () {

		$app = \MvcCore\Application::GetInstance();


		// Patch core to use extended debug class:
		if (class_exists('MvcCore\Ext\Debugs\Tracy')) {
			\MvcCore\Ext\Debugs\Tracy::$Editor = 'MSVS2019';
			$app->SetDebugClass('MvcCore\Ext\Debugs\Tracy');
		}
		\MvcCore\Ext\Debugs\Tracy::Init();


		// Comment this line for PHP 8+ and Attributes anotation:
		\MvcCore\Ext\Models\Db\Misc\Reflection::SetAttributesAnotations(FALSE);

		
		$sysCfg = \MvcCore\Config::GetSystem();
		$cache = \MvcCore\Ext\Caches\Redis::GetInstance([ // `default` connection to:
			\MvcCore\Ext\ICache::CONNECTION_NAME		=> $sysCfg->cache->storeName,
			\MvcCore\Ext\ICache::CONNECTION_DATABASE	=> $sysCfg->cache->databaseName,
		]);
		\MvcCore\Ext\Cache::RegisterStore($sysCfg->cache->storeName, $cache, TRUE);
		if ($sysCfg->cache->enabled) 
			$cache->Connect();

		
		/**
		 * Uncomment this line before generate any assets into temporary directory, before application
		 * packing/building, only if you want to pack application without JS/CSS/fonts/images inside
		 * result PHP package and you want to have all those files placed on hard drive.
		 * You can use this variant in modes `PHP_PRESERVE_PACKAGE`, `PHP_PRESERVE_HDD` and `PHP_STRICT_HDD`.
		 */
		//\MvcCore\Ext\Views\Helpers\Assets::SetAssetUrlCompletion(FALSE);


		// Initialize authentication service extension and set custom user class
		\MvcCore\Ext\Auths\Basic::GetInstance()

			// Set unique password hash:
			->SetPasswordHashSalt('s9E56/QH6.a69sJML9aS6s')

			// To use credentials from system config file:
			//->SetUserClass('MvcCore\Ext\Auths\Basics\Users\SystemConfig')

			// To use credentials from database:
			->SetUserClass('\App\Models\User')

			// To use custom authentication submitting controller:
			->SetControllerClass('//App\Controllers\Auth')

			->SetExpirationAuthorization(3600)
			->SetExpirationIdentity(86400 * 30)

			// To describe basic credentials database structure
			/*->SetTableStructureForDbUsers('users', [
				'id'			=> 'id',
				'userName'		=> 'user_name',
				'passwordHash'	=> 'password_hash',
				'fullName'		=> 'full_name',
			])*/;

		// Display db password hash value by unique password hash for desired user name:
		//die(\MvcCore\Ext\Auths\Basics\User::EncodePasswordToHash('demo'));


		// Set up application routes (without custom names),
		// defined basically as `Controller::Action` combinations:
		\MvcCore\Router::GetInstance([
			'front_home'			=> [
				'match'				=> '#^/(index\.php)?$#',
				'reverse'			=> '/',
				'controllerAction'	=> 'Front\Index:Index',
				'defaults'			=> ['order' => 'desc'],
				'constraints'		=> ['order' => 'a-z'],
			],
			'front_post'			=> [
				'pattern'			=> '/post/<path>',
				'controllerAction'	=> 'Front\Post:Index',
				'constraints'		=> ['path' => '[-_a-zA-Z0-9]+'],
			],

			'front_register'			=> [
				'pattern'			=> '/register',
				'controllerAction'	=> 'Front\Auth:Register',
			],
			'front_login'			=> [
				'pattern'			=> '/login',
				'controllerAction'	=> 'Front\Auth:SignIn',
			],

			'admin_home'			=> [
				'pattern'			=> '/admin',
				'controllerAction'	=> 'Admin\Posts:Index',
			],
			'admin_post_create'			=> [
				'pattern'			=> '/admin/post/create',
				'controllerAction'	=> 'Admin\Posts:Create',
			],
			'admin_post_submit'			=> [
				'pattern'			=> '/admin/post/save',
				'controllerAction'	=> 'Admin\Posts:Submit',
				'method'			=> 'POST'
			],
			'admin_post_edit'			=> [
				//'pattern'			=> '/admin/post/edit[/<id>]',
				'match'				=> '#^/admin/post/edit(/(?<id>\d+))?/?$#',
				'reverse'			=> '/admin/post/edit[/<id>]',
				'controllerAction'	=> 'Admin\Posts:Edit',
				'defaults'			=> ['id' => 1,],
				'constraints'		=> ['id' => '\d+'],
			],
			
			'admin_comments'		=> [
				'pattern'			=> '/admin/comments',
				'controllerAction'	=> 'Admin\Comments:Index',
			],
			'admin_comment_detail'	=> [
				'pattern'			=> '/admin/comment/<id>',
				'controllerAction'	=> 'Admin\Comments:Detail',
				'constraints'		=> ['id' => '\d+'],
			],

			'api_posts'				=> [
				'pattern'			=> '/api/posts',
				'controllerAction'	=> 'Api\Soap:Posts',
			]
		]);
		
		return $app;
	}
}

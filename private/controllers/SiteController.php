<?php


class SiteController {

	private static $titles = array(
		'index' => 'Naslovna', 'login' => 'Prijava', 'logout' => 'Odjava',
		'profile' => 'Moj profil', 'registration' => 'Registracija',
		'constructors' => 'Građevinari', 'requests' => 'Moji zahtjevi',
		'notifications' => 'Obavijesti', 'counties' => 'Županije',
		'log' => 'Dnevnik', 'data' => 'Podaci', 'statistics' => 'Statistika',
		'activation' => 'Aktivacija', 'about' => 'O autoru', 'documentation' => 'Dokumentacija'
	);



	public static function handleRequest() {
		if (!isset($_GET['action']) or $_GET['action'] == 'index')
			self::actionIndex();
		else{
			$action = 'action' . ucfirst($_GET['action']);
			if(method_exists(__CLASS__, $action))
				call_user_func(array('self', $action));
			else
				self::actionError404();
		}
	}


	public static function getTitle() {
		if (!isset($_GET['action']))
			return "Legalizacija | Naslovna";
		else if(array_key_exists($_GET['action'], self::$titles))
			return "Legalizacija | " . self::$titles[$_GET['action']];
		else
			return "Legalizacija | 404";
	}


	public static function getLeftMenu(){
		if(!User::isLoggedIn() || User::isRegularUser()){
			$type = User::isLoggedIn() ? 2 : 1;
			echo "<li><a href=\"?action=constructors&type={$type}\"> Građevinari</a></li>";
		}
		else if(User::isModerator())
			echo "<li><a href=\"?action=notifications\"> Obavijesti </a></li>";
		else if(User::isAdmin()){
			echo "<li><a href=\"?action=counties\"> Županije </a></li>";
			echo "<li><a href=\"?action=statistics\"> Statistika </a></li>";
			echo "<li><a href=\"?action=data\"> Podaci </a></li>";
			echo "<li><a href=\"?action=log\"> Dnevnik </a></li>";
		}
		if(User::isLoggedIn() and !User::isAdmin())
			echo "<li><a href=\"?action=requests\"> Moji zahtjevi </a></li>";
	}



	public static function getJSFile() {
		if (isset($_GET['action'])) {
			return $_GET['action'] . '.js';
		}
		else
			return "";
	}



	private static function actionIndex() {
		$view = new Index();
		$view->getOutput();
	}


	private static function actionError404() {
		$view = new Site404();
		$view->getOutput();
	}



	private static function actionCounties() {
		if (!User::isAdmin())
			Utils::redirect('index');
		$countiesModel = new CountiesModel(DatabaseHandler::getInstance());
		$usersModel = new UsersModel(DatabaseHandler::getInstance());
		if (!isset($_GET['id'])) {
			$controller = new CountiesController($countiesModel);
			$view = new Counties($countiesModel, $usersModel, $controller);
		}
		else{
			$controller = new CountiesController($usersModel);
			$view = new Moderators($usersModel, $controller);
		}
		$view->getOutput();
	}


	private static function actionConstructors() {
		if(User::isAdmin() || !isset($_GET['type']))
			Utils::redirect('index');
		else{
			$view = null;
			if(isset($_GET['id'])){
				$commentsModel = new CommentsModel(DatabaseHandler::getInstance());
				$gradesModel = new GradesModel(DatabaseHandler::getInstance());
				$requestsModel = new RequestsModel(DatabaseHandler::getInstance());
				$usersModel = new UsersModel(DatabaseHandler::getInstance());
				$view = new ConstructorProfile($commentsModel, $gradesModel, $requestsModel, $usersModel);
			}
			else{
				$model = new CountiesModel(DatabaseHandler::getInstance());
				$view = new Constructors($model);
			}
			$view->getOutput();
		}
	}


	private static function actionData(){
		if(!User::isAdmin())
			Utils::redirect('index');
		else{
			$view = new Data();
			$view->getOutput();
		}
	}



	private static function actionRequests(){
		if(!User::isLoggedIn())
			Utils::redirect('index');
		else{
			$view = null;

			$resourcesModel = new ResourcesModel(DatabaseHandler::getInstance());
			$requestsModel = new RequestsModel(DatabaseHandler::getInstance());
			$terrainsModel = new TerrainsModel(DatabaseHandler::getInstance());

			$controller = new RequestController($resourcesModel, $requestsModel, $terrainsModel);

			if(!isset($_GET['type']))
				$view = new Requests($requestsModel, $terrainsModel, $controller);
			else{
				if($_GET['type'] == 'new')
					$view = new CreateRequest($requestsModel, $terrainsModel, $controller);
				else if(isset($_GET['id']))
					$view = new EditViewRequest($resourcesModel, $requestsModel, $terrainsModel, $controller);
				else
					$view = new Site404();
			}

			$view->getOutput();
		}
	}


	private static function actionNotifications(){
		if(!User::isModerator())
			Utils::redirect('index');

		$requestsModel = new RequestsModel(DatabaseHandler::getInstance());
		$usersModel = new UsersModel(DatabaseHandler::getInstance());

		$view = new Notifications($requestsModel, $usersModel);
		$view->getOutput();
	}


	private static function actionLogin() {
		if(User::isLoggedIn())
			Utils::redirect('index');
		else{
			$model = new UsersModel(DatabaseHandler::getInstance());
			$controller = new UserController($model);
			$view = new Login($model, $controller);
			$view->getOutput();
		}
	}


	private static function actionLogout(){
		if (User::isLoggedIn())
			User::logout();
		Utils::redirect('index');
	}


	private static function actionProfile() {
		if (!User::isLoggedIn())
			Utils::redirect('index');
		else{
			$model = new UsersModel(DatabaseHandler::getInstance());
			$controller = new UserController($model);
			$view = new Profile($model, $controller);
			$view->getOutput();
		}
	}



	private static function actionRegistration() {
		if (User::isLoggedIn())
			Utils::redirect('index');
		else{
			$model = new UsersModel(DatabaseHandler::getInstance());
			$controller = new UserController($model);
			$view = new Registration($model, $controller);
			$view->getOutput();
		}
	}



	private  static function actionActivation(){
		$model = new ActivationCodesModel(DatabaseHandler::getInstance());
		$controller = new ActivationController($model);
		$view = new Activation($model, $controller);
		$view->getOutput();
	}


	private static function actionAbout(){
		$view = new About();
		$view->getOutput();
	}


	private static function actionDocumentation(){
		$view = new Documentation();
		$view->getOutput();
	}

}
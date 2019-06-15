<?php


class ActivationController {

	private $model = null;


	function __construct(ActivationCodesModel $model){
		$this->model = $model;
	}


	public function actionCheck(){
		$exists = $this->model->select()->where(array('code' => $_GET['code']))->exec();
		if($exists == null){
			echo "Kod nije pronađen.";
			return;
		}

		$timestamp = $this->model->executeRawQuery('SELECT CURRENT_TIMESTAMP t;');
		$timestamp = $timestamp[0]['t'];

		$valid = $this->model->select()->where(array(
				'activated' => 't',
				'valid_until' => 'comparison:<=;logic:OR;value:' . $timestamp,
				'code' => $_GET['code']
		))->exec();

		if($valid != null) {
			echo "Kod je nevažeći ili je već aktiviran.";
			return;
		}

		$usersModel = new UsersModel(DatabaseHandler::getInstance());
		$userId = $exists[0]['user_id'];

		$this->model->update(array('activated' => 't'), array('code' => $_GET['code']));
		$usersModel->update(array('active' => 't'), array('id' => $userId));

		echo "Aktivacija uspješna!";
	}

}
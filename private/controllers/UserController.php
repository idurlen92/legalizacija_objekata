<?php

class UserController {

    /** @var UsersModel */
    private $model = null;

    public  function __construct(UsersModel $model){
        $this->model = $model;
    }


    public function actionLogin($data){
	    $user = $this->model->select()->where(array(
			    "username" => $data['form_login_username'], "active" => 't'
		    ))->exec();

	    if($user == null)
		    Utils::redirect("login&noUser=true");
	    else if($user[0]['locked'] == 't')
		    Utils::redirect("login&locked=true");
	    else{
		    $user = $this->model->select(array('u.*', 'ut.type_name user_type'))->where(array(
			    "u.username" => $data['form_login_username'],
			    "u.password" => $data['form_login_password'],
			    "u.active" => 't'
		    ))->join('user_types ut', 'u')->exec();

		    if($user == null){
			    $attempt = isset($_GET['attempt']) ? ($_GET['attempt'] + 1) : 1;
			    if($attempt == 3)
				    $this->model->update(array('locked' => 't'), array('username' => $data['form_login_username']));
			    Utils::redirect("login&attempt=" . $attempt);
		    }
		    else{
			    User::login($user[0]['username'], $user[0]['id'], $user[0]['user_type'], isset($data['form_login_remember']));
			    Utils::redirect('index');
		    }
	    }// else[1]
    }



    public function actionRegistration($data){
        $success = $this->model->insert(array(
                'name' => $data['form_reg_name'],
                'surname' => $data['form_reg_surname'],
                'gender' => $data['form_reg_gender'],
                'birth_date' => $data['form_reg_date'],
                'username' => $data['form_reg_username'],
                'password' => $data['form_reg_password'],
                'email' => $data['form_reg_email'],
                'city' => $data['form_reg_city'],
                'address' => $data['form_reg_address'],
                'county_id' => $data['form_reg_county']
        ));

        if(!$success){
	        echo "<div class=\"operation_fail\">Greška u unesenim podacima</div>";
        }
        else{
            $activationCodesModel = new ActivationCodesModel($this->model->getDatabase());

	        $userId = $this->model->getInsertId();
	        $code = Utils::generateCode();
	        $validUntil = $activationCodesModel->executeRawQuery("SELECT CURRENT_TIMESTAMP + INTERVAL 12 HOUR until", array());

			$res = $activationCodesModel->insert(array(
				'user_id' => $userId,
				'code' => $code,
				'valid_until' => $validUntil[0]['until']
			));

	        if($res !== null){
		        $message = "Postovani,\nza aktivaciju korisnickog racuna molimo otvorite link:\n";
		        $message .= "http://arka.foi.hr/WebDiP/2014_projekti/WebDiP2014x015/index.php?";
		        $message .= "action=activation&code=" . $code;
				Utils::sendMail('aktivacija racuna', $message, $data['form_reg_email']);
	        }

	        $address = "registration&operation=";
	        $address .= ($res !== null) ? 'finished' : 'error';
	        Utils::redirect($address);
        }
    }


}
<?php

class Notifications implements View{

	private $requestsModel = null;
	private $usersModel = null;
	private $controller = null;

	public function __construct(RequestsModel $requestsModel, UsersModel $usersModel){
		$this->requestsModel = $requestsModel;
		$this->usersModel = $usersModel;
	}


	public function getOutput(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$title = $_POST['inputTitle'];
			$message = $_POST['inputMessage'];
			$recipients = implode(',', $_POST['selectedUsers']);

			if(!Utils::sendMail($recipients, $title, $message))
				echo "<div class=\"operation_fail\">Greška u slanju mail-a</div>";
			else
				echo "<div class=\"operation_success\">Uspješno slanje!</div>";
			/*$success = true;
			foreach($_POST['selectedUsers'] as $address){
				echo $address;
				//$success == $success and !Utils::sendMail($address, $title, $message);
			}
			if(!$success)*/
		}

		$form = " <form id=\"form_notifications\" method=\"POST\" onsubmit=\"return isFormValid();\">
 					<table id=\"tableMessaing\">
 						<tbody>
 							<tr>
 								<td><label for=\"inputTitle\">Naslov</label></td>
								<td><input type=\"text\" id=\"inputTitle\" name=\"inputTitle\"></td>
							</tr>
							<tr>
								<td><label for=\"inputMessage\">Tekst</label></td>
								<td><textarea id=\"inputMessage\" name=\"inputMessage\"></textarea></td>
							</tr>
							<tr>
								<td colspan=\"2\";\">
									<div class=\"scrollable\" id=\"checkUsers\">";


		$users = $this->usersModel->select(array('DISTINCT u.name', 'u.surname', 'u.email'))
			->join('requests r', 'u')->where(array(
				'r.constructor_id' => User::getUserId(),
				'r.request_status_id' => 'comparison:IN;value:1,2'
			))->exec();

		$i=1;
		foreach($users as $user){
			$form .= "<input type=\"checkbox\" name=\"selectedUsers[]\" id=\"selectedUsers" . $i ."\""
				. " value=\"" . $user['email'] . "\">";
			$form .= "<label for=\"selectedUsers" . $i++ . "\">" . $user['surname'] . ": " . $user['name'] . "</label><br/>";
		}

		$form .=	"</div></td></tr></tbody></table>
			        <input type=\"submit\" method=\"POST\" value=\"Pošalji\">
			    </form>";

		echo $form;
	}

}
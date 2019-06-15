<?php

class ConstructorProfile implements View{

	private $commentsModel = null;
	private $gradesModel = null;
	private $requestsModel = null;
	private $usersModel = null;


	public function __construct(CommentsModel $commentsModel, GradesModel $gradesModel, RequestsModel $requestsModel,UsersModel $usersModel){
		$this->commentsModel = $commentsModel;
		$this->gradesModel = $gradesModel;
		$this->requestsModel = $requestsModel;
		$this->usersModel = $usersModel;
	}



	private function createBasicInfoSection($constructor){
		$result = $this->requestsModel->select('COUNT(*) n')->where(array(
			'constructor_id' => $constructor['id']))->exec();
		$requestNumber = $result[0]['n'];

		//TODO: table class!!!
		$infoSection = "<div><h2>" . $constructor['name'] . " " . $constructor['surname'] . "</h2>";
		$infoSection .= "<table><tbody>";
		$infoSection .= "<tr><td>Ukupan broj zahtjeva</td><td>" . $requestNumber . "</td></tr>";
		$infoSection .= "<tr><td>Grad</td><td>" . $constructor['city'] . "</td></tr>";
		$infoSection .= "<tr><td>Adresa</td><td>" . $constructor['address'] . "</td></tr>";
		$infoSection .= "<tr><td>E-mail</td><td><a href=\"mailto:" . $constructor['email'] . "\">";
		$infoSection .=  $constructor['email'] . "</a></td></tr>";
		$infoSection .= "</tbody></table>";

		$averageGrade = $this->gradesModel->getAverageGrade($constructor['id']);
		$averageGrade = round($averageGrade);

		$infoSection .= "<table id=\"tableGrades\"><tbody>";
		for($k = 1; $k <= 5; $k++){
			$infoSection .= "<td><img src=\"private/resources/site/star_small.png\" width=\"40\" height=\"40\" alt=\"" . 'star' . $k . "\"";
			if($averageGrade != null and $k <= $averageGrade)
				$infoSection .= "style=\"background-color: gold;\"";
			$infoSection .= "></img></td>";
		}

		$infoSection .= "</tr></tbody></table></div>";

		return $infoSection;
	}



	private function createCommentSection($constructor){
		$comments = $this->commentsModel->select()->where(array('constructor_id' => $constructor['id']))
			->orderBy('time DESC')->exec();
		$canComment = ($this->requestsModel->select()->where(array(
				'user_id' => User::getUserId(),'constructor_id' => $constructor['id']))->exec() != null);

		$commentSection = "<fieldset><ul id=\"comment_list\">";
		if($comments == null)
			$commentSection .= "<h3>Nema komentara</h3>";
		else{
			foreach($comments as $currentComment){
				$userCommented = $this->usersModel->getByKey($currentComment['user_id']);

				$commentSection .= "<li><div class=\"comment\">";
				$commentSection .= "<div class=\"comment_info\">" . Utils::formatDateTime($currentComment['time'])
					. " " . $userCommented['username'] . "</div>";
				$commentSection .= "<div class=\"comment_text\">" . $currentComment['comment'] . "</div>";
				$commentSection .= "</div></li>";
			}
		}
		$commentSection .= "</ul>";

		if($canComment){
			$commentSection .= "<div class=\"comment_post\">";
			$commentSection .= "<textarea id=\"inputComment\" name=\"inputComment\" placeholder=\"Upiši komentar:\"></textarea>";
			$commentSection .= "<br/><input type=\"button\" id=\"buttonPostComment\" value=\"Pošalji\"/>";
			$commentSection .= "</div>";
		}
		$commentSection .= "</fieldset>";

		return $commentSection;
	}



	public function getOutput() {
		$constructor = $this->usersModel->getByKey($_GET['id']);
		echo $this->createBasicInfoSection($constructor);
		echo $this->createCommentSection($constructor);
	}


}
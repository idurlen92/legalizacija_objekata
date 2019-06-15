<?php

class Login implements View{

    private $model = null;
    private $controller = null;

    public function __construct(Model $model, UserController $controller){
        $this->model = $model;
        $this->controller = $controller;
    }


    public function getOutput(){
        if($_SERVER['REQUEST_METHOD'] == 'POST')
            $this->controller->actionLogin($_POST);

        $form = "<div class=\"warning\" id=\"errorDiv\"><h5>Krivo korisničko ime i/ili lozinka</h5></div>
				<form id=\"form_login\" name=\"form_login\" onsubmit=\"return isFormValid();\" method=\"POST\">
				<fieldset>
					<input type=\"text\" id=\"form_login_username\" name=\"form_login_username\" placeholder=\"Korisničko ime\" tabindex=\"1\" autofocus>
					<input type=\"password\" id=\"form_login_password\" name=\"form_login_password\" placeholder=\"Lozinka\" tabindex=\"2\"> <br/>
					<input type=\"checkbox\" id=\"form_login_remember\" name=\"form_login_remember\" tabindex=\"3\">  Zapamti me <br/>
					<input type=\"submit\" value=\"Prijava\" tabindex=\"4\">
				</fieldset>
			</form>";

	    if(isset($_GET['locked']) or ( isset($_GET['attempt']) and $_GET['attempt'] == 3))
	        echo "<h5>Korisnički račun je zaključan!<br>Kontaktirajte administratora za otključavanje.</h5>";
	    else
            echo $form;

    }

}
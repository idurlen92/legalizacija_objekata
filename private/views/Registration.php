<?php

class Registration implements View{

	private $countiesModel = null;
    /** @var UsersModel */
    private $model = null;
    /** @var UserController  */
    private $controller = null;

    public function __construct(UsersModel $model, UserController $controller){
        $this->model = $model;
	    $this->countiesModel = new CountiesModel($model->getDatabase());
        $this->controller = $controller;
    }


    public function getOutput(){
        $counties = $this->countiesModel->select()->orderBy('name')->exec();

        $form = "<form id=\"form_registration\" name=\"form_registration\" method=\"POST\" onsubmit=\"return isFormValid();\">
            <fieldset>
                <table>
                    <tr>
                        <td><label for=\"form_reg_name\">Ime:</label></td>
                        <td><input type=\"text\" id=\"form_reg_name\" name=\"form_reg_name\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_surname\">Prezime:</label></td>
                        <td><input type=\"text\" id=\"form_reg_surname\" name=\"form_reg_surname\"></td>
                    </tr>
                    <tr>
                        <td><label>Spol:</label></td>
                        <td>
                            <label for=\"form_reg_gender_m\">Muško</label>
                            <input type=\"radio\" name=\"form_reg_gender\" id=\"form_reg_gender_m\" value=\"m\">
                            <label for=\"form_reg_gender_f\">Žensko</label>
                            <input type=\"radio\" name=\"form_reg_gender\" id=\"form_reg_gender_f\" value=\"z\">
                        </td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_date\">Datum rođenja:</label></td>
                        <td><input type=\"date\" id=\"form_reg_date\" name=\"form_reg_date\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_username\">Korisničko ime:</label></td>
                        <td><input type=\"text\" id=\"form_reg_username\" name=\"form_reg_username\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_pass\">Lozinka</label></td>
                        <td><input type=\"password\" id=\"form_reg_password\" name=\"form_reg_password\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_pass2\">Ponovljena lozinka</label></td>
                        <td><input type=\"password\" id=\"form_reg_pass2\" name=\"form_reg_pass2\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_email\">E-mail</label></td>
                        <td><input type=\"email\" id=\"form_reg_email\" name=\"form_reg_email\"></td>
                    </tr>
                    <tr>
                        <td><label for=\"form_reg_county\">Županija</label></td>
                        <td>
                            <select id=\"form_reg_county\" name=\"form_reg_county\" form=\"form_registration\">";

	    $form .= "<option value=\"-\"> - </option>";
        foreach($counties as $county)
            $form .= '<option value="' . $county['id'] . '">' . $county['name'] . '</option>' ;

        $form .=  "</select></td></tr>
                        <tr>
                            <td><label for=\"form_reg_city\">Grad</label></td>
                            <td><input type=\"text\" id=\"form_reg_city\" name=\"form_reg_city\"></td>
                        </tr>
                        <tr>
                            <td><label for=\"form_reg_address\">Adresa</label></td>
                            <td><input type=\"text\" id=\"form_reg_address\" name=\"form_reg_address\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"2\"><input type=\"submit\" value=\"Potvrdi\"></td>
                        </tr>
                    </table>
                </fieldset>
            </form>";

        if($_SERVER['REQUEST_METHOD'] == 'POST')
            $this->controller->actionRegistration($_POST);
        else if(!isset($_GET['operation']))
            echo $form;
	    else if($_GET['operation'] == 'finished')
		    echo "<h5> Uspješna registracija!</h5>";
	    else
		    echo "<h5> Greška prilikom registracije!</h5>";
    }



}
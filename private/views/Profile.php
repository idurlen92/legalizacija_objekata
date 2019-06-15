<?php

class Profile implements View{


    /** @var UsersModel */
    private $model = null;
    /** @var UserController  */
    private $controller = null;

    public function __construct(UsersModel $model, UserController $controller){
        $this->model = $model;
        $this->controller = $controller;
    }



    public function getOutput(){
	    $rowNames = array(
		    'name' => 'Ime', 'surname' => 'Prezime', 'gender' => 'Spol', 'birth_date' => 'Datum rođenja',
		    'username' => 'Korisničko ime', 'password' => 'Lozinka', 'email' => 'E-pošta', 'city' => 'Grad',
		    'address' => 'Adresa', 'county' => 'Županija',
		    'user_type' => 'Tip korisnika'
	    );

	    $result = $this->model->select(array('u.*', 'c.name county', 'ut.type_name user_type')
	        )->join(array('counties c', 'user_types ut'), 'u')->where(array('u.id' => User::getUserId()))->exec();
	    $user = $result[0];

        $table = "<table id=\"profileTable\" border=\"1\"><tbody>";
        foreach($rowNames as $key => $value){
            $table .= "<tr><td>" . $value . "</td>" . "<td>" . $user[$key] . "</td>";
        }

        $table .= "</tbody></table></form>";

        echo $table;
    }
}
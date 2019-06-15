<?php

class Documentation implements View{


    public function __construct(){
    }



    public function getOutput(){
	    include_once 'public_html/dokumentacija.html';
    }
}
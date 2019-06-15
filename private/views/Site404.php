<?php

class Site404 implements View{

    public function getOutput() {
        echo "<div style=\"text-align: center;\"><h3> Stranica nije pronađena (404) </h3>"
            . "<img src=\"private/resources/site/error.png\" alt=\"error.png\" />"
	        . "</div>";
    }
}
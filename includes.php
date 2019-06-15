<?php
	include 'private/models/Model.php';
	include 'private/views/View.php';

	function includeFiles($path){
		static $count = -1;
		static $excludes = array(
			'.', '..', 'resources', 'ajax', '.htaccess', 'config.ini', 'Model.php', 'View.php'
		);

		$count++;
		$directory = dir($path);

		while(false !== ($entry = $directory->read())){
			if(!in_array($entry, $excludes)){
				if(is_dir($path . $entry)){
					if($count < 1000){
						//echo "Entering " . $entry . "<br/>";
						includeFiles($path . $entry . '/');
					}
					else{
						echo "Infinite recursion...<br/>";
						return;
					}
				}
				else if(is_file($path . $entry)){
					//echo "Including " . $entry . "<br/>";
					include $path . $entry;
				}
			}// if
		}// while

	}// function includeFiles(path..)
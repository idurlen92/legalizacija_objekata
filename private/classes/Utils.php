<?php

class Utils {


	public static function formatDateTime($dateTime){
		$parts = explode(' ', $dateTime);

		$dateTimeFormatted = self::formateDate($parts[0]) . " ";
		$dateTimeFormatted .= substr($parts[1], 0, 5);

		return $dateTimeFormatted;
	}



	/**
	 * Formats given database date string to Croatian date format string.
	 * @param $date
	 * @param bool $year
	 * @return string
	 */
	public static function formateDate($date, $year = false){
		$monthToStrMonth = array(
			1 => 'siječnja', 2 => 'veljače', 3 => 'ožujka', 4 => 'travnja',
			5 => 'svibnja', 6 => 'lipnja', 7 => 'srpnja', 8 => 'kolovoza',
			9 => 'rujna', 10 => 'listopada', 11 => 'studenog', 12 => 'prosinca'
		);

		$dateFormatted = (string)intval($date[8] . $date[9]) . ". ";
		$month = intval($date[5] . $date[6]);
		$dateFormatted .= $monthToStrMonth[$month];
		if($year)
			$dateFormatted .= " " . substr($date, 0, 4);

		return $dateFormatted;
	}



	/**
	 * Performs searching given string by search string.
	 * @param $string
	 * @param $search
	 * @return bool
	 */
    public static function findString($string, $search) {
	    $string = strtolower($string);
	    $search = strtolower($search);
        return (strpos($string, $search) == false) ? false : true;
    }


    public static function generateCode(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 60; $i++) {
            $randomString .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randomString;
    }



    /**
     * @param $queryStr - Example: pass 'login&type=2' for ...index.php?action=login&type=2
     */
    public static function redirect($queryStr){
	    $location = '?action=' . $queryStr;
	    if($_SERVER['HTTP_HOST'] == 'localhost')
            header("location:" . $location);
	    else
            echo "<script>window.location.href = \"" . $location ."\"</script>";
    }



	/**
	 * @param $title - title of the message
	 * @param $content - content of the message
	 * @param $addressesTo - email address/addresses to send to
	 * @param string $from - senders mail
	 * @return bool
	 */
    public static function sendMail($title, $content, $addressesTo, $from = '<admin@legalizacija-objekata.hr>'){
        return mail($addressesTo, $title,$content, "From: " . $from);
    }



	public static function requestDirectoryExists($requestId){
		$requestFolderPath = 'private/resources/requests/' . $requestId;
		return file_exists($requestFolderPath);
	}


	public static function getRequestDirectoryPath($requestId){
		return 'private/resources/requests/' . $requestId . '/';
	}


	/**
	 * @param $requestId
	 */
	public static function makeRequestDirectory($requestId){
		$requestFolderPath = 'private/resources/requests/' . $requestId;
		if(!file_exists($requestFolderPath)){
			if(!mkdir($requestFolderPath))
				echo "Error making directory: " . $requestFolderPath;
		}
	}



}
<?php
    session_start();

	include 'includes.php';
	includeFiles('private/');
    $siteController = new SiteController();
?>


<!DOCTYPE html>
<html lang="hr">
	<head>
	    <title> <?php echo SiteController::getTitle(); ?> </title>
	    <meta charset="UTF-8">
	    <meta name="author" content="Ivan Durlen">
	    <meta name="description" content="Naslovna stranica">
	    <meta name="keywords" content="">
	    <meta name="dc.created" content="">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" type="text/css" href="public_html/css/style.css">
	</head>

	<body id="body">
	    <header id="header">
		    <div id="header_content">
			    <h4>Legalizacija objekata</h4>
		    </div>
	    </header>

	    <div id="navigation">
	        <nav>
	            <ul id="left_navigation">
	                <li><a href="?action=index"> Početna </a></li>
	                <?php
		                SiteController::getLeftMenu();
	                ?>
	            </ul>
		        <ul id="right_navigation">
			        <?php
				        if(User::isLoggedIn()){
					        echo "<li><a href=\"?action=profile\"> Moj profil </a></li>";
					        echo "<li><a href=\"?action=logout\">Odjava</a></li>";
				        }
				        else{
					        echo "<li><a href=\"?action=registration\"> Registracija </a></li>";
					        echo "<li><a href=\"?action=login\">Prijava</a></li>";
				        }
			        ?>
		        </ul>
	        </nav>
	    </div>

	    <div id="content" style="visibility: hidden;">
	        <?php
	            SiteController::handleRequest();
	        ?>
	    </div>

	    <footer id="footer" style="visibility: hidden; clear: both;">
		    <h5> &#169; Ivan Durlen 2015 <br/> </h5>
		    <a href="mailto:idurlen@foi.hr">idurlen@foi.hr</a>
	    </footer>

	    <script type="text/javascript" src="public_html/js/library/jquery.js"></script>
	    <script type="text/javascript" src="public_html/js/utilities/utils.js"></script>
	    <script type="text/javascript" src="public_html/js/utilities/effects.js"></script>
	    <script type="text/javascript" src="<?php echo 'public_html/js/page/' . SiteController::getJSFile(); ?>"></script>
	</body>
</html>







<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Resturant Coupons</title>
<link rel="stylesheet" href="css/main.css" type="text/css" />
</head>
<body>
<?php
ini_set('display_errors', true);
 // for the Parse object
require "autoload.php";
// this should be somewhere above the HTML files on the server
include("variables.php");
// for Parse initialization
// must have Parse\ in front of all of these; bad documentation
Parse\ParseClient::initialize( $app_id, $rest_key, $master_key );
use Parse\ParseObject;
use Parse\ParseQuery;


// local Parse library
include("class.library.php");
include("layout.php");

$parse_library = new parseLibrary;

# any messages sent to this page?
$sent_msg = isset($_GET['login_msg']) ? $_GET['login_msg'] : 0;

#order the books available
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : "subject"; 

// start the main layout of the page
startMainLayout();

if(isset($_POST['email'], $_POST['password'])) {
	# can check email/password for junk
	# do this with JS on the front end but, doesn't hurt to do it again
	$email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
	if (preg_match($email_regex, $_POST['email'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];
		$parse_library->checkLogin($email, $password, $library_db);
	} else {
		# not a proper email address, go home with msg
		$parse_library->changeLocation("index.php?login_msg=1");
	}
} else {
	$parse_library->showSignUp();
	$parse_library->showLogin($sent_msg);
}

endMainLayout();

?>

</body>
</html>
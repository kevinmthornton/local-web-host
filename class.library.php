<?php
# class for the parse library and all the functions


class parseLibrary {
	#### index page functions 
	# show the status of all books
	function showBookStatus($_order_by, $_library_db) {
		echo "<h2>Book Status</h2>";
		echo "<table width=\"550\" cellpadding=\"2\" cellspacing=\"2\">\n";
		echo "<tr><td width=\"50\"><a href=\"?order_by=subject\">Subject</a></td><td width=\"300\"><a href=\"?order_by=title\">Title</a></td><td width=\"100\"><strong>Who Has</strong></td><td width=\"100\"><strong>Return Date</strong></td></tr>\n";
		$select_book =  $_library_db->stmt_init();
		if($select_book->prepare("SELECT id, title, subject FROM tbl_books ORDER BY $_order_by")) {
			$select_book->execute();
			$select_book->bind_result($id, $title, $subject);
			$select_book->store_result();
			 while ($select_book->fetch()) {
				$this->seeIfCheckedOut($id, $title, $subject, $_library_db);	
			 }
			$select_book->close();
		} else {
			echo "cannot prepare in showBookStatus";
		}
		echo "</table> <p>&nbsp;</p>";
	} # show book status
	
	# see if this book is checked out, if so, color it different
	function seeIfCheckedOut($_book_id, $_title, $_subject, $_library_db2) {
		$select_book2 =  $_library_db2->stmt_init();
		$select_sql = "
			SELECT 
				b.id, 
				b.title, 
				co.book_id, 
				co.when_checked_out, 
				co.return_date,
				u.first
			FROM 
				tbl_books b, 
				tbl_checked_out co,
				tbl_users u
			WHERE b.id = co.book_id 
			AND co.user_id = u.id
			AND co.book_id = $_book_id 
			ORDER BY b.title
		";
		#echo "<p>$select_sql</p>";
		if($select_book2->prepare($select_sql)) {
			$select_book2->execute();
			$select_book2->bind_result($book_id, $title, $co_book_id, $when_checked_out, $return_date, $firstname);
			$select_book2->store_result();
			$select_book2->fetch();
			
			#echo "num rows: " . $select_book2->num_rows . "<br/>";
			if($select_book2->num_rows == 1) { # yes, this book is checked out
				echo "<tr style='background-color:#ccc'><td>$_subject</td> <td>$_title</td> <td>$firstname</td> <td>$return_date</td></tr>\n ";
			} else {
				echo "<tr style='background-color:#fff'><td>$_subject</td> <td>$_title</td><td> </td> <td> Available</td></tr>\n ";
			}
			$select_book2->close();
		} else {
			echo "cannot prepare seeifCheckedOut <p> $select_sql </p>" . $_library_db->error;
		}
	} # see if checked out

		
	# show the login form
	function showLogin($_login_msg) {
	    echo "<div id='loginForm'>";
		echo "<h2>Login</h2>";
		
		switch($_login_msg) {
			case "1":
				echo "<p class=\"redColor\"> Password not found</p>";
				break;
			case "2" :
				echo "<p class=\"redColor\"> You are now logged out</p>";	
				break;
			case "3" :
				echo "<p class=\"redColor\"> Bad email address</p>";	
				break;
			default:
				break;
		}
		
		// suck in the file and place in this method
		$loginFileString = file_get_contents('login_form.html');
		echo $loginFileString;
		echo "</div>";
	} #show login
	
	# show the sign up form
	function showSignUp() {
	    echo "<div id='signUp'>";
	    echo "<h2>Sign Up </h2>";
	    
	    echo "</div>";
	}
	
	# check the login information 
	function checkLogin($_email, $_password, $_library_db) {
		$select_user =  $_library_db->stmt_init();
		if ($select_user->prepare("SELECT id, first, password, salt FROM tbl_users WHERE email = ? LIMIT 1")) { 
		  $select_user->bind_param('s', $_email); 
		  $select_user->execute(); 
		  $select_user->store_result();
		  $select_user->bind_result($user_id, $first, $db_password, $salt); # bind db return to vars
		  $select_user->fetch();
		  $passwordSalt = hash('sha512', $_password.$salt); # hash the password with the unique salt.
	 
		  if($select_user->num_rows == 1) { # does user exist?
			 if($db_password == $passwordSalt) { # password from select match the password the user submitted? 
				# they are OK, set up session vars
			   $_SESSION['user_id'] = $user_id; 
			   $_SESSION['first_name'] = $first; 
			   # login successful so, change location to user page and check session there
			   $this->changeLocation("user.php");
			 } else {
				 #echo "<p class=\"red\"> password does not match</p> ";
				$this->changeLocation("index.php?login_msg=1");
			 }
		  } else {
			  #echo "<p class=\"red\">Email not found</p>";
			 $this->changeLocation("index.php?login_msg=3");
		  }
	  } else {
		 $this->changeLocation("index.php?login_msg=3");
	  }
	} # check login
		
	# log the user out
	function logout() {
		session_start();
		session_unset();
		session_destroy();
		$this->changeLocation("index.php?login_msg=2");
	} # logout
	
	
	##### USER page functions
	#check on the session to make sure they can see this page
	function checkSession($_library_db) {
		if(isset($_SESSION['user_id'] )) {
			$select_user =  $_library_db->stmt_init();
			if ($select_user->prepare("SELECT id FROM tbl_users WHERE id = ? LIMIT 1")) { 
				$select_user->bind_param('s', $_SESSION['user_id']); 
		  		$select_user->execute(); 
				$select_user->store_result();
		  		$select_user->bind_result($db_first);
				if($select_user->num_rows == 1) { # does user exist?
			 		if($db_first != $_SESSION['first_name']) { # first from db and first from session match? if not, send them home
						#echo "names do not match";
						$this->changeLocation("index.php?login_msg=1");
					}
				}
			} else {
				# user not found
				#echo "user not found 1";
				$this->changeLocation("index.php?login_msg=1");
			}
		} else {
			#echo "user not found 2";
			$this->changeLocation("index.php?login_msg=1");
		}
	} # check session
	
	# show the users checked out books
	function showCheckedOutBooks($_library_db) {
		echo "<h2>Hello, " . $_SESSION['first_name'] . " these are your checked out books</h2>";
		echo "<table width=\"400\" cellpadding=\"2\" cellspacing=\"2\">\n";
		echo "<tr><td width=\"300\"></td><td width=\"100\"></td></tr>\n";
		$select_book =  $_library_db->stmt_init();
		if($select_book->prepare("SELECT b.id, b.title FROM tbl_books b, tbl_checked_out co WHERE b.id = co.book_id AND co.user_id = ". $_SESSION['user_id'] . " ORDER BY b.title")) {
			$select_book->execute();
			$select_book->bind_result($id, $title);
			 while ($select_book->fetch()) {
					echo "<tr><td>$title</td> <td><a href=\"user.php?check_in=1&book_id=$id\">Check In</a></td> </tr>\n ";
			 }
			$select_book->close();
		} else {
			echo "cannot prepare";
		}
		echo "</table><p>&nbsp;</p>";
		
	} # show checked out

	# show the users checked out books
	function showAvailableBooks($_order_by, $_library_db) {
		echo "<h2>Books Available for checkout</h2>\n";
		echo "<table width=\"500\" cellpadding=\"2\" cellspacing=\"2\">\n";
		echo "<tr><td width=\"100\"><a href=\"?order_by=subject\">Subject</a></td><td width=\"300\"><a href=\"?order_by=title\">Title</a></td><td width=\"100\"></td></tr>\n";
		$select_book =  $_library_db->stmt_init();
		$select_sql = "
		SELECT id, title, subject, isbn FROM tbl_books
		WHERE id NOT IN (
				SELECT b.id 
				FROM tbl_books b, tbl_checked_out co 
				WHERE b.id = co.book_id )  ORDER BY $_order_by
		";

		if($select_book->prepare($select_sql)) {
			$select_book->execute();
			$select_book->bind_result($id, $title, $subject, $isbn);
			 while ($select_book->fetch()) {
					echo "<tr><td>$subject</td> <td>$title</td> <td><a href=\"user.php?check_out=1&book_id=$id\">Check Out</a></td> </tr>\n ";
			 }
			$select_book->close();
		} else {
			echo "cannot prepare";
		}
		echo "</table><p>&nbsp;</p>";
	} # show checked out

	# check in this users book passing the book id
	function checkInBook($_book_id, $_library_db) {
		$delete_book =  $_library_db->stmt_init();
		$delete_sql = "DELETE FROM tbl_checked_out  WHERE book_id = $_book_id AND user_id = " . $_SESSION['user_id'];
		#echo $delete_sql;
		if ($delete_book->prepare($delete_sql)) {    
		   	$delete_book->execute();
		} else {
			echo "prepare failed";
		}
		echo "<p class=\"redColor\">Checked In Book</p>";
	} # check in book
	
	# check out this users book passing the book id
	function checkOutBook($_book_id, $_library_db) {
		$checked_out_date = date("F j, Y"); # today
		$two_weeks = mktime(0, 0, 0, date("m")  , date("d")+14, date("Y")); # two weeks from now
		$return_date = gmdate("F, j, Y", $two_weeks);
		
		$insert_book =  $_library_db->stmt_init();
		if ($insert_book->prepare("INSERT INTO tbl_checked_out (book_id, user_id, when_checked_out, return_date) VALUES (?,?,?,? )")) {    
			$insert_book->bind_param('iiss', $_book_id, $_SESSION['user_id'], $checked_out_date, $return_date);
		   	$insert_book->execute();
		} else {
			echo "prepare failed";
		}
		echo "<p class=\"redColor\">Checked Out Book</p>";
	} # check out book	
	
	# get the user into the database with hashed passwords
	# could run this out of a .csv iterating over lines
	function insertUsers($_library_db) {
		$first = "";
		$last = "";
		$email = "";
		$password =""; 
		# create salt
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		# create salted password
		$password = hash('sha512', $password.$random_salt);
		 
		$insert_user =  $_library_db->stmt_init();
		if ($insert_user->prepare("INSERT INTO tbl_users (first, last, email, password, salt) VALUES (?, ?, ?, ?, ?)")) {    
		   $insert_user->bind_param('sssss', $first, $last, $email, $password, $random_salt); 
		   $insert_user->execute();
		}
	}
	
	function changeLocation($_location) {
		echo "change location";
		if (ob_get_length() > 0) {
			ob_end_clean(); #clear buffer, end collection of content
		}
		print('<script language="JavaScript"> location.replace("'.$_location.'"); </script> <noscript><META http-equiv="Refresh" content="0;URL='.$_location.'"></noscript>');
	} # change location 

} # tech library class



?>

<?php
ini_set('display_errors', true);

require "autoload.php";

// you get these from parse.com > settings > Keys
$app_id = "Y6jk4OSvcJpg3qX12SlhlJRkPw68nY9NyZ60FyzN";
$rest_key = "iseByHNR2KtqCmjHe5GczjDWmMqJjnScqkVV659K";
$master_key = "mlM9cov7H4jAqkgCAO4DMXOtIZGYBN2PsDqfrqMq";

// must have Parse\ in front of all of these; bad documentation
Parse\ParseClient::initialize( $app_id, $rest_key, $master_key );

use Parse\ParseObject;
use Parse\ParseQuery;

// Retrieving
$query = new ParseQuery("Birthday");
$query->each(function($obj) {
  echo $obj->getObjectId();
  echo " " . $obj->get("name") . " " . $obj->get("giftIdeas") ."<br> ";
});

// just get a specific record
//try {
//  $timsBirthday = $query->get('mmSHXt6GpO');
//   //The object was retrieved successfully
//  echo "<p>tim's birthday " . $timsBirthday->get("name") . "</p>";
//  $timsBirthday->set('name', '');
//  
//  //$timsBirthday->delete('name');
//  $timsBirthday->save();
//  //$timsBirthday->destroy();
//  
//} catch (ParseException $error) {
//   //The object was not retrieved successfully.
//   //error is a ParseException with an error code and message.
//  echo $error->getCode();
//  echo $error->getMessage();
//}

// creating
//$markBDay = new DateTime('04/25/1969'); //date("M-d-Y", mktime(0, 0, 0, 13, 1, 1997));
//$newBirthday = new ParseObject("Birthday");
//$newBirthday->set("name", "mark");
//$newBirthday->set("giftIdeas", "bike");
//$newBirthday->set("date", $markBDay);
// 
//try {
//  $newBirthday->save();
//  echo 'New object created with objectId: ' . $newBirthday->getObjectId();
//} catch (ParseException $ex) {  
//  // Execute any logic that should take place if the save fails.
//  // error is a ParseException object with an error code and message.
//  echo 'Failed to create new object, with error message: ' + $ex->getMessage();
//}

// updating
// create a new date object
//$markBDay = new DateTime('04/25/1969'); 
//$marksBirthday = $query->get('fRSxadoHgu');
//$marksBirthday->set("date", $markBDay);
//$marksBirthday->save();

// deleting
//$timsBirthday = $query->get('mmSHXt6GpO');
//echo "<p>tim's birthday below " . $timsBirthday->get("name") . "</p>";
//$timsBirthday->delete("name");
//$timsBirthday->save();
//$timsBirthday->destroy();


/*
 Add the "use" declarations where you'll be using the classes.
 
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;


Objects:

$object = ParseObject::create("TestObject");
$objectId = $object->getObjectId();
$php = $object->get("elephant");

// Set values:
$object->set("elephant", "php");
$object->set("today", new DateTime());
$object->setArray("mylist", [1, 2, 3]);
$object->setAssociativeArray(
  "languageTypes", array("php" => "awesome", "ruby" => "wtf")
);

// Save:
$object->save();


Users:

// Signup
$user = new ParseUser();
$user->setUsername("foo");
$user->setPassword("Q2w#4!o)df");
try {
  $user->signUp();
} catch (ParseException $ex) {
  // error in $ex->getMessage();
}

// Login
try {
  $user = ParseUser::logIn("foo", "Q2w#4!o)df");
} catch(ParseException $ex) {
  // error in $ex->getMessage();
}

// Current user
$user = ParseUser::getCurrentUser();


Security:

// Access only by the ParseUser in $user
$userACL = ParseACL::createACLWithUser($user);

// Access only by master key
$restrictedACL = new ParseACL();

// Set individual access rights
$acl = new ParseACL();
$acl->setPublicReadAccess(true);
$acl->setPublicWriteAccess(false);
$acl->setUserWriteAccess($user, true);
$acl->setRoleWriteAccessWithName("PHPFans", true);


Queries:

$query = new ParseQuery("TestObject");

// Get a specific object:
$object = $query->get("anObjectId");

$query->limit(10); // default 100, max 1000

// All results:
$results = $query->find();

// Just the first result:
$first = $query->first();

// Process ALL (without limit) results with "each".
// Will throw if sort, skip, or limit is used.
$query->each(function($obj) {
  echo $obj->getObjectId();
});


Cloud Functions:

$results = ParseCloud::run("aCloudFunction", array("from" => "php"));


Analytics:

PFAnalytics::trackEvent("logoReaction", array(
  "saw" => "elephant",
  "said" => "cute"
));


Files:

// Get from a Parse Object:
$file = $aParseObject->get("aFileColumn");
$name = $file->getName();
$url = $file->getURL();
// Download the contents:
$contents = $file->getData();

// Upload from a local file:
$file = ParseFile::createFromFile(
  "/tmp/foo.bar", "Parse.txt", "text/plain"
);

// Upload from variable contents (string, binary)
$file = ParseFile::createFromData($contents, "Parse.txt", "text/plain");


Push:
$data = array("alert" => "Hi!");

// Push to Channels
ParsePush::send(array(
  "channels" => ["PHPFans"],
  "data" => $data
));

// Push to Query
$query = ParseInstallation::query();
$query->equalTo("design", "rad");
ParsePush::send(array(
  "where" => $query,
  "data" => $data
));

*/
 
// https://github.com/ParsePlatform/parse-php-sdk
?>
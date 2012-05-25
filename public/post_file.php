<?php
# _Author: Donald L. Merand_

/*
SETUP
=====
*/

# Set some constants that we'll need later
$doc_root = $_SERVER["DOCUMENT_ROOT"];
$request_method = $_SERVER['REQUEST_METHOD'];
$server_protocol = "http";
$server_name = $_SERVER['SERVER_NAME'];

# Set an errors variable that we test against to see if we should move forward
# _Any form validation should set the $errors variable_
$errors = array();


/*
AWESOME SECURITY
================
This is our awesome security system. Checking passed authentication tokens against a "database" of existing ones.

Part II of the security system is to use basic HTTP auth. Unbreakable!
*/
switch($request_method)
{
  case 'POST':
    $token = $_POST['token'];
    break;
	case 'DELETE':
		parse_str(file_get_contents('php://input'), $_DELETE);
    $token = $_DELETE['token']; 
    $dir = escapeshellarg($_DELETE['dir']);
    break;
  default:
    # This page only responds to POST + DELETE requests. 
    # Add an error if we're doing anything else
    $errors[] = "Invalid HTTP request type: $request_method.";
}

$auth_tokens = array(
  "ba38a82e75d4faf026822455026df469" => "Donald"
);

# Pass an error if no token was passed, or the passed token isn't in our "database".
if (!array_key_exists($token, $auth_tokens)) {
  $errors[] = "Auth failed";
}


/*
POST
====
*/
# Page submitted, no errors - set local variables based on POST values
if ($request_method == "POST" && empty($errors)) { 
	# reads the name of the file the user submitted for uploading
	$file_name=$_FILES['file']['name'];
	# if it is not empty
	if ($file_name)	{
    # `$_FILES['file']['tmp_name']` is the temporary filename of the file
    # in which the uploaded file was stored on the server
    $tmp_name = $_FILES['file']['tmp_name'];

    # We're going to store the files in a unique directory, in this case named after a UNIX epoch timestamp.
    $result_dir = time();
    $result_path="$doc_root/$result_dir"; 
    $result_file_path="$result_path/$file_name";
    # Recall that $type must be set for this block to execute
    
    # Make sure the image directory exists - test and make if empty
    $dir_check = "[ -d '$result_path' ] || /bin/mkdir '$result_path'";
    system($dir_check, $mkdir_result);
    # Remember that shell commands exit 0 on success, so if we see a result there was a failure.
    if ($mkdir_result) { $errors[] = "Directory creation failed"; }
    
    # Now move the tmp file into it's permanent location
    $move_cmd = "/bin/mv '$tmp_name' '$result_file_path'";
    system($move_cmd, $is_moved);
    if ($is_moved) { $errors[] = 'File move failed'; }

    $result = "$server_protocol://$server_name/$result_dir/$file_name posted.";
  } else {
    $errors[] = "No valid file name.";
  }
}


/*
DELETE
======
*/
# "Dir" variable should be a directory
if ($request_method == 'DELETE' && empty($dir)) {
  # You have to send a "dir" variable
  $errors[] = "DELETE requested, but no 'dir' provided";
} elseif ($request_method == 'DELETE' && empty($errors)) {
  $dir_path = "$doc_root/$dir";

  # Actually run the directory removal command
  $rm_command = "[ -d $dir_path ] && /bin/rm -rf '$dir_path'";
  system($rm_command, $rm_failed);

  # If there was a removal failure...
  if ($rm_failed) { $errors[] = "$rm_failed: Directory creation failure"; }

  # If we didn't get errors above, we'll be using this result value
  $result = "$dir removed.";
}


/*
OUTPUT HAPPENS BELOW
====================
*/

# Return text not HTML
Header('Content-type: text/plain');

# Print errors if any were passed from form parse above
if (!empty ($errors)) {
  print "Errors: \n";
  print implode ("\n", $errors);
} else {
  # Otherwise print the result of whatever happened above
  print $result;
}

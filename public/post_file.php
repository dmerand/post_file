<?php
# _Author: Donald L. Merand_

# Set some constants that we'll need later
$server_name = $_SERVER['SERVER_NAME'];
$server_protocol = "http";

# Set an errors variable that we test against to see if we should move forward
$errors = array();

# This page only responds to POST requests. Add an error if we're doing anything else
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  $errors[] = 'Invalid HTTP request type.';
}

# This is our awesome security system. Checking passed authentication tokens against a "database" of existing ones.
$token = $_POST['token'];
$auth_tokens = array(
  "ba38a82e75d4faf026822455026df469" => "Donald"
);

# Pass an error if no token was passed, or the passed token isn't in our "database".
if (!array_key_exists($token, $auth_tokens)) {
  $errors[] = "Auth failed";
}

# _Any form validation should happen here, and set the $errors variable_

# Page submitted, no errors - set local variables based on POST values
if (empty($errors)) { 
	# reads the name of the file the user submitted for uploading
	$file_name=$_FILES['file']['name'];
	# if it is not empty
	if ($file_name)	{
    # Un-quote passed filename
		$file_name = stripslashes($file_name);
    
    # `$_FILES['file']['tmp_name']` is the temporary filename of the file
    # in which the uploaded file was stored on the server
    $tmp_name = $_FILES['file']['tmp_name'];

    # We're going to store the files in a unique directory, in this case named after a UNIX epoch timestamp.
    $doc_root = $_SERVER["DOCUMENT_ROOT"];
    $result_dir = time();
    $result_path="$doc_root/$result_dir"; 
    $result_file_path="$result_path/$file_name";
    # Recall that $type must be set for this block to execute
    
    # Make sure the image directory exists - test and make if empty
    $dir_check = "[ -d '$result_path' ] || $(which mkdir) '$result_path'";
    system($dir_check, $mkdir_result);
    # Remember that shell commands exit 0 on success, so if we see a result there was a failure.
    if ($mkdir_result) { $errors[] = "Directory creation failed"; }
    
    # Now move the tmp file into it's permanent location
    $move_cmd = "mv '$tmp_name' '$result_file_path'";
    system($move_cmd, $is_moved);
    if ($is_moved) { $errors[] = 'File move failed'; }
  } else {
    $errors[] = "No valid file name.";
  }
}


# OUTPUT HAPPENS BELOW
# ====================


# Return text not HTML
Header('Content-type: text/plain');

# Print errors if any were passed from form parse above
if (!empty ($errors)) {
  print "Errors: \n";
  print implode ("\n", $errors);
} else {
  # Otherwise print the address of the file you just uploaded.
  print "$server_protocol://$server_name/$result_dir/$file_name";
}

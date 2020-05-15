<?php

# Code taken from LTB Self Service Password

/* @function string posthook_command(string $posthook, string  $login, string $newpassword, false|boolean $posthook_password_encodebase64)
   Creates the command line to execute for the posthook process. Passwords will be base64 encoded if configured. Base64 encoding will prevent passwords with special 
   characters to be modified by the escapeshellarg() function.
   @param $postkook string script/command to execute for procesing posthook data
   @param $login string username to change/set password for
   @param $newpassword string new passwword for given login
   @param posthook_password_encodebase64 boolean set to true if passwords are to be converted to base64 encoded strings
*/
function posthook_command($posthook, $login, $newpassword, $posthook_password_encodebase64 = false) {
        $command = '';
        if ( isset($posthook_password_encodebase64) && $posthook_password_encodebase64 ) {
                $command = escapeshellcmd($posthook).' '.escapeshellarg($login).' '.base64_encode($newpassword);

        } else {
                $command = escapeshellcmd($posthook).' '.escapeshellarg($login).' '.escapeshellarg($newpassword);
        }
        return $command;
}

?>

<?php

# Code taken from LTB Self Service Password

/* @function string hook_command(string $hook, string  $login, string $newpassword, null|string $oldpassword, null|boolean $hook_password_encodebase64)
   Creates the command line to execute for the prehook/posthook process. Passwords will be base64 encoded if configured. Base64 encoding will prevent passwords with special
   characters to be modified by the escapeshellarg() function.
   @param $hook string script/command to execute for procesing hook data
   @param $login string username to change/set password for
   @param $newpassword string new passwword for given login
   @param $oldpassword string old password for given login
   @param hook_password_encodebase64 boolean set to true if passwords are to be converted to base64 encoded strings
*/
function hook_command($hook, $login, $newpassword, $oldpassword = null, $hook_password_encodebase64 = false) {

    $command = '';
    if ( isset($hook_password_encodebase64) && $hook_password_encodebase64 ) {
        $command = escapeshellcmd($hook).' '.escapeshellarg($login).' '.base64_encode($newpassword);

        if ( ! is_null($oldpassword) ) {
            $command .= ' '.base64_encode($oldpassword);
        }

    } else {
        $command = escapeshellcmd($hook).' '.escapeshellarg($login).' '.escapeshellarg($newpassword);

        if ( ! is_null($oldpassword) ) {
            $command .= ' '.escapeshellarg($oldpassword);
        }
    }
    return $command;
}

?>

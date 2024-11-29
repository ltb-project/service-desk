<?php

# Code taken from LTB Self Service Password

/* @function string password_hook_command(string $hook, string  $login, string $newpassword, null|string $oldpassword, null|boolean $hook_password_encodebase64)
   Creates the command line to execute for the prehook/posthook for password reste. Passwords will be base64 encoded if configured. Base64 encoding will prevent passwords with special
   characters to be modified by the escapeshellarg() function.
   @param $hook string script/command to execute for procesing hook data
   @param $login string username to change/set password for
   @param $newpassword string new passwword for given login
   @param $oldpassword string old password for given login
   @param hook_password_encodebase64 boolean set to true if passwords are to be converted to base64 encoded strings
 */
function password_hook_command($hook, $login, $newpassword, $oldpassword = null, $hook_password_encodebase64 = false) {

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

/* @function string hook_command(string $hook, string $login)
   Creates hook command line passing login as parameter
   @param $hook string script/command to execute for procesing hook data
   @param $login string username
 */
function hook_command($hook, $login) {
    $command = escapeshellcmd($hook).' '.escapeshellarg($login);
    return $command;
}

/* @function string validity_hook_command(string $hook, string $login, string $start_date, string $end_date)
   Creates hook command line passing login and dates as parameter
   @param $hook string script/command to execute for procesing hook data
   @param $login string username
   @param $start_date string start date YYYY-MM-DD
   @param $end_date string end date YYYY-MM-DD
 */
function validity_hook_command($hook, $login, $start_date, $end_date) {
    if (!$start_date) { $start_date = "0000-00-00"; }
    if (!$end_date) { $end_date = "0000-00-00"; }
    $command = escapeshellcmd($hook).' '.escapeshellarg($login).' '.escapeshellarg($start_date).' '.escapeshellarg($end_date);
    return $command;
}

?>

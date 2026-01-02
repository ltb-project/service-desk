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

/* @function string hook_command(string $hook, string arg1, string arg2,...)
   Creates hook command line passing multiple arguments
   @param $hook string script/command to execute for procesing hook data
   @param $argN string Nth argument
 */
function hook_command($hook, ...$args) {
    $command = escapeshellcmd($hook);
    foreach ($args as $arg) {
        $command .= ' '.escapeshellarg($arg);
    }
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

function get_hook_login($dn, $ldapInstance, $login_attribute)
{
    $login_value = "";
    if(!empty($dn) && !empty($login_attribute))
    {
        $login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $login_attribute);
    }
    return $login_value;
}

function call_external_command($hookConfig, $entrypoint, $login_value, $params)
{
    $returnCode = 0;
    $returnMessage = "";
    $returnedDN = isset($params['dn']) ? $params['dn'] : null;
    $returnedEntry = isset($params['entry']) ? $params['entry'] : null;

    switch ($entrypoint) {

        case "passwordCheck":
        case "passwordReset":
            $password = $params['password'];
            $command = password_hook_command($hookConfig['externalScript'],
                                             $login_value,
                                             $password,
                                             null,
                                             $hookConfig['encodebase64']);
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            break;

        case "updateValidityDates":
            $start_date = $params['start_date'];
            $end_date = $params['end_date'];
            $command = validity_hook_command($hookConfig['externalScript'],
                                             $login_value,
                                             $start_date,
                                             $end_date);
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            break;

        case "passwordLock":
        case "passwordUnlock":
        case "accountEnable":
        case "accountDisable":
        case "deleteAccount":
            $command = hook_command($hookConfig['externalScript'], $login_value);
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            break;

        case "createAccount":
            $dn = $params['dn'];
            $command = hook_command($hookConfig['externalScript'], $dn, json_encode($returnedEntry));
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            $returnedDN = isset($output[1]) ? $output[1] : $dn;
            if(count($output) > 2) {
                $returnedEntry = json_decode(implode('', array_slice($output, 2)), true);
            }
            break;

        case "updateAccount":
            $dn = $params['dn'];
            $command = hook_command($hookConfig['externalScript'], $dn, json_encode($returnedEntry));
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            if(count($output) > 1) {
                $returnedEntry = json_decode(implode('', array_slice($output, 1)), true);
            }
            break;

        case "renameAccount":
            $dn      = $params['dn'];
            $new_rdn = $params['new_rdn'];
            $parent  = $params['parent'];
            $command = hook_command($hookConfig['externalScript'], $login_value, $dn, $new_rdn, $parent);
            exec($command, $output, $returnCode);
            $returnMessage = isset($output[0]) ? $output[0] : "";
            break;

    }
    return array($returnCode, $returnMessage, $returnedDN, $returnedEntry);
}

function call_external_function($hookConfig, $entrypoint, $login_value, $params)
{
    $returnCode = 0;
    $returnMessage = "";
    $returnedDN = isset($params['dn']) ? $params['dn'] : null;
    $returnedEntry = isset($params['entry']) ? $params['entry'] : null;

    switch ($entrypoint) {

        case "passwordCheck":
        case "passwordReset":
            $password = $params['password'];
            if( isset($hookConfig['encodebase64']) &&
                $hookConfig['encodebase64'] )
            {
                $password = base64_encode($params['password']);
            }
            $params = [$login_value, $password];
            list($returnCode, $returnMessage) =
                $hookConfig['function'](...$params);
            break;

        case "updateValidityDates":
            $start_date = $params['start_date'];
            $end_date = $params['end_date'];
            $params = [$login_value, $start_date, $end_date];
            list($returnCode, $returnMessage) =
                $hookConfig['function'](...$params);
            break;

        case "passwordLock":
        case "passwordUnlock":
        case "accountEnable":
        case "accountDisable":
        case "deleteAccount":
            $params = [$login_value];
            list($returnCode, $returnMessage) =
                $hookConfig['function'](...$params);
            break;

        case "createAccount":
            $dn = $params['dn'];
            $params = [$dn, $returnedEntry];
            list($returnCode, $returnMessage, $returnedDN, $returnedEntry) =
                $hookConfig['function'](...$params);
            break;

        case "updateAccount":
            $dn = $params['dn'];
            $params = [$dn, $returnedEntry];
            list($returnCode, $returnMessage, $returnedEntry) =
                $hookConfig['function'](...$params);
            break;

        case "renameAccount":
            $dn      = $params['dn'];
            $new_rdn = $params['new_rdn'];
            $parent  = $params['parent'];
            $params = [$login_value, $dn, $new_rdn, $parent];
            list($returnCode, $returnMessage) =
                $hookConfig['function'](...$params);
            break;

    }
    return array($returnCode, $returnMessage, $returnedDN, $returnedEntry);
}

function hook($hookConfig, $entrypoint, $login_value, $params) {

    $returnCode = 0; # success return code by default
    $returnMessage = "";
    $returnedDN = isset($params['dn']) ? $params['dn'] : null;
    $returnedEntry = isset($params['entry']) ? $params['entry'] : null;

    if ( isset($hookConfig['externalScript']) ||
         isset($hookConfig['function']) ) {
        if ( isset($login_value) ) {

            # Compute and run external command
            if(isset($hookConfig['externalScript']))
            {
                list($returnCode, $returnMessage, $returnedDN, $returnedEntry) =
                    call_external_command($hookConfig, $entrypoint, $login_value, $params);
            }

            # Compute arguments and run external function
            if(isset($hookConfig['function']))
            {
                list($returnCode, $returnMessage, $returnedDN, $returnedEntry) =
                    call_external_function($hookConfig, $entrypoint, $login_value, $params);
            }

        }
        else
        {
            $returnCode = 255;
            $returnMessage = "No login found, cannot execute hook script";
        }
    }

    return array($returnCode, $returnMessage, $returnedDN, $returnedEntry);
}

?>

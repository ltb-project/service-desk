<?php
/*
 * Reset password in LDAP directory
 */

if($result === "" and $always_authenticate_admin) {
    if(isset($_POST["admin_username"]) and $_POST["admin_username"] and isset($_POST["admin_password"]) and $_POST["admin_password"]) {
        $ldap_binddn = $ldap_login_attribute ."=" . $_POST["admin_username"] ."," . $ldap_user_base;
        $ldap_bindpw = $_POST["admin_password"];
    } else {
        $result = "admincredentialsrequired";
    }
}

?>
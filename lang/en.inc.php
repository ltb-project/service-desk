<?php

// Continue session variables
session_start();
$displayname = $_SESSION["displayname"];

#==============================================================================
# English
#==============================================================================

$messages["label_created"] = "Created";
$messages["label_modified"] = "Modified";
$messages['accountlocked'] = "Account is locked";
$messages['accountnotlocked'] = "Fail to lock account";
$messages['accountnotunlocked'] = "Fail to unlock account";
$messages['accountstatus'] = "Account status";
$messages['accountunlocked'] = "Account is not locked";
$messages['checkpassword'] = "Check password";
$messages['currentpassword'] = "Current password";
$messages['dashboards'] = "Dashboards";
$messages['displayentry'] = "Display entry";
$messages['dnrequired'] = "Entry identifier required";
$messages['editentry'] = "Edit entry";
$messages['entriesfound'] = "entries found";
$messages['entryfound'] = "entry found";
$messages['expiredaccounts'] = "Passwords expired";
$messages['false'] = "No";
$messages['forcereset'] = "Force reset at next connection";
$messages['idleaccounts'] = "Idle accounts";
$messages['idleaccountstitle'] = "Accounts idle for more than $idledays days";
$messages['label_authtimestamp'] = "Last authentication";
$messages['label_businesscategory'] = "Business category";
$messages['label_carlicense'] = "Car license";
$messages['label_description'] = "Description";
$messages['label_displayname'] = "Display name";
$messages['label_employeenumber'] = "Employee number";
$messages['label_employeetype'] = "Employee type";
$messages['label_expirationdate'] = "Expiration date";
$messages['label_fax'] = "Fax";
$messages['label_firstname'] = "First name";
$messages['label_fullname'] = "Full name";
$messages['label_identifier'] = "Identifier";
$messages['label_l'] = "Locality";
$messages['label_lastname'] = "Last name";
$messages['label_mail'] = "Mail";
$messages['label_mailquota'] = "Mail quota";
$messages['label_mobile'] = "Mobile";
$messages['label_organization'] = "Organization";
$messages['label_organizationalunit'] = "Organizational Unit";
$messages['label_pager'] = "Pager";
$messages['label_phone'] = "Phone";
$messages['label_postaladdress'] = "Address";
$messages['label_postalcode'] = "Postal code";
$messages['label_pwdaccountlockedtime'] = "Locking date";
$messages['label_pwdchangedtime'] = "Last password change";
$messages['label_pwdfailuretime'] = "Last authentication failures";
$messages['label_pwdreset'] = "Reset password at next connecion";
$messages['label_state'] = "State";
$messages['label_street'] = "Street";
$messages['label_title'] = "Title";
$messages['ldaperror'] = "LDAP communication error";
$messages['lockaccount'] = "Lock account";
$messages['lockedaccounts'] = "Locked accounts";
$messages['login'] = "Please login to continue";
$messages['logout'] = "Logout";
$messages['newpassword'] = "New password";
$messages['noentriesfound'] = "No entries found";
$messages['notdefined'] = "Not defined";
$messages['pager_all'] = "All";
$messages['password'] = "Password";
$messages['passwordchanged'] = "Password changed";
$messages['passwordexpired'] = "Password is expired";
$messages['passwordinvalid'] = "Authentication has failed";
$messages['passwordok'] = "Authentication succeeds!";
$messages['passwordrefused'] = "Password is incorrect";
$messages['passwordrequired'] = "Please enter a password";
$messages['print_all'] = "Print all results";
$messages['print_page'] = "Print this page";
$messages['resetpassword'] = "Reset password";
$messages['search'] = "Search";
$messages['searchrequired'] = "Please enter your search";
$messages['sizelimit'] = "Size limit has been reached, some entries could not be displayed";
$messages['submit'] = "Submit";
$messages['title'] = "Service Desk";
$messages['title_search'] = "Search results:";
$messages['tooltip_emailto'] = "Send an email";
$messages['tooltip_phoneto'] = "Dial this number";
$messages['true'] = "Yes";
$messages['unlockaccount'] = "Unlock account";
$messages['unlockdate'] = "Automatic unlock date:";
$messages['username'] = "Username";
$messages['usernamerequired'] = "Please enter a username";
$messages['usernotallowed'] = "Your account is not allowed to login. Please use an authorized account.";
$messages['usernotfound'] = "The entered username was not found. Please try again.";
$messages['welcome'] = "Welcome to LDAP Tool Box service desk";
$messages['welcomeuser'] = "Welcome, $displayname";
$messages['willexpireaccounts'] = "Passwords soon expired";
$messages['willexpireaccountstitle'] = "Passwords that will expire within $willexpiredays days";

?>

<?php

# Notify administrators
function notify_admin_by_mail($mail_from, $mail_from_name, $changesubject, $changemessage, $mail_signature, $data)
{
    global $notify_admin_by_mail;
    global $notify_admin_by_mail_list;

    $admin_mail_list=array();

    if (isset($notify_admin_by_mail_list))
    {
        $admin_mail_list=$notify_admin_by_mail_list;
    }

    if (isset($notify_admin_by_mail))
    {
        // don't sent twice the mail if admin already in list
        if ( ! in_array($notify_admin_by_mail,$admin_mail_list,true))
        {
            array_unshift($admin_mail_list,$notify_admin_by_mail);
        }
    }

    if (! empty($admin_mail_list))
    {
        if ( !\Ltb\Mail::send_mail_global($admin_mail_list, $mail_from, $mail_from_name, $changesubject, $changemessage.$mail_signature, $data) ) {
            error_log("Error while sending email to administrators $admin_mail_list");
        }
    }

}

?>

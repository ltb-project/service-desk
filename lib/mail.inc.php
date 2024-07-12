<?php

# Get administrators mail list but remove duplicates
function get_admin_mail_list($notify_admin_by_mail, $notify_admin_by_mail_list)
{

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

    return $admin_mail_list;
}

?>

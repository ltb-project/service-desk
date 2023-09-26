<?php
/*
 * Search idle entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';

require_once("../conf/sqlrequest/config.inc.php");

$dbtable='mailboxes';

$dburl='pgsql:host=ubphi;port=5432;dbname=sdplugin';
$dbuser='sdplugin';
$dbpass='guessmeitsobvious';

$dbh = new PDO($dburl, $dbuser, $dbpass);
// use the connection here
$sth = $dbh->query('SELECT uid,mail,allocated_storage_mail,used_storage_mail FROM '.$dbtable);
// $sth = $dbh->query('SELECT * FROM '.$dbtable);

$entries=array();

// implicit first fullname (don't ask me why yet
// ,'json'
$columns=array('identifier','mail', 'quota','used','percent_used');

// could be but : types ( int -> string ) and percent ( function )
$db_attribute_map=array('identifier'=>array('attribute'=>'identifier','sql'=>'uid','type'=>'text'),
                        'quota'=>array('attribute'=>'quota','sql'=>'used_storage_mail','type'=>'bytes'),
                        'used'=>array('attribute'=>'used','sql'=>'allocated_storage_mail','type'=>'bytes'),
                        'mail'=>array('attribute'=>'mail','sql'=>'mail','type'=>'text'),
                        'percent_used'=>array('attribute'=>'percent_used','type'=>'percent','faclass'=>'pie-chart'));

// result are retrieves only on first iteration on $sth, need to have them as string.
foreach ($sth as $entry)
{
    // unit is Kio.
    $mailUsed=$entry['used_storage_mail'];
    $mailQuota=$entry['allocated_storage_mail'];
    if ($mailQuota>0)
    {
        $percent_used=((100*$mailUsed)/$mailQuota);
    }
    array_push($entries,array('identifier'=>$entry['uid'],
                              'quota'=>''.$mailQuota * 1024,
                              'used'=>''.$mailUsed * 1024,
                              'percent_used'=>' '.str_pad(number_format($percent_used,2), 5, '0', STR_PAD_LEFT),
                              'mail'=>$entry['mail']));
}

$nb_entries=count($entries);
$size_limit_reached=false;

$search_result_sortby='percent_used';
$search_result_title='percent_used';

if ( ! empty($entries) )
{
                $smarty->assign("page_title", "sqlrequesttitle");
                $smarty->assign("nb_entries", $nb_entries);
                $smarty->assign("entries", $entries);
                $smarty->assign("size_limit_reached", $size_limit_reached);
                $smarty->assign("attributes_map", $db_attribute_map);
//                if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_iresult_title);
                $smarty->assign("listing_columns", $columns);
//                $smarty->assign("listing_linkto",  isset($search_result_linkto) ? $search_result_linkto : array($search_result_title));
                $smarty->assign("listing_sortby",  array_search($search_result_sortby, $columns));
//                $smarty->assign("show_undef", $search_result_show_undefined);
                $smarty->assign("truncate_value_after", $search_result_truncate_value_after);
}

// and now we're done; close it
$sth = null;
$dbh = null;

?>

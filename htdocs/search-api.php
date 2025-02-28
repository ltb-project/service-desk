<?php
/*
 * Search disabled entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';


# Get search parameters from request
$datatables_params = array(
    "columns" => null,
    "draw"    => null,
    "start"   => null,
    "length"  => null,
    "order"   => null,
    "search"  => null
);
foreach ($datatables_params as $key => $value) {
    if (isset($_REQUEST[$key]))
    {
        $datatables_params[$key] = $_REQUEST[$key];
    }
    else
    {
        error_log("Missing parameter: $key");
        exit(1);
    }
}


[$ldap,$result,$nb_entries,$entries,$size_limit_reached] = $ldapInstance->search($ldap_user_filter, array(), $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

$ldapInstance->ldapSort($entries, $attributes_map['identifier']['attribute']);

$entries = array_slice( $entries,
                        (intval($datatables_params["length"]) * intval($datatables_params["start"])),
                        intval($datatables_params["length"]) );


# Format data to send
$data = array();
# TODO: get this list from configuration
$attribute_list = array("cn", "uid", "mail", "mobile", "sn");
$i=0;
foreach ($entries as $entry)
{
    $data[$i] = array();
    array_push( $data[$i], $entry["dn"] );
    foreach ($attribute_list as $attr)
    {
        $val = $entry[$attr][0] ?? "";
        array_push( $data[$i], $val );
    }
    $i++;
}

echo json_encode(
    array(
        "draw" => 1,
        "recordsTotal" => $nb_entries,
        "recordsFiltered" => $datatables_params["length"],
        "data" => $data
    )
);

exit(0);

?>

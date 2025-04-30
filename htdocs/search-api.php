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
                        intval($datatables_params["start"]),
                        intval($datatables_params["length"]) );


# Format data to send
$data = array();

# Get columns labels
$columns = $search_result_items;
if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);

# Get attribute list from columns: attr => type
$attribute_list = [];
foreach( $columns as $column ) {
    $attribute_list[$attributes_map[$column]['attribute']] = $attributes_map[$column]['type'];
}

$i=0;
foreach ($entries as $entry)
{
    $data[$i] = array();
    # Always push DN as first value of the entry
    array_push( $data[$i], $entry["dn"] );
    foreach ($attribute_list as $attr => $type)
    {
        $values = [];
        foreach ($entry[$attr] as $j => $value) {
            if($j != "count") {

                # If this is a DN, we search for the corresponding cn
                if( $type == "dn_link" || $type == "ppolicy_dn" )
                {
                    $dn = $value;
                    // Get cn of corresponding link
                    $cn_vals = $ldapInstance->get_attribute_values($dn, "cn");
                    if( $cn_vals == false )
                    {
                        $cn = [];
                    }
                    else
                    {
                        $cn = [];
                        foreach ($cn_vals as $k => $cn_val) {
                            if($k != "count") {
                                array_push( $cn, $cn_val );
                            }
                        }
                    }
                    array_push( $values, [ $dn, $cn ] );
                }

                # If this is a standard list of values, just push it
                else
                {
                    array_push( $values, $value );
                }
            }
        }
        array_push( $data[$i], $values );
    }
    $i++;
}

echo json_encode(
    array(
        "draw" => $datatables_params["draw"],
        "recordsTotal" => $nb_entries,
        "recordsFiltered" => $nb_entries,
        "data" => $data
    )
);

exit(0);

?>

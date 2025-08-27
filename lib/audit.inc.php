<?php

function auditlog($file, $dn, $admin, $action, $result, $comment) {

  $log = array(
    "date" => date_format(date_create(), "D, d M Y H:i:s"),
    "ip" => $_SERVER['REMOTE_ADDR'],
    "user_dn" => $dn,
    "done_by" => $admin,
    "action" => $action,
    "result" => $result
  );

  if ($comment) {
    $log['comment'] = $comment;
  }

  file_put_contents($file, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function displayauditlog($audit_log_file, $audit_log_days, $audit_log_sortby, $audit_log_reverse, $ldapInstance) {

  $events = array();

  # Date calculation to limit oldest audit logs
  $olddatelog = new DateTime();
  date_sub( $olddatelog, new DateInterval('P'.$audit_log_days.'D') );

  foreach(file($audit_log_file) as $line) {
    $json = json_decode($line, true);
    $logdate = DateTimeImmutable::createFromFormat("D, d M Y H:i:s", $json['date']);
    if ($logdate > $olddatelog) {
      $json['date'] = date_format($logdate, "Y-m-d H:i:s");

      $dn = $json['user_dn'];
      $linked_attr = "cn";
      # Get linked_attr of corresponding link
      $ldapInstance->connect();
      $linked_attr_res = $ldapInstance->get_attribute_values($dn, $linked_attr);
      if( $linked_attr_res == false )
      {
          $json['user_dn_values'] = false;
      }
      else
      {
          $linked_attr_vals = [];
          foreach ($linked_attr_res as $k => $linked_attr_val) {
              if($k != "count") {
                  array_push( $linked_attr_vals, $linked_attr_val );
              }
          }
          $json['user_dn_values'] = base64_encode(json_encode([$dn,$linked_attr_vals]));
      }

      array_push($events, $json);
    }
  }

  # Sort audit log with sort key and normal/reverse order
  dateSort($events, $audit_log_sortby, $audit_log_reverse);

  $nb_events = sizeof($events);

  return [$events,$nb_events];
}

function dateSort(array &$events, $sortkey, $audit_log_reverse) {
  $reverse_order = fn($a, $b) => strtotime($b[$sortkey]) <=> strtotime($a[$sortkey]);
  $normal_order =  fn($a, $b) => strtotime($a[$sortkey]) <=> strtotime($b[$sortkey]);

  if ($audit_log_reverse) {
    usort($events, $reverse_order);
  }
  else {
    usort($events, $normal_order);
  }

  return true;
}

?>

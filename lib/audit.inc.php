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

function displayauditlog($audit_log_file, $audit_log_days, $audit_log_sortby, $audit_log_reverse) {

  $events = array();

  # Date calculation to limit oldest audit logs
  $olddatelog = new DateTime();
  date_sub( $olddatelog, new DateInterval('P'.$audit_log_days.'D') );

  foreach(file($audit_log_file) as $line) {
    $json = json_decode($line, true);
    $logdate = DateTimeImmutable::createFromFormat("D, d M Y H:i:s", $json['date']);
    if ($logdate > $olddatelog) {
      $json['date'] = date_format($logdate, "Y-m-d H:i:s");
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
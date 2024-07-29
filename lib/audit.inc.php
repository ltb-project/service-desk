<?php

function auditlog($file, $dn, $admin, $action, $result, $comment) {

  $log = array(
    "date" => date_format(date_create(), "D, d M Y H:i:s"),
    "ip" => $_SERVER['REMOTE_ADDR'],
    "dn" => $dn,
    "done_by" => $admin,
    "action" => $action,
    "result" => $result
  );

  if ($comment) {
    $log['comment'] = $comment;
  }

  file_put_contents($file, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function displayauditlog($audit_log_file) {

  $entries = array();

  foreach(file($audit_log_file) as $line) {
    $json = json_decode($line, true);
    array_push($entries, $json);
  }

  $nb_entries = sizeof($entries);

  return [$entries,$nb_entries];
}

?>
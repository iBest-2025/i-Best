
<?php
$to = $_GET["to"] ?? "";
$messages = [];

if (file_exists("messages.json")) {
  $all = json_decode(file_get_contents("messages.json"), true);
  foreach ($all as $msg) {
    if ($msg["to"] === $to) {
      $messages[] = $msg;
    }
  }
}
header("Content-Type: application/json; charset=utf-8");
echo json_encode($messages, JSON_UNESCAPED_UNICODE);
?>


<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"] ?? "";
  $message = $_POST["message"] ?? "";
  $to = $_POST["to"] ?? "";

  if ($username && $message && $to) {
    $data = [
      "username" => $username,
      "message" => $message,
      "to" => $to,
      "created_at" => date("Y-m-d H:i:s")
    ];

    $all = [];

    if (file_exists("messages.json")) {
      $all = json_decode(file_get_contents("messages.json"), true);
    }

    $all[] = $data;
    file_put_contents("messages.json", json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo "done";
  }
}
?>

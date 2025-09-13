<?php
$file = "words.json";
$words = json_decode(file_get_contents($file), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $en = trim($_POST["en"]);
    $vi = trim($_POST["vi"]);

    if ($en && $vi) {
        $words[] = ["en" => $en, "vi" => $vi];
        file_put_contents($file, json_encode($words, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "✅ Đã thêm từ: $en - $vi ";
    } else {
        $message = "❌ Vui lòng nhập đầy đủ!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thêm từ mới</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1>➕ Add Word</h1>
  <?php if (!empty($message)) echo "<p>$message</p>"; ?>
  <form method="POST">
    <input type="text" style="border-radius: 8px; font-size: 2rem;" name="en" placeholder="English word"><br><br>
    <input type="text" style="border-radius: 8px; font-size: 2rem;" name="vi" placeholder="Vietnamese meaning"><br><br>
    <button type="submit" style = "font-size: 1.2rem;">Add</button>
  </form>
  <a href="index.php">Back To Home</a>
</div>
</body>
</html>

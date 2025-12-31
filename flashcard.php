<?php
session_start();
$words = json_decode(file_get_contents("words.json"), true);

// Nếu chưa có danh sách random thì tạo mới
if (!isset($_SESSION['flash_shuffled']) || !isset($_SESSION['flash_index'])) {
    $_SESSION['flash_shuffled'] = $words;
    shuffle($_SESSION['flash_shuffled']);
    $_SESSION['flash_index'] = 0;
}

// Nếu bấm Next thì mới tăng index
if (isset($_GET['next'])) {
    $_SESSION['flash_index']++;
    // Nếu hết danh sách thì reset lại từ đầu
    if ($_SESSION['flash_index'] >= count($_SESSION['flash_shuffled'])) {
        shuffle($words); // tạo vòng mới
        $_SESSION['flash_shuffled'] = $words;
        $_SESSION['flash_index'] = 0;
    }
}

// Lấy từ hiện tại
$randomWord = $_SESSION['flash_shuffled'][$_SESSION['flash_index']];

// Kiểm tra có bấm "Show meaning" hay không
$showVi = isset($_GET['show']) && $_GET['show'] === 'vi';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📖 Flashcards</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1><a href="index.php">📖 Flashcard</a></h1>

  <!-- Thẻ hiển thị từ -->
  <div class="card" style="font-size: 3rem; margin: 20px 0;">
    <?php if ($showVi): ?>
      <?= $randomWord['vi'] ?>
    <?php else: ?>
      <?= $randomWord['en'] ?>
    <?php endif; ?>
  </div>

<!-- Nút Show/Hide -->
<?php if ($showVi): ?>
  <a href="flashcard.php">Hide</a>
<?php else: ?>
  <a href="flashcard.php?show=vi">Show Meaning</a>
<?php endif; ?>

<!-- Nút Next (thêm ?next=1 để tăng index) -->
<a href="flashcard.php?next=1">Next</a>

  <!-- Nút Back -->
  <a href="index.php">Back To Home</a>
</div>
</body>
</html>

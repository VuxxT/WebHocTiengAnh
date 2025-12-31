<?php
$file = "words.json";
$words = json_decode(file_get_contents($file), true);

// Xóa từ
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (isset($words[$id])) {
        unset($words[$id]);
        $words = array_values($words); // reset lại index
        file_put_contents($file, json_encode($words, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "🗑️ Đã xóa từ!";
    }
}

// Sửa từ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_id"])) {
    $id = (int)$_POST["edit_id"];
    $en = trim($_POST["en"]);
    $vi = trim($_POST["vi"]);

    if ($en && $vi) {
        $words[$id] = ["en" => $en, "vi" => $vi];
        file_put_contents($file, json_encode($words, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "✏️ Updated!";
    }
}

// ---------- PHÂN TRANG ---------- //
$total = count($words);          // Tổng số từ
$limit = 100;                      // Giới hạn 10 từ mỗi trang
$totalPages = ceil($total / $limit);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

// Xác định vị trí bắt đầu
$start = ($page - 1) * $limit;

// Lấy dữ liệu của trang hiện tại
$wordsPage = array_slice($words, $start, $limit, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quản lý từ vựng</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1><a href="index.php"> 📋 Manage Words </a></h1>
  <?php if (!empty($message)) echo "<p><b>$message</b></p>"; ?>
  
  <table border="1" cellpadding="10" cellspacing="0" style="margin:auto; border-collapse: collapse;">
    <tr>
      <th>#</th>
      <th>English</th>
      <th>Vietnamese</th>
      <th>IPA</th>
      <th>Action</th>
    </tr>
    <?php foreach ($wordsPage as $i => $w): ?>
    <tr>
      <td><?= $i ?></td>
      <td><?= htmlspecialchars($w['en']) ?></td>
      <td><?= htmlspecialchars($w['vi']) ?></td>
      <td><?= htmlspecialchars($w['ipa']) ?></td>
      <td>
        <!-- Form sửa -->
        <form method="POST" style="display:inline;">
          <input type="hidden" name="edit_id" value="<?= $i ?>">
          <input type="text" style="font-size: 1.2rem; margin-bottom: 5px;" name="en" value="<?= htmlspecialchars($w['en']) ?>" required>
          <input type="text" style="font-size: 1.2rem;" name="vi" value="<?= htmlspecialchars($w['vi']) ?>" required>
    </br>
          <button type="submit"  style="font-size: 1rem;">Save</button>
        </form>
        <!-- Nút xóa -->
        <a href="?delete=<?= $i ?>&page=<?= $page ?>" onclick="return confirm('Are you sure to delete this word?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  
  <!-- Hiển thị phân trang -->
  <div style="margin-top: 20px; text-align: center;">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
      <a href="?page=<?= $p ?>" style="margin: 0 5px; <?= $p == $page ? 'font-weight:bold;' : '' ?>">
        <?= $p ?>
      </a>
    <?php endfor; ?>
  </div>
  
  <br>
  <a href="index.php">⬅ Quay lại</a>
</div>
</body>
</html>

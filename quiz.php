<style> body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; } 
.container { background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; width: 90%; max-width: 450px; } 
h1 { background: #28a745; color: #fff; padding: 10px; border-radius: 10px; margin-bottom: 20px; font-size: 1.5rem; } 
h1 a { text-decoration: none; color: white; } 
p { font-size: 1.2rem; margin: 15px 0; } 
p b { font-size: 2.5rem; color: #333; } 
button { background: #007bff; color: white; border: none; padding: 12px 20px; margin: 8px 0; border-radius: 8px; font-size: 1rem; width: 100%; cursor: pointer; transition: all 0.3s ease; } 
button:hover { background: #0056b3; transform: scale(1.03); } 
#result { font-size: 1.2rem; margin-top: 15px; font-weight: bold; } 
#result.correct { color: #28a745; } 
#result.incorrect { color: #dc3545; } 
a.action { display: inline-block; background: #28a745; color: white; text-decoration: none; padding: 12px 20px; margin: 10px 5px; border-radius: 8px; transition: all 0.3s ease; font-size: 1rem; } 
a.action:hover { background: #1e7e34; } @media (max-width: 480px) { h1 { font-size: 1.2rem; } p b { font-size: 2rem; } button, a.action { font-size: 0.9rem; padding: 10px; } } </style>
<?php


session_start();
$words = json_decode(file_get_contents("words.json"), true);

// Nếu chưa có danh sách random thì tạo mới
if (!isset($_SESSION['quiz_shuffled'])) {
    $_SESSION['quiz_shuffled'] = $words;
    shuffle($_SESSION['quiz_shuffled']);
    $_SESSION['quiz_index'] = 0;
}

// Lấy từ hiện tại
$question = $_SESSION['quiz_shuffled'][$_SESSION['quiz_index']];
$correct = $question['vi'];

// Tăng index
$_SESSION['quiz_index']++;

// Nếu đã hết thì reset vòng mới
if ($_SESSION['quiz_index'] >= count($_SESSION['quiz_shuffled'])) {
    unset($_SESSION['quiz_shuffled']);
    unset($_SESSION['quiz_index']);
}

// Tạo 3 đáp án sai
$answers = [$correct];
while (count($answers) < 4) {
    $wrong = $words[array_rand($words)]['vi'];
    if (!in_array($wrong, $answers)) {
        $answers[] = $wrong;
    }
}
shuffle($answers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bài Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1><span class="logo">📑 Quiz</span></h1>

  <p>Word: <b style="font-size: 3rem;"><?= $question['en'] ?></b></p>

  <div class="answers">
    <?php foreach ($answers as $ans): ?>
      <button style="font-size: 1.2rem;" class="answer-btn" onclick="checkAnswer('<?= $ans ?>','<?= $correct ?>', this)">
        <?= $ans ?>
      </button>
    <?php endforeach; ?>
  </div>

  <p id="result"></p>

  <!-- Ban đầu ẩn -->
  <div class="actions">
    <a id="nextBtn" href="quiz.php" class="btn" style="display:none;">Next Question</a>
    <a href="index.php" class="btn">Back To Home</a>
  </div>
</div>

<script>
function checkAnswer(selected, correct, btn) {
  const result = document.getElementById("result");
  const nextBtn = document.getElementById("nextBtn");

  if (selected === correct) {
    result.innerHTML = "✅ Correct!";
    result.className = "correct";
    nextBtn.style.display = "inline-block";
    btn.classList.add("correct-btn");
  } else {
    result.innerHTML = "❌ Incorrect! Try again.";
    result.className = "incorrect";
    btn.classList.add("incorrect-btn");
  }
}
</script>
</body>
</html>

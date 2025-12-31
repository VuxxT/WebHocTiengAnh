<?php
session_start();
$words = json_decode(file_get_contents("words.json"), true);

// --- Tạo danh sách random nếu chưa có ---
if (!isset($_SESSION['quiz_shuffled']) || !isset($_SESSION['quiz_index'])) {
    $_SESSION['quiz_shuffled'] = $words;
    shuffle($_SESSION['quiz_shuffled']);
    $_SESSION['quiz_index'] = 0;
}

// --- Lấy từ hiện tại ---
$question = $_SESSION['quiz_shuffled'][$_SESSION['quiz_index']];
$correct = $question['vi'];

// --- Khi bấm "next" mới tăng index ---
if (isset($_GET['next'])) {
    $_SESSION['quiz_index']++;

    // --- Nếu đã học hết 100 từ thì tạo vòng mới ---
    if ($_SESSION['quiz_index'] >= count($_SESSION['quiz_shuffled'])) {
        shuffle($words);
        $_SESSION['quiz_shuffled'] = $words;
        $_SESSION['quiz_index'] = 0;
    }

    // Lấy lại từ mới sau khi next
    $question = $_SESSION['quiz_shuffled'][$_SESSION['quiz_index']];
    $correct = $question['vi'];
}

// --- Tạo 3 đáp án sai ---
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
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quiz Từ Vựng</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #fef3c7, #ecfccb); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
.container { background: #dfe2d1; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; width: 90%; max-width: 450px; }
h2 { color: #28a745; }
#timer { font-size: 3rem; margin: 20px 0; }
button { background: #268f3e; color: white; border: none; padding: 12px 20px; margin: 8px 0; border-radius: 8px; font-size: 1rem; width: 100%; cursor: pointer; transition: all 0.3s ease; }
button:hover { background: #0056b3; transform: scale(1.03); }
#result { font-size: 1.3rem; margin-top: 15px; font-weight: bold; }
.correct { color: #28a745; }
.incorrect { color: #dc3545; }
a { display: inline-block; margin-top: 15px; background: #28a745; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; }
a:hover { background: #1e7e34; }
.speak-btn {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: white;
  border: none;
  border-radius: 50%;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 10px rgba(245, 158, 11, 0.4);
  margin: 15px auto;
}

.speak-btn:hover {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  transform: scale(1.08);
  box-shadow: 0 6px 14px rgba(217, 119, 6, 0.5);
}

.speak-btn:active {
  transform: scale(0.95);
  box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
}

.speak-btn::after {
  content: "";
  position: absolute;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: rgba(251, 191, 36, 0.4);
  animation: pulse 1.8s infinite;
  z-index: -1;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 0.8;
  }
  70% {
    transform: scale(1.6);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 0;
  }
}

.progress {
  font-size: 1rem;
  color: #666;
  margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="container" style="border: 2px solid lightcyan;">
  <h2><a href="./index.php">🏠</a>📑 Quiz</h2>
  <!-- 
  <div class="progress">
    Câu <?= $_SESSION['quiz_index'] + 1 ?>/<?= count($_SESSION['quiz_shuffled']) ?>
  </div>
  -->

  <div id="timer">5</div>

  <p >
    Từ: <b style="font-size:2rem;" id="word"><?= htmlspecialchars($question['en']) ?></b>
    </br>
    <b style="font-size:2rem;" id="word"><?= htmlspecialchars($question['ipa']) ?></b>
    <button class="speak-btn" onclick="speakWord()">🔊</button>
  </p>

  <div class="answers">
    <?php foreach ($answers as $ans): ?>
      <button onclick="checkAnswer('<?= addslashes($ans) ?>','<?= addslashes($correct) ?>', this)">
        <?= htmlspecialchars($ans) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <p id="result"></p>
  <a href="quiz.php?next=1" id="nextBtn" style="display:none;">Câu Tiếp ➡️</a>
</div>

<script>
let timeLeft = 5;
const timerDisplay = document.getElementById("timer");
const result = document.getElementById("result");
const nextBtn = document.getElementById("nextBtn");
let answered = false;

// --- Đồng hồ đếm ngược ---
const countdown = setInterval(() => {
  if (timeLeft > 0) {
    timeLeft--;
    timerDisplay.textContent = timeLeft;
  } else {
    clearInterval(countdown);
    if (!answered) {
      result.textContent = "⏰ Hết giờ! Học lại từ này nhé!";
      result.className = "incorrect";
      setTimeout(() => {
        window.location.href = "quiz.php?next=1";
      }, 2000);
    }
  }
}, 1000);

// --- Kiểm tra đáp án ---
function checkAnswer(selected, correct, btn) {
  if (answered) return;
  answered = true;
  clearInterval(countdown);

  if (selected === correct) {
    result.innerHTML = "✅ Chính xác!";
    result.className = "correct";
    btn.style.backgroundColor = "#28a745";
    setTimeout(() => window.location.href = "quiz.php?next=1", 400);
  } else {
    result.innerHTML = "❌ Sai rồi! Thử lại nhé.";
    result.className = "incorrect";
    btn.style.backgroundColor = "#dc3545";
    nextBtn.style.display = "inline-block";
  }
}

// --- Phát âm từ ---
function speakWord() {
  const word = document.getElementById("word").innerText;
  const utterance = new SpeechSynthesisUtterance(word);
  utterance.lang = "en-GB";
  utterance.rate = 0.9;
  utterance.pitch = 1;
  speechSynthesis.speak(utterance);
}

// Tự động phát âm khi hiển thị từ mới

  window.addEventListener("load", speakWord);
</script>
</body>
</html>

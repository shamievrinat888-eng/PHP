<?php
const UNIVERSITY = "Алматинский технологический университет";
const DISCIPLINE = "Программирование на PHP";

$studentName = "Шамиев Ринат";
$group = "ИС-24-22";
$course = 3;

// Оценки за лабораторные
$grade1 = 85;
$grade2 = 90;
$grade3 = 78;

$result = ($grade1 + $grade2 + $grade3) / 3;

if ($result >= 50) {
    $status = "Дисциплина освоена";
    $statusStyle = "status-success";
} else {
    $status = "Необходимо повысить результат";
    $statusStyle = "status-warning";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Карточка студента</title>
    <style>
        :root {
            --primary: rgb(79, 70, 229);
            --card-bg: rgb(255, 255, 255);
            --text-main: rgb(30, 41, 59);
            --text-muted: rgb(100, 116, 139);
            --border: rgb(226, 232, 240);
            --bg: rgb(241, 245, 249);
        }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, rgb(241, 245, 249) 0%, rgb(203, 213, 225) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        .card-header {
            background: var(--primary);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .card-header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; }
        .card-header h2 { margin: 5px 0 0; font-size: 1rem; font-weight: 400; opacity: 0.9; }
        .card-body { padding: 30px; }
        .student-info {
            display: flex; flex-direction: column; gap: 10px;
            margin-bottom: 25px; border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }
        .info-row { display: flex; justify-content: space-between; font-size: 0.95rem; }
        .info-label { color: var(--text-muted); }
        .info-value { color: var(--text-main); font-weight: 500; }
        .name-value { font-size: 1.1rem; font-weight: 700; color: var(--primary); }
        .grades { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .grade-box {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px; padding: 10px;
            text-align: center; flex: 1; margin: 0 5px;
        }
        .grade-box span { display: block; font-size: 0.8rem; color: var(--text-muted); }
        .grade-box strong { display: block; font-size: 1.2rem; color: var(--text-main); margin-top: 5px; }
        .result-section {
            text-align: center; background: var(--bg);
            border-radius: 15px; padding: 20px;
        }
        .result-score {
            font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block; padding: 8px 16px;
            border-radius: 20px; font-size: 0.85rem; font-weight: 600;
        }
        .status-success { background: rgb(220, 252, 231); color: rgb(22, 101, 52); }
        .status-warning { background: rgb(254, 226, 226); color: rgb(153, 27, 27); }
        .footer {
            text-align: center; padding: 15px; font-size: 0.8rem;
            color: var(--text-muted); background: var(--bg);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1><?= UNIVERSITY ?></h1>
            <h2><?= DISCIPLINE ?></h2>
        </div>
        
        <div class="card-body">
            <div class="student-info">
                <div class="info-row">
                    <span class="info-label">Студент:</span>
                    <span class="info-value name-value"><?= $studentName ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Группа:</span>
                    <span class="info-value"><?= $group ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Курс:</span>
                    <span class="info-value"><?= $course ?></span>
                </div>
            </div>

            <div class="grades">
                <div class="grade-box">
                    <span>Оценка 1</span>
                    <strong><?= $grade1 ?></strong>
                </div>
                <div class="grade-box">
                    <span>Оценка 2</span>
                    <strong><?= $grade2 ?></strong>
                </div>
                <div class="grade-box">
                    <span>Оценка 3</span>
                    <strong><?= $grade3 ?></strong>
                </div>
            </div>

            <div class="result-section">
                <div class="info-label">Средний балл</div>
                <div class="result-score"><?= round($result, 2) ?></div>
                <div class="status-badge <?= $statusStyle ?>">
                    <?= $status ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Сформировано: <?= date("d.m.Y") ?>
        </div>
    </div>
</body>
</html>
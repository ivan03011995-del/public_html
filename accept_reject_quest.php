<?php
// accept_reject_quest.php
require 'db.php';
session_start();

// Проверяем, авторизован ли пользователь
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die('Ошибка: пользователь не авторизован.');
}

// Получаем параметры NPC и диалога
$npc_id = $_GET['npc_id'] ?? null;
$dialog_key = $_GET['dialog_key'] ?? null;

if (!$npc_id || !$dialog_key) {
    die('Ошибка: недостающие параметры.');
}

// Получаем диалог по ключу
$stmt = $pdo->prepare('SELECT * FROM dialogs WHERE npc_id = ? AND dialog_key = ?');
$stmt->execute([$npc_id, $dialog_key]);
$dialog = $stmt->fetch();

if (!$dialog) {
    die('Диалог не найден.');
}

// Обрабатываем ответ игрока
$response = $_GET['response'] ?? null;
if ($response) {
    // accept_reject_quest.php
// Проверяем, какой квест привязан к выбранному ответу
    $quest_column = 'quest_' . $response;
    $quest_id = $dialog[$quest_column] ?? null;

    if ($quest_id) {
        // Проверяем, не принят ли уже квест
        $stmt = $pdo->prepare('SELECT * FROM user_quests WHERE user_id = ? AND quest_id = ?');
        $stmt->execute([$user_id, $quest_id]);
        $existingQuest = $stmt->fetch();
        if (!$existingQuest) {
            // Добавляем квест в таблицу user_quests
            $stmt = $pdo->prepare('INSERT INTO user_quests (user_id, quest_id, status) VALUES (?, ?, "accepted")');
            $stmt->execute([$user_id, $quest_id]);

            // Обновляем текущий квест пользователя в таблице users
            $stmt = $pdo->prepare('UPDATE users SET current_quest_id = ? WHERE id = ?');
            $stmt->execute([$quest_id, $user_id]);

            echo 'Квест принят!<br>';
        } else {
            echo 'Вы уже приняли этот квест.<br>';
        }
    }


    // Переход к следующему диалогу
    $next_dialog_key = $dialog['next_' . $response];
    if ($next_dialog_key) {
        // Получаем следующий диалог по ключу
        $stmt = $pdo->prepare('SELECT * FROM dialogs WHERE npc_id = ? AND dialog_key = ?');
        $stmt->execute([$npc_id, $next_dialog_key]);
        $next_dialog = $stmt->fetch();

        if ($next_dialog) {
            echo '<h3>' . htmlspecialchars($next_dialog['text']) . '</h3>';
            for ($i = 1; $i <= 4; $i++) {
                if ($next_dialog['response_' . $i]) {
                    echo '<a href="?npc_id=' . $npc_id . '&dialog_key=' . $next_dialog_key . '&response=' . $i . '">' . htmlspecialchars($next_dialog['response_' . $i]) . '</a><br>';
                }
            }
        } else {
            echo 'Диалог завершён.<br>';
        }
    } else {
        echo 'Конец диалога.<br>';
    }
} else {
    // Если нет ответа, выводим варианты ответов
    echo '<h3>' . htmlspecialchars($dialog['text']) . '</h3>';
    for ($i = 1; $i <= 4; $i++) {
        if ($dialog['response_' . $i]) {
            echo '<a href="?npc_id=' . $npc_id . '&dialog_key=' . $dialog_key . '&response=' . $i . '">' . htmlspecialchars($dialog['response_' . $i]) . '</a><br>';
        }
    }
}
?>

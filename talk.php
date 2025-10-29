<?php
// talk.php
require 'db.php';
session_start();

// Проверка на наличие NPC ID в GET-запросе
if (!isset($_GET['npc_id'])) {
    die("NPC не найден.");
}

$npc_id = (int)$_GET['npc_id'];

// Получаем данные NPC из базы
$stmt = $pdo->prepare("SELECT * FROM npc WHERE id = ?");
$stmt->execute([$npc_id]);
$npc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$npc) {
    die("NPC не найден.");
}

// Получаем ключ диалога из GET-запроса
$dialog_key = $_GET['dialog'] ?? 'старт';

// Получаем диалог по ключу
$stmt = $pdo->prepare('SELECT * FROM dialogs WHERE npc_id = ? AND dialog_key = ?');
$stmt->execute([$npc['id'], $dialog_key]);
$dialog = $stmt->fetch(PDO::FETCH_ASSOC);

// Переход на страницу торговли или других действий
if ($dialog_key == 'торг') {
    header('Location: trade.php?npc_id=' . $npc['id']);
    exit;
} elseif ($dialog_key == 'все') {
    header('Location: game.php');
    exit;
} elseif ($dialog_key == 'купить') {
    header('Location: buy.php?npc_id=' . $npc['id']);
    exit;
} elseif ($dialog_key == 'квест') {
    header('Location: accept_reject_quest.php?npc_id=' . $npc['id'] . '&dialog_key=' . $dialog_key);
    exit;
}

if ($dialog_key == 'врач') {
    // Логика восстановления здоровья игрока
    // (код для восстановления здоровья здесь)
}

// Получаем предыдущий диалог
$prev_dialog = $dialog['prev_key'] ?? 'старт';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styles.css">
    <title>Диалог с NPC - <?= htmlspecialchars($npc['name']) ?></title>
</head>
<body>
<h1><?= htmlspecialchars($npc['name']) ?></h1>

<?php if ($dialog): ?>
    <h2> <?= htmlspecialchars($dialog['text']) ?></h2>

    <!-- Форма для выбора ответа -->
    <form action="talk.php" method="get">
        <input type="hidden" name="npc_id" value="<?= $npc['id'] ?>"><br><hr>

        <?php if (!empty($dialog['response_1']) && !empty($dialog['next_1'])): ?>
            <button type="submit" name="dialog" value="<?= htmlspecialchars($dialog['next_1']) ?>">
                <?= htmlspecialchars($dialog['response_1']) ?>
            </button><br><br>
        <?php endif; ?>

        <?php if (!empty($dialog['response_2']) && !empty($dialog['next_2'])): ?>
            <button type="submit" name="dialog" value="<?= htmlspecialchars($dialog['next_2']) ?>">
                <?= htmlspecialchars($dialog['response_2']) ?>
            </button><br><br>
        <?php endif; ?>

        <?php if (!empty($dialog['response_3']) && !empty($dialog['next_3'])): ?>
            <button type="submit" name="dialog" value="<?= htmlspecialchars($dialog['next_3']) ?>">
                <?= htmlspecialchars($dialog['response_3']) ?>
            </button><br><br>
        <?php endif; ?>

        <?php if (!empty($dialog['response_4']) && !empty($dialog['next_4'])): ?>
            <button type="submit" name="dialog" value="<?= htmlspecialchars($dialog['next_4']) ?>">
                <?= htmlspecialchars($dialog['response_4']) ?>
            </button><br>
        <?php endif; ?>
    </form><br><br><hr>
<?php else: ?>
    <p>Диалог завершён.</p>
<?php endif; ?>

<!-- Кнопка "Назад", возвращающая к предыдущему диалогу -->
<form action="talk.php" method="get"><br><br>
    <input type="hidden" name="npc_id" value="<?= $npc['id'] ?>">
    <input type="hidden" name="dialog" value="<?= htmlspecialchars($prev_dialog) ?>">
    <button type="submit">Назад</button>
</form>
<form action="game.php" method="get"><br><br>
    <button type="submit">В игру</button>
</form>
</body>
</html>

<?php
function запрос($query, $params = [], $pdo, $fetch = false, $fetchAll = false) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        if ($fetchAll) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($fetch) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $stmt->rowCount();
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
}

// SELECT: Получить одну запись по условию
function получить($таблица, $условие = '1', $значения = [], $pdo = null) {
    $query = "SELECT * FROM `$таблица` WHERE $условие";
    return запрос($query, $значения, $pdo, true);
}

// SELECT: Получить все записи
function получитьВсе($таблица, $условие = '', $значения = [], $pdo = null) {
    $query = "SELECT * FROM `$таблица`" . ($условие ? " WHERE $условие" : "");
    return запрос($query, $значения, $pdo, false, true);
}

// INSERT: Вставка записи
function вставить($таблица, $поля = [], $значения = [], $pdo = null) {
    if (empty($поля) || empty($значения)) return false;

    $плейсхолдеры = implode(', ', array_fill(0, count($значения), '?'));
    $поляСтрокой = implode(', ', $поля);
    $query = "INSERT INTO `$таблица` ($поляСтрокой) VALUES ($плейсхолдеры)";
    return запрос($query, $значения, $pdo);
}

// UPDATE: Обновление записи
function обновить($таблица, $поля, $значения, $условие, $условияЗначения, $pdo) {
    // Приведение $поля и $значения к массиву, если передано строкой
    if (!is_array($поля)) {
        $поля = [$поля];
    }

    if (!is_array($значения)) {
        $значения = [$значения];
    }

    if (!is_array($условияЗначения)) {
        $условияЗначения = [$условияЗначения];
    }

    // Проверка на соответствие количества полей и значений
    if (count($поля) !== count($значения)) {
        die("Количество полей и значений не совпадает в обновить()");
    }

    $установки = implode(', ', array_map(fn($поле) => "`$поле` = ?", $поля));
    $query = "UPDATE `$таблица` SET $установки WHERE $условие";
    return запрос($query, array_merge($значения, $условияЗначения), $pdo);
}


// DELETE: Удаление
function удалить($таблица, $условие = '1', $значения = [], $pdo = null) {
    $query = "DELETE FROM `$таблица` WHERE $условие";
    return запрос($query, $значения, $pdo);
}

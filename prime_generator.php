<?php
// prime_generator.php - Генератор простых чисел на PHP (CLI + веб)
// CLI: php prime_generator.php --max 100 --stats

function sieveOfEratosthenes($n) {
    if ($n < 2) return [];
    $isPrime = array_fill(2, $n - 1, true);
    $limit = (int)sqrt($n);
    for ($i = 2; $i <= $limit; $i++) {
        if ($isPrime[$i]) {
            for ($j = $i * $i; $j <= $n; $j += $i) {
                $isPrime[$j] = false;
            }
        }
    }
    $primes = [];
    for ($i = 2; $i <= $n; $i++) {
        if ($isPrime[$i]) $primes[] = $i;
    }
    return $primes;
}

function isPrimeTrial($n) {
    if ($n < 2) return false;
    if ($n == 2) return true;
    if ($n % 2 == 0) return false;
    $limit = (int)sqrt($n);
    for ($i = 3; $i <= $limit; $i += 2) {
        if ($n % $i == 0) return false;
    }
    return true;
}

function primesInRange($a, $b, $method = 'sieve') {
    if ($a < 2) $a = 2;
    if ($a > $b) return [];
    if ($method == 'sieve') {
        $all = sieveOfEratosthenes($b);
        return array_filter($all, function($p) use ($a) { return $p >= $a; });
    } else {
        $res = [];
        for ($p = $a; $p <= $b; $p++) {
            if (isPrimeTrial($p)) $res[] = $p;
        }
        return $res;
    }
}

function stats($primes) {
    $count = count($primes);
    if ($count == 0) return ['count' => 0, 'sum' => 0, 'avg' => 0, 'min' => 0, 'max' => 0];
    $sum = array_sum($primes);
    return [
        'count' => $count,
        'sum' => $sum,
        'avg' => $sum / $count,
        'min' => min($primes),
        'max' => max($primes)
    ];
}

function exportToFile($filename, $primes) {
    file_put_contents($filename, implode("\n", $primes));
}

// CLI
if (php_sapi_name() === 'cli') {
    $options = getopt("", ["max:", "min:", "method:", "export:", "stats"]);
    if (isset($options['max'])) {
        $max = (int)$options['max'];
        $min = isset($options['min']) ? (int)$options['min'] : 2;
        $method = isset($options['method']) ? $options['method'] : 'sieve';
        if (!in_array($method, ['sieve','trial'])) $method = 'sieve';
        $start = microtime(true);
        if ($method == 'sieve') {
            $primes = sieveOfEratosthenes($max);
        } else {
            $primes = primesInRange($min, $max, $method);
        }
        $elapsed = microtime(true) - $start;
        if (isset($options['stats'])) {
            $st = stats($primes);
            echo "Количество: {$st['count']}, Сумма: {$st['sum']}, Среднее: " . number_format($st['avg'], 2) . ", Мин: {$st['min']}, Макс: {$st['max']}\n";
            echo "Время: " . number_format($elapsed, 4) . " сек.\n";
        } else {
            echo implode(", ", $primes) . "\n";
        }
        if (isset($options['export'])) {
            exportToFile($options['export'], $primes);
            echo "Экспортировано в {$options['export']}\n";
        }
    } else {
        // Интерактивный режим
        while (true) {
            echo "\n🔢 Генератор простых чисел (интерактивный)\n";
            echo "1. Сгенерировать простые числа до N\n";
            echo "2. Сгенерировать простые числа в диапазоне [A, B]\n";
            echo "3. Проверить число на простоту\n";
            echo "4. Выход\n";
            echo "Ваш выбор: ";
            $choice = trim(fgets(STDIN));
            if ($choice == '4') break;
            elseif ($choice == '1') {
                echo "Введите N: ";
                $n = (int)trim(fgets(STDIN));
                $start = microtime(true);
                $primes = sieveOfEratosthenes($n);
                $elapsed = microtime(true) - $start;
                $st = stats($primes);
                echo "Простых чисел: {$st['count']}, Сумма: {$st['sum']}, Среднее: " . number_format($st['avg'], 2) . ", Время: " . number_format($elapsed, 4) . " сек.\n";
                echo "Показать список? (y/n): ";
                if (trim(fgets(STDIN)) == 'y') echo implode(", ", $primes) . "\n";
                echo "Экспортировать в файл? (y/n): ";
                if (trim(fgets(STDIN)) == 'y') {
                    echo "Имя файла: ";
                    $fname = trim(fgets(STDIN));
                    if (empty($fname)) $fname = 'primes.txt';
                    exportToFile($fname, $primes);
                    echo "Сохранено в $fname\n";
                }
            } elseif ($choice == '2') {
                echo "Введите A: ";
                $a = (int)trim(fgets(STDIN));
                echo "Введите B: ";
                $b = (int)trim(fgets(STDIN));
                echo "Метод (sieve/trial): ";
                $method = trim(fgets(STDIN));
                if (!in_array($method, ['sieve','trial'])) $method = 'sieve';
                $start = microtime(true);
                $primes = primesInRange($a, $b, $method);
                $elapsed = microtime(true) - $start;
                $st = stats($primes);
                echo "Простых чисел: {$st['count']}, Сумма: {$st['sum']}, Среднее: " . number_format($st['avg'], 2) . ", Время: " . number_format($elapsed, 4) . " сек.\n";
                echo "Показать список? (y/n): ";
                if (trim(fgets(STDIN)) == 'y') echo implode(", ", $primes) . "\n";
                echo "Экспортировать в файл? (y/n): ";
                if (trim(fgets(STDIN)) == 'y') {
                    echo "Имя файла: ";
                    $fname = trim(fgets(STDIN));
                    if (empty($fname)) $fname = 'primes.txt';
                    exportToFile($fname, $primes);
                    echo "Сохранено в $fname\n";
                }
            } elseif ($choice == '3') {
                echo "Введите число: ";
                $num = (int)trim(fgets(STDIN));
                $isPrime = isPrimeTrial($num);
                echo "$num " . ($isPrime ? "является" : "не является") . " простым.\n";
            } else {
                echo "Неверный выбор.\n";
            }
        }
    }
    exit;
}

// Веб-интерфейс
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🔢 Генератор простых чисел (PHP)</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fb; margin: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 120px; }
        input, select, button { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #3498db; color: white; border: none; cursor: pointer; padding: 6px 20px; }
        button:hover { background: #2980b9; }
        .result { background: #ecf0f1; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .stats { font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔢 Генератор простых чисел (PHP)</h1>
    <form method="GET">
        <div class="form-group">
            <label>Верхняя граница N:</label>
            <input type="number" name="max" value="<?= isset($_GET['max']) ? $_GET['max'] : 100 ?>" required>
        </div>
        <div class="form-group">
            <label>Нижняя граница:</label>
            <input type="number" name="min" value="<?= isset($_GET['min']) ? $_GET['min'] : 2 ?>">
        </div>
        <div class="form-group">
            <label>Метод:</label>
            <select name="method">
                <option value="sieve" <?= isset($_GET['method']) && $_GET['method']=='sieve' ? 'selected' : '' ?>>Решето Эратосфена</option>
                <option value="trial" <?= isset($_GET['method']) && $_GET['method']=='trial' ? 'selected' : '' ?>>Перебор делителей</option>
            </select>
        </div>
        <div class="form-group">
            <label>Показать статистику:</label>
            <input type="checkbox" name="stats" value="1" <?= isset($_GET['stats']) ? 'checked' : '' ?>>
        </div>
        <button type="submit">Сгенерировать</button>
    </form>

    <?php if (isset($_GET['max'])): 
        $max = (int)$_GET['max'];
        $min = isset($_GET['min']) ? (int)$_GET['min'] : 2;
        $method = isset($_GET['method']) ? $_GET['method'] : 'sieve';
        $showStats = isset($_GET['stats']);
        if (!in_array($method, ['sieve','trial'])) $method = 'sieve';
        $start = microtime(true);
        if ($method == 'sieve') {
            $primes = sieveOfEratosthenes($max);
        } else {
            $primes = primesInRange($min, $max, $method);
        }
        $elapsed = microtime(true) - $start;
        $st = stats($primes);
    ?>
        <div class="result">
            <h3>Результат</h3>
            <?php if ($showStats): ?>
                <div class="stats">
                    <p>Количество: <?= $st['count'] ?></p>
                    <p>Сумма: <?= $st['sum'] ?></p>
                    <p>Среднее: <?= number_format($st['avg'], 2) ?></p>
                    <p>Минимальное: <?= $st['min'] ?></p>
                    <p>Максимальное: <?= $st['max'] ?></p>
                    <p>Время выполнения: <?= number_format($elapsed, 4) ?> сек.</p>
                </div>
            <?php else: ?>
                <p><?= implode(', ', $primes) ?></p>
            <?php endif; ?>
            <?php if (!empty($primes)): ?>
                <p><a href="?export=1&max=<?= $max ?>&min=<?= $min ?>&method=<?= $method ?>" download="primes.txt">📥 Скачать как TXT</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

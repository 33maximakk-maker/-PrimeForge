#!/usr/bin/env node
/**
 * prime_generator.js - Генератор простых чисел на JavaScript (Node.js CLI)
 */
const fs = require('fs');
const readline = require('readline');
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

function sieveOfEratosthenes(n) {
    if (n < 2) return [];
    const isPrime = new Array(n + 1).fill(true);
    isPrime[0] = isPrime[1] = false;
    for (let i = 2; i * i <= n; i++) {
        if (isPrime[i]) {
            for (let j = i * i; j <= n; j += i) {
                isPrime[j] = false;
            }
        }
    }
    const primes = [];
    for (let i = 2; i <= n; i++) {
        if (isPrime[i]) primes.push(i);
    }
    return primes;
}

function isPrimeTrial(n) {
    if (n < 2) return false;
    if (n === 2) return true;
    if (n % 2 === 0) return false;
    for (let i = 3; i * i <= n; i += 2) {
        if (n % i === 0) return false;
    }
    return true;
}

function primesInRange(a, b, method) {
    if (a < 2) a = 2;
    if (a > b) return [];
    if (method === 'sieve') {
        const all = sieveOfEratosthenes(b);
        return all.filter(p => p >= a);
    } else {
        const result = [];
        for (let p = a; p <= b; p++) {
            if (isPrimeTrial(p)) result.push(p);
        }
        return result;
    }
}

function stats(primes) {
    if (!primes.length) return { count: 0, sum: 0, avg: 0, min: 0, max: 0 };
    const sum = primes.reduce((a, b) => a + b, 0);
    return {
        count: primes.length,
        sum: sum,
        avg: sum / primes.length,
        min: Math.min(...primes),
        max: Math.max(...primes)
    };
}

function exportToFile(filename, primes) {
    fs.writeFileSync(filename, primes.join('\n'), 'utf8');
}

function prompt(query) {
    return new Promise(resolve => rl.question(query, resolve));
}

async function interactive() {
    console.log("🔢 Генератор простых чисел (интерактивный)");
    while (true) {
        console.log("\nВыберите действие:");
        console.log("1. Сгенерировать простые числа до N");
        console.log("2. Сгенерировать простые числа в диапазоне [A, B]");
        console.log("3. Проверить число на простоту");
        console.log("4. Выход");
        const choice = await prompt("Ваш выбор: ");
        if (choice === '4') break;
        else if (choice === '1') {
            const n = parseInt(await prompt("Введите N: "));
            const start = Date.now();
            const primes = sieveOfEratosthenes(n);
            const elapsed = (Date.now() - start) / 1000;
            const st = stats(primes);
            console.log(`Простых чисел: ${st.count}, Сумма: ${st.sum}, Среднее: ${st.avg.toFixed(2)}, Время: ${elapsed.toFixed(4)} сек.`);
            const show = await prompt("Показать список? (y/n): ");
            if (show.toLowerCase() === 'y') console.log(primes);
            const exp = await prompt("Экспортировать в файл? (y/n): ");
            if (exp.toLowerCase() === 'y') {
                const fname = await prompt("Имя файла: ") || "primes.txt";
                exportToFile(fname, primes);
                console.log(`Сохранено в ${fname}`);
            }
        } else if (choice === '2') {
            const a = parseInt(await prompt("Введите A: "));
            const b = parseInt(await prompt("Введите B: "));
            const method = (await prompt("Метод (sieve/trial): ")).toLowerCase() || 'sieve';
            const start = Date.now();
            const primes = primesInRange(a, b, method);
            const elapsed = (Date.now() - start) / 1000;
            const st = stats(primes);
            console.log(`Простых чисел: ${st.count}, Сумма: ${st.sum}, Среднее: ${st.avg.toFixed(2)}, Время: ${elapsed.toFixed(4)} сек.`);
            const show = await prompt("Показать список? (y/n): ");
            if (show.toLowerCase() === 'y') console.log(primes);
            const exp = await prompt("Экспортировать в файл? (y/n): ");
            if (exp.toLowerCase() === 'y') {
                const fname = await prompt("Имя файла: ") || "primes.txt";
                exportToFile(fname, primes);
                console.log(`Сохранено в ${fname}`);
            }
        } else if (choice === '3') {
            const num = parseInt(await prompt("Введите число: "));
            const isPrime = isPrimeTrial(num);
            console.log(`${num} ${isPrime ? 'является' : 'не является'} простым.`);
        } else {
            console.log("Неверный выбор.");
        }
    }
    rl.close();
}

function main() {
    const args = process.argv.slice(2);
    if (args.length > 0) {
        // CLI
        const parsed = {};
        for (let i = 0; i < args.length; i++) {
            if (args[i] === '--max') parsed.max = parseInt(args[++i]);
            else if (args[i] === '--min') parsed.min = parseInt(args[++i]);
            else if (args[i] === '--method') parsed.method = args[++i];
            else if (args[i] === '--export') parsed.export = args[++i];
            else if (args[i] === '--stats') parsed.stats = true;
        }
        if (parsed.max) {
            const start = Date.now();
            const method = parsed.method === 'trial' ? 'trial' : 'sieve';
            let primes;
            if (method === 'sieve') {
                primes = sieveOfEratosthenes(parsed.max);
            } else {
                primes = primesInRange(parsed.min || 2, parsed.max, 'trial');
            }
            const elapsed = (Date.now() - start) / 1000;
            if (parsed.stats) {
                const st = stats(primes);
                console.log(`Количество: ${st.count}, Сумма: ${st.sum}, Среднее: ${st.avg.toFixed(2)}, Мин: ${st.min}, Макс: ${st.max}`);
                console.log(`Время: ${elapsed.toFixed(4)} сек.`);
            } else {
                console.log(primes);
            }
            if (parsed.export) {
                exportToFile(parsed.export, primes);
                console.log(`Экспортировано в ${parsed.export}`);
            }
        } else {
            console.log("Использование: node prime_generator.js --max N [--min M] [--method sieve|trial] [--export file] [--stats]");
        }
    } else {
        interactive();
    }
}

main();

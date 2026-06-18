// prime_generator.rs - Генератор простых чисел на Rust (CLI)
use std::io::{self, Write};
use std::time::Instant;
use std::env;
use std::fs::File;
use std::io::prelude::*;

fn sieve_of_eratosthenes(n: usize) -> Vec<usize> {
    if n < 2 {
        return vec![];
    }
    let mut is_prime = vec![true; n + 1];
    is_prime[0] = false;
    is_prime[1] = false;
    let limit = (n as f64).sqrt() as usize;
    for i in 2..=limit {
        if is_prime[i] {
            let mut j = i * i;
            while j <= n {
                is_prime[j] = false;
                j += i;
            }
        }
    }
    (2..=n).filter(|&i| is_prime[i]).collect()
}

fn is_prime_trial(n: usize) -> bool {
    if n < 2 { return false; }
    if n == 2 { return true; }
    if n % 2 == 0 { return false; }
    let limit = (n as f64).sqrt() as usize;
    for i in (3..=limit).step_by(2) {
        if n % i == 0 {
            return false;
        }
    }
    true
}

fn primes_in_range(a: usize, b: usize, method: &str) -> Vec<usize> {
    let start = if a < 2 { 2 } else { a };
    if start > b {
        return vec![];
    }
    if method == "sieve" {
        let all = sieve_of_eratosthenes(b);
        all.into_iter().filter(|&p| p >= start).collect()
    } else {
        (start..=b).filter(|&p| is_prime_trial(p)).collect()
    }
}

fn stats(primes: &[usize]) -> (usize, usize, f64, usize, usize) {
    let count = primes.len();
    if count == 0 {
        return (0, 0, 0.0, 0, 0);
    }
    let sum: usize = primes.iter().sum();
    let avg = sum as f64 / count as f64;
    let min = primes[0];
    let max = primes[count - 1];
    (count, sum, avg, min, max)
}

fn export_to_file(filename: &str, primes: &[usize]) -> Result<(), std::io::Error> {
    let mut file = File::create(filename)?;
    for p in primes {
        writeln!(file, "{}", p)?;
    }
    Ok(())
}

fn read_line(prompt: &str) -> String {
    print!("{}", prompt);
    io::stdout().flush().unwrap();
    let mut input = String::new();
    io::stdin().read_line(&mut input).expect("Ошибка ввода");
    input.trim().to_string()
}

fn interactive() {
    println!("🔢 Генератор простых чисел (интерактивный)");
    loop {
        println!("\nВыберите действие:");
        println!("1. Сгенерировать простые числа до N");
        println!("2. Сгенерировать простые числа в диапазоне [A, B]");
        println!("3. Проверить число на простоту");
        println!("4. Выход");
        let choice = read_line("Ваш выбор: ");
        match choice.as_str() {
            "4" => break,
            "1" => {
                let n: usize = read_line("Введите N: ").parse().expect("Ошибка ввода");
                let start = Instant::now();
                let primes = sieve_of_eratosthenes(n);
                let elapsed = start.elapsed().as_secs_f64();
                let (count, sum, avg, min, max) = stats(&primes);
                println!("Простых чисел: {}, Сумма: {}, Среднее: {:.2}, Время: {:.4} сек.", count, sum, avg, elapsed);
                let show = read_line("Показать список? (y/n): ");
                if show.to_lowercase() == "y" {
                    println!("{:?}", primes);
                }
                let exp = read_line("Экспортировать в файл? (y/n): ");
                if exp.to_lowercase() == "y" {
                    let fname = read_line("Имя файла: ");
                    let fname = if fname.is_empty() { "primes.txt".to_string() } else { fname };
                    if let Err(e) = export_to_file(&fname, &primes) {
                        println!("Ошибка сохранения: {}", e);
                    } else {
                        println!("Сохранено в {}", fname);
                    }
                }
            }
            "2" => {
                let a: usize = read_line("Введите A: ").parse().expect("Ошибка ввода");
                let b: usize = read_line("Введите B: ").parse().expect("Ошибка ввода");
                let method = read_line("Метод (sieve/trial): ");
                let method = if method == "trial" { "trial" } else { "sieve" };
                let start = Instant::now();
                let primes = primes_in_range(a, b, method);
                let elapsed = start.elapsed().as_secs_f64();
                let (count, sum, avg, min, max) = stats(&primes);
                println!("Простых чисел: {}, Сумма: {}, Среднее: {:.2}, Время: {:.4} сек.", count, sum, avg, elapsed);
                let show = read_line("Показать список? (y/n): ");
                if show.to_lowercase() == "y" {
                    println!("{:?}", primes);
                }
                let exp = read_line("Экспортировать в файл? (y/n): ");
                if exp.to_lowercase() == "y" {
                    let fname = read_line("Имя файла: ");
                    let fname = if fname.is_empty() { "primes.txt".to_string() } else { fname };
                    if let Err(e) = export_to_file(&fname, &primes) {
                        println!("Ошибка сохранения: {}", e);
                    } else {
                        println!("Сохранено в {}", fname);
                    }
                }
            }
            "3" => {
                let num: usize = read_line("Введите число: ").parse().expect("Ошибка ввода");
                let is_prime = is_prime_trial(num);
                println!("{} {} простым.", num, if is_prime { "является" } else { "не является" });
            }
            _ => println!("Неверный выбор."),
        }
    }
}

fn main() {
    let args: Vec<String> = env::args().collect();
    if args.len() > 1 {
        let mut max = 0;
        let mut min = 2;
        let mut method = "sieve".to_string();
        let mut export_file = String::new();
        let mut stats_flag = false;
        let mut i = 1;
        while i < args.len() {
            match args[i].as_str() {
                "--max" => {
                    max = args[i + 1].parse().expect("Ошибка");
                    i += 2;
                }
                "--min" => {
                    min = args[i + 1].parse().expect("Ошибка");
                    i += 2;
                }
                "--method" => {
                    method = args[i + 1].clone();
                    i += 2;
                }
                "--export" => {
                    export_file = args[i + 1].clone();
                    i += 2;
                }
                "--stats" => {
                    stats_flag = true;
                    i += 1;
                }
                _ => {
                    i += 1;
                }
            }
        }
        if max > 0 {
            let start = Instant::now();
            let primes = if method == "sieve" {
                sieve_of_eratosthenes(max)
            } else {
                primes_in_range(min, max, &method)
            };
            let elapsed = start.elapsed().as_secs_f64();
            if stats_flag {
                let (count, sum, avg, min, max) = stats(&primes);
                println!("Количество: {}, Сумма: {}, Среднее: {:.2}, Мин: {}, Макс: {}", count, sum, avg, min, max);
                println!("Время: {:.4} сек.", elapsed);
            } else {
                println!("{:?}", primes);
            }
            if !export_file.is_empty() {
                if let Err(e) = export_to_file(&export_file, &primes) {
                    println!("Ошибка экспорта: {}", e);
                } else {
                    println!("Экспортировано в {}", export_file);
                }
            }
        } else {
            println!("Использование: prime_generator --max N [--min M] [--method sieve|trial] [--export file] [--stats]");
        }
    } else {
        interactive();
    }
}

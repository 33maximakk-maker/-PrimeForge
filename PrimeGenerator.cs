// PrimeGenerator.cs - Генератор простых чисел на C# (CLI)
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Diagnostics;

class PrimeGenerator
{
    static List<int> SieveOfEratosthenes(int n)
    {
        if (n < 2) return new List<int>();
        bool[] isPrime = new bool[n + 1];
        for (int i = 2; i <= n; i++) isPrime[i] = true;
        for (int i = 2; i * i <= n; i++)
        {
            if (isPrime[i])
            {
                for (int j = i * i; j <= n; j += i)
                    isPrime[j] = false;
            }
        }
        List<int> primes = new List<int>();
        for (int i = 2; i <= n; i++)
            if (isPrime[i]) primes.Add(i);
        return primes;
    }

    static bool IsPrimeTrial(int n)
    {
        if (n < 2) return false;
        if (n == 2) return true;
        if (n % 2 == 0) return false;
        for (int i = 3; i * i <= n; i += 2)
            if (n % i == 0) return false;
        return true;
    }

    static List<int> PrimesInRange(int a, int b, string method)
    {
        if (a < 2) a = 2;
        if (a > b) return new List<int>();
        if (method == "sieve")
        {
            var all = SieveOfEratosthenes(b);
            return all.Where(p => p >= a).ToList();
        }
        else
        {
            List<int> res = new List<int>();
            for (int p = a; p <= b; p++)
                if (IsPrimeTrial(p)) res.Add(p);
            return res;
        }
    }

    static (int count, long sum, double avg, int min, int max) Stats(List<int> primes)
    {
        int count = primes.Count;
        if (count == 0) return (0, 0, 0, 0, 0);
        long sum = primes.Sum(p => (long)p);
        double avg = sum / (double)count;
        return (count, sum, avg, primes[0], primes[count - 1]);
    }

    static void ExportToFile(string filename, List<int> primes)
    {
        using (StreamWriter sw = new StreamWriter(filename))
        {
            foreach (int p in primes)
                sw.WriteLine(p);
        }
    }

    static void Interactive()
    {
        Console.WriteLine("🔢 Генератор простых чисел (интерактивный)");
        while (true)
        {
            Console.WriteLine("\nВыберите действие:");
            Console.WriteLine("1. Сгенерировать простые числа до N");
            Console.WriteLine("2. Сгенерировать простые числа в диапазоне [A, B]");
            Console.WriteLine("3. Проверить число на простоту");
            Console.WriteLine("4. Выход");
            Console.Write("Ваш выбор: ");
            string choice = Console.ReadLine().Trim();
            if (choice == "4") break;
            else if (choice == "1")
            {
                Console.Write("Введите N: ");
                int n = int.Parse(Console.ReadLine().Trim());
                Stopwatch sw = Stopwatch.StartNew();
                var primes = SieveOfEratosthenes(n);
                sw.Stop();
                var stats = Stats(primes);
                Console.WriteLine($"Простых чисел: {stats.count}, Сумма: {stats.sum}, Среднее: {stats.avg:F2}, Время: {sw.Elapsed.TotalSeconds:F4} сек.");
                Console.Write("Показать список? (y/n): ");
                if (Console.ReadLine().Trim().ToLower() == "y")
                    Console.WriteLine(string.Join(", ", primes));
                Console.Write("Экспортировать в файл? (y/n): ");
                if (Console.ReadLine().Trim().ToLower() == "y")
                {
                    Console.Write("Имя файла: ");
                    string fname = Console.ReadLine().Trim();
                    if (string.IsNullOrEmpty(fname)) fname = "primes.txt";
                    ExportToFile(fname, primes);
                    Console.WriteLine($"Сохранено в {fname}");
                }
            }
            else if (choice == "2")
            {
                Console.Write("Введите A: ");
                int a = int.Parse(Console.ReadLine().Trim());
                Console.Write("Введите B: ");
                int b = int.Parse(Console.ReadLine().Trim());
                Console.Write("Метод (sieve/trial): ");
                string method = Console.ReadLine().Trim().ToLower();
                if (method != "sieve" && method != "trial") method = "sieve";
                Stopwatch sw = Stopwatch.StartNew();
                var primes = PrimesInRange(a, b, method);
                sw.Stop();
                var stats = Stats(primes);
                Console.WriteLine($"Простых чисел: {stats.count}, Сумма: {stats.sum}, Среднее: {stats.avg:F2}, Время: {sw.Elapsed.TotalSeconds:F4} сек.");
                Console.Write("Показать список? (y/n): ");
                if (Console.ReadLine().Trim().ToLower() == "y")
                    Console.WriteLine(string.Join(", ", primes));
                Console.Write("Экспортировать в файл? (y/n): ");
                if (Console.ReadLine().Trim().ToLower() == "y")
                {
                    Console.Write("Имя файла: ");
                    string fname = Console.ReadLine().Trim();
                    if (string.IsNullOrEmpty(fname)) fname = "primes.txt";
                    ExportToFile(fname, primes);
                    Console.WriteLine($"Сохранено в {fname}");
                }
            }
            else if (choice == "3")
            {
                Console.Write("Введите число: ");
                int num = int.Parse(Console.ReadLine().Trim());
                bool isPrime = IsPrimeTrial(num);
                Console.WriteLine($"{num} {(isPrime ? "является" : "не является")} простым.");
            }
            else
            {
                Console.WriteLine("Неверный выбор.");
            }
        }
    }

    static void Main(string[] args)
    {
        if (args.Length > 0)
        {
            int max = 0, min = 2;
            string method = "sieve";
            string exportFile = null;
            bool statsFlag = false;
            for (int i = 0; i < args.Length; i++)
            {
                switch (args[i])
                {
                    case "--max": max = int.Parse(args[++i]); break;
                    case "--min": min = int.Parse(args[++i]); break;
                    case "--method": method = args[++i]; break;
                    case "--export": exportFile = args[++i]; break;
                    case "--stats": statsFlag = true; break;
                }
            }
            if (max > 0)
            {
                Stopwatch sw = Stopwatch.StartNew();
                List<int> primes;
                if (method == "sieve")
                    primes = SieveOfEratosthenes(max);
                else
                    primes = PrimesInRange(min, max, method);
                sw.Stop();
                if (statsFlag)
                {
                    var stats = Stats(primes);
                    Console.WriteLine($"Количество: {stats.count}, Сумма: {stats.sum}, Среднее: {stats.avg:F2}, Мин: {stats.min}, Макс: {stats.max}");
                    Console.WriteLine($"Время: {sw.Elapsed.TotalSeconds:F4} сек.");
                }
                else
                {
                    Console.WriteLine(string.Join(", ", primes));
                }
                if (exportFile != null)
                {
                    ExportToFile(exportFile, primes);
                    Console.WriteLine($"Экспортировано в {exportFile}");
                }
            }
            else
            {
                Console.WriteLine("Использование: PrimeGenerator --max N [--min M] [--method sieve|trial] [--export file] [--stats]");
            }
        }
        else
        {
            Interactive();
        }
    }
}

// prime_generator.go - Генератор простых чисел на Go (CLI)
package main

import (
	"bufio"
	"flag"
	"fmt"
	"math"
	"os"
	"strconv"
	"strings"
	"time"
)

func sieveOfEratosthenes(n int) []int {
	if n < 2 {
		return []int{}
	}
	isPrime := make([]bool, n+1)
	for i := 2; i <= n; i++ {
		isPrime[i] = true
	}
	for i := 2; i*i <= n; i++ {
		if isPrime[i] {
			for j := i * i; j <= n; j += i {
				isPrime[j] = false
			}
		}
	}
	primes := []int{}
	for i := 2; i <= n; i++ {
		if isPrime[i] {
			primes = append(primes, i)
		}
	}
	return primes
}

func isPrimeTrial(n int) bool {
	if n < 2 {
		return false
	}
	if n == 2 {
		return true
	}
	if n%2 == 0 {
		return false
	}
	for i := 3; i*i <= n; i += 2 {
		if n%i == 0 {
			return false
		}
	}
	return true
}

func primesInRange(a, b int, method string) []int {
	if a < 2 {
		a = 2
	}
	if a > b {
		return []int{}
	}
	if method == "sieve" {
		all := sieveOfEratosthenes(b)
		res := []int{}
		for _, p := range all {
			if p >= a {
				res = append(res, p)
			}
		}
		return res
	} else {
		res := []int{}
		for p := a; p <= b; p++ {
			if isPrimeTrial(p) {
				res = append(res, p)
			}
		}
		return res
	}
}

func stats(primes []int) (count int, sum int, avg float64, min int, max int) {
	count = len(primes)
	if count == 0 {
		return 0, 0, 0, 0, 0
	}
	sum = 0
	for _, p := range primes {
		sum += p
	}
	avg = float64(sum) / float64(count)
	min = primes[0]
	max = primes[count-1]
	return
}

func exportToFile(filename string, primes []int) error {
	f, err := os.Create(filename)
	if err != nil {
		return err
	}
	defer f.Close()
	for _, p := range primes {
		_, err := f.WriteString(strconv.Itoa(p) + "\n")
		if err != nil {
			return err
		}
	}
	return nil
}

func interactive() {
	scanner := bufio.NewScanner(os.Stdin)
	fmt.Println("🔢 Генератор простых чисел (интерактивный)")
	for {
		fmt.Println("\nВыберите действие:")
		fmt.Println("1. Сгенерировать простые числа до N")
		fmt.Println("2. Сгенерировать простые числа в диапазоне [A, B]")
		fmt.Println("3. Проверить число на простоту")
		fmt.Println("4. Выход")
		fmt.Print("Ваш выбор: ")
		scanner.Scan()
		choice := scanner.Text()
		if choice == "4" {
			break
		} else if choice == "1" {
			fmt.Print("Введите N: ")
			scanner.Scan()
			n, err := strconv.Atoi(scanner.Text())
			if err != nil {
				fmt.Println("Ошибка ввода")
				continue
			}
			start := time.Now()
			primes := sieveOfEratosthenes(n)
			elapsed := time.Since(start).Seconds()
			count, sum, avg, min, max := stats(primes)
			fmt.Printf("Простых чисел: %d, Сумма: %d, Среднее: %.2f, Время: %.4f сек.\n", count, sum, avg, elapsed)
			fmt.Print("Показать список? (y/n): ")
			scanner.Scan()
			if strings.ToLower(scanner.Text()) == "y" {
				fmt.Println(primes)
			}
			fmt.Print("Экспортировать в файл? (y/n): ")
			scanner.Scan()
			if strings.ToLower(scanner.Text()) == "y" {
				fmt.Print("Имя файла: ")
				scanner.Scan()
				fname := scanner.Text()
				if fname == "" {
					fname = "primes.txt"
				}
				err := exportToFile(fname, primes)
				if err != nil {
					fmt.Println("Ошибка сохранения:", err)
				} else {
					fmt.Printf("Сохранено в %s\n", fname)
				}
			}
		} else if choice == "2" {
			fmt.Print("Введите A: ")
			scanner.Scan()
			a, err := strconv.Atoi(scanner.Text())
			if err != nil {
				fmt.Println("Ошибка ввода")
				continue
			}
			fmt.Print("Введите B: ")
			scanner.Scan()
			b, err := strconv.Atoi(scanner.Text())
			if err != nil {
				fmt.Println("Ошибка ввода")
				continue
			}
			fmt.Print("Метод (sieve/trial): ")
			scanner.Scan()
			method := scanner.Text()
			if method != "sieve" && method != "trial" {
				method = "sieve"
			}
			start := time.Now()
			primes := primesInRange(a, b, method)
			elapsed := time.Since(start).Seconds()
			count, sum, avg, min, max := stats(primes)
			fmt.Printf("Простых чисел: %d, Сумма: %d, Среднее: %.2f, Время: %.4f сек.\n", count, sum, avg, elapsed)
			fmt.Print("Показать список? (y/n): ")
			scanner.Scan()
			if strings.ToLower(scanner.Text()) == "y" {
				fmt.Println(primes)
			}
			fmt.Print("Экспортировать в файл? (y/n): ")
			scanner.Scan()
			if strings.ToLower(scanner.Text()) == "y" {
				fmt.Print("Имя файла: ")
				scanner.Scan()
				fname := scanner.Text()
				if fname == "" {
					fname = "primes.txt"
				}
				err := exportToFile(fname, primes)
				if err != nil {
					fmt.Println("Ошибка сохранения:", err)
				} else {
					fmt.Printf("Сохранено в %s\n", fname)
				}
			}
		} else if choice == "3" {
			fmt.Print("Введите число: ")
			scanner.Scan()
			num, err := strconv.Atoi(scanner.Text())
			if err != nil {
				fmt.Println("Ошибка ввода")
				continue
			}
			isPrime := isPrimeTrial(num)
			fmt.Printf("%d %s простым.\n", num, map[bool]string{true: "является", false: "не является"}[isPrime])
		} else {
			fmt.Println("Неверный выбор.")
		}
	}
}

func main() {
	var max int
	var min int
	var method string
	var exportFile string
	var statsFlag bool

	flag.IntVar(&max, "max", 0, "Верхняя граница")
	flag.IntVar(&min, "min", 2, "Нижняя граница")
	flag.StringVar(&method, "method", "sieve", "Алгоритм (sieve/trial)")
	flag.StringVar(&exportFile, "export", "", "Файл для экспорта")
	flag.BoolVar(&statsFlag, "stats", false, "Показать статистику")
	flag.Parse()

	if max > 0 {
		start := time.Now()
		var primes []int
		if method == "sieve" {
			primes = sieveOfEratosthenes(max)
		} else {
			primes = primesInRange(min, max, method)
		}
		elapsed := time.Since(start).Seconds()
		if statsFlag {
			count, sum, avg, min, max := stats(primes)
			fmt.Printf("Количество: %d, Сумма: %d, Среднее: %.2f, Мин: %d, Макс: %d\n", count, sum, avg, min, max)
			fmt.Printf("Время: %.4f сек.\n", elapsed)
		} else {
			fmt.Println(primes)
		}
		if exportFile != "" {
			err := exportToFile(exportFile, primes)
			if err != nil {
				fmt.Println("Ошибка экспорта:", err)
			} else {
				fmt.Printf("Экспортировано в %s\n", exportFile)
			}
		}
	} else {
		interactive()
	}
}

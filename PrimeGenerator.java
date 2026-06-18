// PrimeGenerator.java - Генератор простых чисел на Java (CLI)
import java.io.*;
import java.util.*;

public class PrimeGenerator {
    private static Scanner scanner = new Scanner(System.in);

    public static List<Integer> sieveOfEratosthenes(int n) {
        if (n < 2) return new ArrayList<>();
        boolean[] isPrime = new boolean[n + 1];
        Arrays.fill(isPrime, true);
        isPrime[0] = isPrime[1] = false;
        for (int i = 2; i * i <= n; i++) {
            if (isPrime[i]) {
                for (int j = i * i; j <= n; j += i) {
                    isPrime[j] = false;
                }
            }
        }
        List<Integer> primes = new ArrayList<>();
        for (int i = 2; i <= n; i++) {
            if (isPrime[i]) primes.add(i);
        }
        return primes;
    }

    public static boolean isPrimeTrial(int n) {
        if (n < 2) return false;
        if (n == 2) return true;
        if (n % 2 == 0) return false;
        for (int i = 3; i * i <= n; i += 2) {
            if (n % i == 0) return false;
        }
        return true;
    }

    public static List<Integer> primesInRange(int a, int b, String method) {
        if (a < 2) a = 2;
        if (a > b) return new ArrayList<>();
        if (method.equals("sieve")) {
            List<Integer> all = sieveOfEratosthenes(b);
            List<Integer> res = new ArrayList<>();
            for (int p : all) {
                if (p >= a) res.add(p);
            }
            return res;
        } else {
            List<Integer> res = new ArrayList<>();
            for (int p = a; p <= b; p++) {
                if (isPrimeTrial(p)) res.add(p);
            }
            return res;
        }
    }

    public static Map<String, Object> stats(List<Integer> primes) {
        Map<String, Object> st = new HashMap<>();
        int count = primes.size();
        st.put("count", count);
        if (count == 0) {
            st.put("sum", 0);
            st.put("avg", 0.0);
            st.put("min", 0);
            st.put("max", 0);
            return st;
        }
        long sum = 0;
        for (int p : primes) sum += p;
        st.put("sum", sum);
        st.put("avg", (double) sum / count);
        st.put("min", primes.get(0));
        st.put("max", primes.get(count - 1));
        return st;
    }

    public static void exportToFile(String filename, List<Integer> primes) throws IOException {
        try (PrintWriter pw = new PrintWriter(new FileWriter(filename))) {
            for (int p : primes) {
                pw.println(p);
            }
        }
    }

    public static void interactive() {
        System.out.println("🔢 Генератор простых чисел (интерактивный)");
        while (true) {
            System.out.println("\nВыберите действие:");
            System.out.println("1. Сгенерировать простые числа до N");
            System.out.println("2. Сгенерировать простые числа в диапазоне [A, B]");
            System.out.println("3. Проверить число на простоту");
            System.out.println("4. Выход");
            System.out.print("Ваш выбор: ");
            String choice = scanner.nextLine().trim();
            if (choice.equals("4")) break;
            else if (choice.equals("1")) {
                System.out.print("Введите N: ");
                int n = Integer.parseInt(scanner.nextLine().trim());
                long start = System.currentTimeMillis();
                List<Integer> primes = sieveOfEratosthenes(n);
                long elapsed = System.currentTimeMillis() - start;
                Map<String, Object> st = stats(primes);
                System.out.printf("Простых чисел: %d, Сумма: %d, Среднее: %.2f, Время: %.3f сек.\n",
                        st.get("count"), st.get("sum"), st.get("avg"), elapsed / 1000.0);
                System.out.print("Показать список? (y/n): ");
                String show = scanner.nextLine().trim().toLowerCase();
                if (show.equals("y")) {
                    System.out.println(primes);
                }
                System.out.print("Экспортировать в файл? (y/n): ");
                String exp = scanner.nextLine().trim().toLowerCase();
                if (exp.equals("y")) {
                    System.out.print("Имя файла: ");
                    String fname = scanner.nextLine().trim();
                    if (fname.isEmpty()) fname = "primes.txt";
                    try {
                        exportToFile(fname, primes);
                        System.out.println("Сохранено в " + fname);
                    } catch (IOException e) {
                        System.out.println("Ошибка сохранения: " + e.getMessage());
                    }
                }
            } else if (choice.equals("2")) {
                System.out.print("Введите A: ");
                int a = Integer.parseInt(scanner.nextLine().trim());
                System.out.print("Введите B: ");
                int b = Integer.parseInt(scanner.nextLine().trim());
                System.out.print("Метод (sieve/trial): ");
                String method = scanner.nextLine().trim().toLowerCase();
                if (!method.equals("sieve") && !method.equals("trial")) method = "sieve";
                long start = System.currentTimeMillis();
                List<Integer> primes = primesInRange(a, b, method);
                long elapsed = System.currentTimeMillis() - start;
                Map<String, Object> st = stats(primes);
                System.out.printf("Простых чисел: %d, Сумма: %d, Среднее: %.2f, Время: %.3f сек.\n",
                        st.get("count"), st.get("sum"), st.get("avg"), elapsed / 1000.0);
                System.out.print("Показать список? (y/n): ");
                String show = scanner.nextLine().trim().toLowerCase();
                if (show.equals("y")) {
                    System.out.println(primes);
                }
                System.out.print("Экспортировать в файл? (y/n): ");
                String exp = scanner.nextLine().trim().toLowerCase();
                if (exp.equals("y")) {
                    System.out.print("Имя файла: ");
                    String fname = scanner.nextLine().trim();
                    if (fname.isEmpty()) fname = "primes.txt";
                    try {
                        exportToFile(fname, primes);
                        System.out.println("Сохранено в " + fname);
                    } catch (IOException e) {
                        System.out.println("Ошибка сохранения: " + e.getMessage());
                    }
                }
            } else if (choice.equals("3")) {
                System.out.print("Введите число: ");
                int num = Integer.parseInt(scanner.nextLine().trim());
                boolean isPrime = isPrimeTrial(num);
                System.out.printf("%d %s простым.\n", num, isPrime ? "является" : "не является");
            } else {
                System.out.println("Неверный выбор.");
            }
        }
    }

    public static void main(String[] args) {
        if (args.length > 0) {
            // CLI
            int max = 0, min = 2;
            String method = "sieve";
            String exportFile = null;
            boolean statsFlag = false;
            for (int i = 0; i < args.length; i++) {
                switch (args[i]) {
                    case "--max": max = Integer.parseInt(args[++i]); break;
                    case "--min": min = Integer.parseInt(args[++i]); break;
                    case "--method": method = args[++i]; break;
                    case "--export": exportFile = args[++i]; break;
                    case "--stats": statsFlag = true; break;
                }
            }
            if (max > 0) {
                long start = System.currentTimeMillis();
                List<Integer> primes;
                if (method.equals("sieve")) {
                    primes = sieveOfEratosthenes(max);
                } else {
                    primes = primesInRange(min, max, method);
                }
                long elapsed = System.currentTimeMillis() - start;
                if (statsFlag) {
                    Map<String, Object> st = stats(primes);
                    System.out.printf("Количество: %d, Сумма: %d, Среднее: %.2f, Мин: %d, Макс: %d\n",
                            st.get("count"), st.get("sum"), st.get("avg"), st.get("min"), st.get("max"));
                    System.out.printf("Время: %.3f сек.\n", elapsed / 1000.0);
                } else {
                    System.out.println(primes);
                }
                if (exportFile != null) {
                    try {
                        exportToFile(exportFile, primes);
                        System.out.println("Экспортировано в " + exportFile);
                    } catch (IOException e) {
                        System.out.println("Ошибка экспорта: " + e.getMessage());
                    }
                }
            } else {
                System.out.println("Использование: java PrimeGenerator --max N [--min M] [--method sieve|trial] [--export file] [--stats]");
            }
        } else {
            interactive();
        }
    }
}

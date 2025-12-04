<?php

require __DIR__ . '/vendor/autoload.php';

use Faker\Factory;

$faker = Factory::create("en_US");

$mysqli = new mysqli("db", "appuser", "apppass", "appdb");

$mysqli->set_charset("utf8mb4");

$mysqli->query("
CREATE TABLE IF NOT EXISTS stats_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month VARCHAR(20),
    sales INT,
    new_clients INT,
    category VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    rating INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$mysqli->query("TRUNCATE stats_data");

$stmt = $mysqli->prepare("
    INSERT INTO stats_data (month, sales, new_clients, category, rating)
    VALUES (?, ?, ?, ?, ?)
");
for ($i = 0; $i < 50; $i++) {
    $month = $faker->monthName();
    $sales = $faker->numberBetween(10, 500);
    $clients = $faker->numberBetween(1, 100);
    $category = $faker->randomElement(["Soft Toys", "Cars", "Dolls", "Board Games"]);
    $rating = $faker->numberBetween(1, 10);

    $stmt->bind_param("siisi", $month, $sales, $clients, $category, $rating);
    $stmt->execute();
}

echo "Fixtures loaded successfully!\n";

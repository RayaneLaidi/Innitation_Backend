<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$apiKey = '244180607fbccb8fbad6873771ee7b48'; // Remplace par ta clé API OpenWeather
$ville = isset($_GET['ville']) ? $_GET['ville'] : '';
$weatherData = null;
$errorMessage = '';

if ($ville) {
    try {
        $client = new Client();
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$ville}&appid={$apiKey}&units=metric&lang=fr";
        $response = $client->request('GET', $url);
        $data = json_decode($response->getBody(), true);

        if ($data['cod'] == 200) {
            $weatherData = [
                'ville' => $ville,
                'pays' => $data['sys']['country'],
                'temp' => $data['main']['temp'],
                'feels_like' => $data['main']['feels_like'],
                'temp_min' => $data['main']['temp_min'],
                'temp_max' => $data['main']['temp_max'],
                'pressure' => $data['main']['pressure'],
                'description' => $data['weather'][0]['description'],
                'icon' => $data['weather'][0]['icon'],
                'humidity' => $data['main']['humidity'],
                'windSpeed' => round($data['wind']['speed'] * 3.6), // m/s → km/h
                'sunrise' => date('H:i', $data['sys']['sunrise']),
                'sunset' => date('H:i', $data['sys']['sunset']),
            ];
        } else {
            $errorMessage = "Ville introuvable.";
        }
    } catch (RequestException $e) {
        $errorMessage = "Erreur de requête : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Météo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #005a87;
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
        }

        .weather-form {
            margin-top: 10px;
        }

        .weather-form input {
            padding: 10px;
            width: 250px;
            border-radius: 5px;
            border: none;
            margin-right: 8px;
        }

        .weather-form button {
            padding: 10px 15px;
            background-color: #ffb81c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .weather-form button:hover {
            background-color: #f0a800;
        }

        main {
            padding: 40px 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .weather-details {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .weather-details h2 {
            margin-top: 0;
            color: #005a87;
        }

        .weather-details img {
            width: 80px;
            vertical-align: middle;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ========== HEADER ========== -->
<header>
    <h1>Météo en temps réel</h1>
    <form method="get" class="weather-form">
        <input type="text" name="ville" placeholder="Entrez une ville" value="<?= htmlspecialchars($ville); ?>" required>
        <button type="submit">Rechercher</button>
    </form>
</header>

<!-- ========== MAIN ========== -->
<main>
    <?php if ($errorMessage): ?>
        <p class="error"><?= $errorMessage ?></p>
    <?php elseif ($weatherData): ?>
        <div class="weather-details">
            <h2>Météo à <?= htmlspecialchars(ucfirst($weatherData['ville'])) ?> (<?= $weatherData['pays'] ?>)</h2>
            <img src="https://openweathermap.org/img/wn/<?= $weatherData['icon'] ?>@2x.png" alt="Icône météo">
            <p><strong>Température actuelle :</strong> <?= $weatherData['temp'] ?>°C</p>
            <p><strong>Ressenti :</strong> <?= $weatherData['feels_like'] ?>°C</p>
            <p><strong>Min / Max :</strong> <?= $weatherData['temp_min'] ?>°C / <?= $weatherData['temp_max'] ?>°C</p>
            <p><strong>Ciel :</strong> <?= ucfirst($weatherData['description']) ?></p>
            <p><strong>Humidité :</strong> <?= $weatherData['humidity'] ?>%</p>
            <p><strong>Pression :</strong> <?= $weatherData['pressure'] ?> hPa</p>
            <p><strong>Vent :</strong> <?= $weatherData['windSpeed'] ?> km/h</p>
            <p><strong>Lever du soleil :</strong> <?= $weatherData['sunrise'] ?> 🡅</p>
            <p><strong>Coucher du soleil :</strong> <?= $weatherData['sunset'] ?> 🡇</p>
        </div>
    <?php endif; ?>
</main>

</body>
</html>

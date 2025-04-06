<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calcul de moyenne</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .resultat {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2em;
            padding: 15px;
            border-radius: 8px;
        }

        .suffisant {
            background-color: #d4edda;
            color: #155724;
        }

        .insuffisant {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Calcul de moyenne</h2>

    <form method="POST">
        <label>Nom de l'étudiant :</label>
        <input type="text" name="nom" required>

        <label>Note 1 :</label>
        <input type="number" name="note1" step="0.01" required>

        <label>Note 2 :</label>
        <input type="number" name="note2" step="0.01" required>

        <label>Note 3 :</label>
        <input type="number" name="note3" step="0.01" required>

        <input type="submit" value="Calculer la moyenne">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $note1 = $_POST['note1'];
        $note2 = $_POST['note2'];
        $note3 = $_POST['note3'];

        function calculerMoyenne($n1, $n2, $n3) {
            return ($n1 + $n2 + $n3) / 3;
        }

        function afficherResultat($nom, $moyenne) {
            $classeCss = ($moyenne >= 10) ? 'suffisant' : 'insuffisant';
            $message = ($moyenne >= 10) ? "Suffisante ✅" : "Insuffisante ❌";

            echo "<div class='resultat $classeCss'>";
            echo "<strong>$nom</strong><br>";
            echo "Moyenne : " . number_format($moyenne, 2) . "<br>";
            echo "Résultat : $message";
            echo "</div>";
        }

        $moyenne = calculerMoyenne($note1, $note2, $note3);
        afficherResultat($nom, $moyenne);
    }
    ?>
</div>

</body>
</html>

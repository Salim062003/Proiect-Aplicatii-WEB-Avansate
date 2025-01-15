<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meniu Principal</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            display: inline-block;
            margin: 10px;
        }

        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        form {
            width: 50%;
            margin: 20px auto;
            text-align: left;
        }

        input[type="text"], input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h1>Meniu Principal</h1>

<ul>
    <li><a href="GestionareClienti.php">Gestionare Clienți</a></li>
    <li><a href="GestionareMasini.php">Gestionare Mașini</a></li>
    <li><a href="GestionareProgramari.php">Gestionare Programări</a></li>
    <li><a href="GestionareServicii.php">Gestionare Servicii</a></li>
    <li><a href="GestionarePiese.php">Gestionare Piese</a></li>
    <li><a href="GestionareAngajati.php">Gestionare Angajați</a></li>
    <li><a href="GestionareFacturi.php">Gestionare Facturi</a></li>
</ul>

</body>
</html>

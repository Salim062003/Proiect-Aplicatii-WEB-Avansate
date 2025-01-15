<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principală</title>
    <style>
        /* Adaugă stilurile pentru a face meniul mai frumos */
        a {
            text-decoration: none;
            color: #4CAF50;
            font-size: 18px;
            padding: 10px;
            display: block;
        }

        a:hover {
            background-color: #f1f1f1;
        }

        /* Poți adăuga și alte stiluri pentru a îmbunătăți designul */
    </style>
</head>
<body>

    <!-- Meniul de navigare -->
    <a href="?page=meniu">Meniu</a>
    <a href="?page=GestionareAngajati">Gestionare Angajați</a>
    <a href="?page=GestionareClienti">Gestionare Clienți</a>
    <a href="?page=GestionareFacturi">Gestionare Facturi</a>
    <a href="?page=GestionareMasini">Gestionare Mașini</a>
    <a href="?page=GestionarePiese">Gestionare Piese</a>
    <a href="?page=GestionareProgramari">Gestionare Programări</a>
    <a href="?page=GestionareServicii">Gestionare Servicii</a>

    <hr>

    <!-- Locul în care se încarcă conținutul în funcție de parametrii URL -->
    <?php
    if (!isset($_GET['page'])) {
        include "meniu.php"; // Fișierul implicit (poate fi meniul principal)
    } else {
        // Folosim switch pentru a încarca fișierele corespunzătoare
        switch ($_GET['page']) {
            case "meniu":
                include "meniu.php";
                break;
            case "GestionareAngajati":
                include "GestionareAngajati.php";
                break;
            case "GestionareClienti":
                include "GestionareClienti.php";
                break;
            case "GestionareFacturi":
                include "GestionareFacturi.php";
                break;
            case "GestionareMasini":
                include "GestionareMasini.php";
                break;
            case "GestionarePiese":
                include "GestionarePiese.php";
                break;
            case "GestionareProgramari":
                include "GestionareProgramari.php";
                break;
            case "GestionareServicii":
                include "GestionareServicii.php";
                break;
            default:
                echo "<p>Pagina nu a fost găsită.</p>";
        }
    }
    ?>

</body>
</html>

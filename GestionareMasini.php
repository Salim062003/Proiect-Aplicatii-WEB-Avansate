<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectarea la baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");

// Verificarea conexiunii
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

// Funcția pentru a afișa tabelul cu mașinile
function afiseazaMasini($db, $sortOrder = 'ASC') {
    $query = "SELECT * FROM masina ORDER BY SERIE_SASIU $sortOrder";
    $rezultate = $db->query($query);
    $output = "<h2 style='color: #333; text-align: center;'>Listă mașini</h2>";

    //daca avem cel putin o linie in tabel, afisam headerul tabelului

    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>Serie Șasiu</th>";
        $output .= "<th>Marca</th>";
        $output .= "<th>Model</th>";
        $output .= "<th>An Fabricație</th>";
        $output .= "<th>Nr Inmatriculare</th>";
        $output .= "<th>CNP Client</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

             //parcurgem rezultatele linie cu linie si le adaugam si pe ele la output

        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["SERIE_SASIU"]}</td>";
            $output .= "<td>{$rand["MARCA"]}</td>";
            $output .= "<td>{$rand["MODEL"]}</td>";
            $output .= "<td>{$rand["AN_FABRICATIE"]}</td>";
            $output .= "<td>{$rand["NR_INMATRICULARE"]}</td>";
            $output .= "<td>{$rand["CNP_CLIENT"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionareMasini.php?action=edit&id={$rand["SERIE_SASIU"]}'>Editează</a>";
            $output .= " | ";
            $output .= "<a style='color: #E53935; text-decoration: none;' href='GestionareMasini.php?action=delete&id={$rand["SERIE_SASIU"]}' onclick='return confirm(\"Sunteți sigur că doriți să ștergeți această mașină?\")'>Șterge</a>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "Nu există mașini în baza de date.";
    }

    return $output;
}

//functie ce va fi apelata pentru a insera o linie in tabel printr-un query de SQL.
function adaugaMasina($db, $serieSasiu, $marca, $model, $anFabricatie, $nrInmatriculare, $cnpClient) {
    $query = "INSERT INTO masina (SERIE_SASIU, MARCA, MODEL, AN_FABRICATIE, NR_INMATRICULARE, CNP_CLIENT) 
              VALUES ('$serieSasiu', '$marca', '$model', $anFabricatie, '$nrInmatriculare', $cnpClient)";
    if ($db->query($query)) {
        echo "<p>Mașina a fost adăugată cu succes!</p>";
    } else {
        echo "<p>Eroare: " . $db->error . "</p>";
    }
}

// Funcția pentru a șterge o mașină
function stergeMasina($db, $serieSasiu) {
    $query = "DELETE FROM masina WHERE SERIE_SASIU = '$serieSasiu'";
    if ($db->query($query)) {
        echo "<p>Mașina a fost ștearsă cu succes!</p>";
    } else {
        echo "<p>Eroare: " . $db->error . "</p>";
    }
}

// Funcția pentru a modifica o mașină
function modificaMasina($db, $serieSasiu, $marca, $model, $anFabricatie, $nrInmatriculare, $cnpClient) {
    $query = "UPDATE masina SET MARCA=?, MODEL=?, AN_FABRICATIE=?, NR_INMATRICULARE=?, CNP_CLIENT=? WHERE SERIE_SASIU=?";
    $stmt = $db->prepare($query);

    if ($stmt === false) {
        die("Eroare la pregătirea interogării: " . $db->error);
    }

    // Leagă parametrii
    $stmt->bind_param("ssisss", $marca, $model, $anFabricatie, $nrInmatriculare, $cnpClient, $serieSasiu);

    // Execută interogarea
    if ($stmt->execute()) {
        echo "<p style='color: #4CAF50; text-align: center;'>Modificările au fost salvate cu succes.</p>";
    } else {
        echo "<p style='color: #E53935; text-align: center;'>Eroare la salvarea modificărilor: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

//obtinem parametrii masinii in functie de serie, folosing SELECT cu WHERE
function getMasinaDetails($db, $serieSasiu) {
    $query = "SELECT * FROM masina WHERE SERIE_SASIU = '$serieSasiu'";
    $result = $db->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Procesarea formularului de adăugare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaMasina") {
    $serieSasiu = $_POST["serie_sasiu"];
    $marca = $_POST["marca"];
    $model = $_POST["model"];
    $anFabricatie = $_POST["an_fabricatie"];
    $nrInmatriculare = $_POST["nr_inmatriculare"];
    $cnpClient = $_POST["cnp_client"];

    adaugaMasina($db, $serieSasiu, $marca, $model, $anFabricatie, $nrInmatriculare, $cnpClient);
}

// Verificarea și procesarea ștergerii
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["id"])) {
        $serieSasiu = $_GET["id"];
        stergeMasina($db, $serieSasiu);
    }
}

// Verificarea și procesarea editării
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] == "edit") {
    if (isset($_GET["id"])) {
        $serieSasiu = $_GET["id"];
        $masinaDetails = getMasinaDetails($db, $serieSasiu);

        if ($masinaDetails) {
            // Afișează formularul de editare
            echo "<h2 style='color: #333; text-align: center;'>Editează mașină</h2>";
            echo "<form action='' method='post' style='width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
            echo "<input type='hidden' name='action' value='modificaMasina'>";
            echo "<input type='hidden' name='serie_sasiu' value='{$masinaDetails["SERIE_SASIU"]}'>";
            echo "<label for='marca'>Marca:</label>";
            echo "<input type='text' name='marca' placeholder='Marca' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$masinaDetails["MARCA"]}'>";
            echo "<label for='model'>Model:</label>";
            echo "<input type='text' name='model' placeholder='Model' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$masinaDetails["MODEL"]}'>";
            echo "<label for='an_fabricatie'>An fabricație:</label>";
            echo "<input type='number' name='an_fabricatie' placeholder='An fabricație' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$masinaDetails["AN_FABRICATIE"]}'>";
            echo "<label for='nr_inmatriculare'>Număr înmatriculare:</label>";
            echo "<input type='text' name='nr_inmatriculare' placeholder='Număr înmatriculare' style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$masinaDetails["NR_INMATRICULARE"]}'>";
            echo "<label for='cnp_client'>CNP Client:</label>";
            echo "<input type='number' name='cnp_client' placeholder='CNP Client' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$masinaDetails["CNP_CLIENT"]}'>";
            echo "<input type='submit' value='Salvează modificările' style='background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;'>";
            echo "</form>";
        } else {
            echo "<p style='color: #E53935; text-align: center;'>Nu s-a găsit mașina specificată pentru editare.</p>";
        }
    }
}

// Procesăm salvarea modificărilor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaMasina") {
    $serieSasiu = $_POST["serie_sasiu"];
    $marca = $_POST["marca"];
    $model = $_POST["model"];
    $anFabricatie = $_POST["an_fabricatie"];
    $nrInmatriculare = $_POST["nr_inmatriculare"];
    $cnpClient = $_POST["cnp_client"];

    modificaMasina($db, $serieSasiu, $marca, $model, $anFabricatie, $nrInmatriculare, $cnpClient);
}

// Afișăm mașinile
echo afiseazaMasini($db);
?>

<!-- Formularul de adăugare -->
<form action="" method="post" style="width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <input type="hidden" name="action" value="adaugaMasina">
    <label for="serie_sasiu">Serie șasiu:</label>
    <input type="text" name="serie_sasiu" placeholder="Serie șasiu" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="marca">Marca:</label>
    <input type="text" name="marca" placeholder="Marca" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="model">Model:</label>
    <input type="text" name="model" placeholder="Model" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="an_fabricatie">An fabricație:</label>
    <input type="number" name="an_fabricatie" placeholder="An fabricație" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="nr_inmatriculare">Număr înmatriculare:</label>
    <input type="text" name="nr_inmatriculare" placeholder="Număr înmatriculare" style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="cnp_client">CNP Client:</label>
    <input type="number" name="cnp_client" placeholder="CNP Client" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <input type="submit" value="Adaugă mașină" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
</form>

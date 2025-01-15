<?php
//accesam baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");
//verificam daca s-a efectuat conexiunea
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

//functia pentru a afisa tabelul cu masinile, in functie de sortOrder(default ASC).
function afiseazaProgramari($db, $sortOrder = 'ASC') {
    //trimitem un query catre baza de date pentru a obtine toate datele, ca sa facem sortarea prin NrProgramare
    $query = "SELECT * FROM programari ORDER BY NR_PROGRAMARE $sortOrder";
    $rezultate = $db->query($query);

    $output = "<h2 style='text-align: center;'>Listă programări</h2>";

    //daca avem cel putin o linie in tabel, afisam headerul tabelului
    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>Nr Programare</th>";
        $output .= "<th>Data Programare</th>";
        $output .= "<th>Serie Sasiu</th>";
        $output .= "<th>Descriere</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

        //parcurgem rezultatele linie cu linie
        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["NR_PROGRAMARE"]}</td>";
            $output .= "<td>{$rand["DATA_PROGRAMARE"]}</td>";
            $output .= "<td>{$rand["SERIE_SASIU"]}</td>";
            $output .= "<td>{$rand["DESCRIERE"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionareProgramari.php?action=edit&id={$rand["NR_PROGRAMARE"]}'>Editează</a>";
            $output .= " | ";
            $output .= "<a style='color: #E53935; text-decoration: none;' href='GestionareProgramari.php?action=delete&id={$rand["NR_PROGRAMARE"]}' onclick='return confirm(\"Sunteți sigur că doriți să ștergeți această programare?\")'>Șterge</a>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "Nu există programări în baza de date.";
    }

    return $output;
}

//initial sortOrder este ASC, verificam ce buton e apasat la sortOrder(Crescator/descrescator)
$sortOrder = 'ASC';
if (isset($_GET['sortOrder']) && ($_GET['sortOrder'] == 'ASC' || $_GET['sortOrder'] == 'DESC')) {
    $sortOrder = $_GET['sortOrder'];
}
echo afiseazaProgramari($db, $sortOrder);

//functie ce va fi apelata pentru a insera o linie in tabel printr-un query de SQL.
function adaugaProgramare($db, $dataProgramare, $serieSasiu, $descriere) {
    
    $dataCurenta = date("Y-m-d"); 
    if ($dataProgramare < $dataCurenta) {
        echo "<p style='color: #E53935; text-align: center;'>Data programării nu poate fi în trecut!</p>";
        return; 
    }


    $query = "INSERT INTO programari (DATA_PROGRAMARE, SERIE_SASIU, DESCRIERE) VALUES ('$dataProgramare', '$serieSasiu', '$descriere')";
    $db->query($query);
}

function stergeProgramare($db, $nrProgramare) {
    $query = "DELETE FROM programari WHERE NR_PROGRAMARE = $nrProgramare";
    $db->query($query);
    // Resetăm AUTO_INCREMENT pentru a evita saltul numerelor
    $db->query("ALTER TABLE programari AUTO_INCREMENT = 1");
}

//obtinem parametrii programarii in functie de NrProgramare, folosing SELECT cu WHERE
function getDetaliiProgramare($db, $nrProgramare) {
    $query = "SELECT * FROM programari WHERE NR_PROGRAMARE = $nrProgramare";
    $result = $db->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

//procesarea formularului pentru adăugare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaProgramare") {
    $dataProgramare = $_POST["data_programare"];
    $serieSasiu = $_POST["serie_sasiu"];
    $descriere = $_POST["descriere"];

    adaugaProgramare($db, $dataProgramare, $serieSasiu, $descriere);
}

//procesarea ștergerii
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["id"])) {
        $nrProgramare = $_GET["id"];
        stergeProgramare($db, $nrProgramare);
    }
}

//procesarea editării
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaProgramare") {
    $nrProgramare = $_POST["nr_programare"];
    $dataProgramare = $_POST["data_programare"];
    $serieSasiu = $_POST["serie_sasiu"];
    $descriere = $_POST["descriere"];

    $query = "UPDATE programari SET DATA_PROGRAMARE = '$dataProgramare', SERIE_SASIU = '$serieSasiu', DESCRIERE = '$descriere' WHERE NR_PROGRAMARE = $nrProgramare";
    $db->query($query);
}

//formularul pentru editare
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] == "edit") {
    if (isset($_GET["id"])) {
        $nrProgramare = $_GET["id"];
        $programareDetails = getDetaliiProgramare($db, $nrProgramare);
        if ($programareDetails) {
            echo "<h2 style='text-align: center; color: #333;'>Editează programare</h2>";
            echo "<form action='' method='post' style='width: 50%; margin: 20px auto;text-align: center; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
            echo "<input type='hidden' name='action' value='modificaProgramare'>";
            echo "<input type='hidden' name='nr_programare' value='{$programareDetails["NR_PROGRAMARE"]}'>";
            echo "<label for='data_programare'>Data programare:</label>";
            echo "<input type='date' name='data_programare' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$programareDetails["DATA_PROGRAMARE"]}'>";
            echo "<label for='serie_sasiu'>Serie șasiu mașină:</label>";
            echo "<input type='text' name='serie_sasiu' placeholder='Serie șasiu mașină' required style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$programareDetails["SERIE_SASIU"]}'>";
            echo "<label for='descriere'>Descriere:</label>";
            echo "<input type='text' name='descriere' placeholder='Descriere' style='width: 100%; padding: 8px; margin-bottom: 10px;' value='{$programareDetails["DESCRIERE"]}'>";
            echo "<input type='submit' value='Salvează modificările' style='background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;'>";
            echo "</form>";
        } else {
            echo "<p style='color: #E53935; text-align: center;'>Nu s-a găsit programarea specificată pentru editare.</p>";
        }
    }
}

?>
<form method='get' action='GestionareProgramari.php'>
    <label for='sortOrder'>Sortare:</label>
    <select name='sortOrder'>
        <option value='ASC' <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Crescător</option>
        <option value='DESC' <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descrescător</option>
    </select>
    <input type='submit' value='Sortează'>
</form>

<h2 style='text-align: center; color: #333;'>Adaugă programare</h2>
<form action='' method='post' style='width: 50%; margin: 20px auto;text-align: center; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
    <input type='hidden' name='action' value='adaugaProgramare'>
    <label for='data_programare'>Data programare:</label>
    <input type='date' name='data_programare' required style='width: 100%; padding: 8px; margin-bottom: 10px;' >
    <label for='serie_sasiu'>Serie șasiu mașină:</label>
    <input type='text' name='serie_sasiu' placeholder='Serie șasiu mașină' required style='width: 100%; padding: 8px; margin-bottom: 10px;'>
    <label for='descriere'>Descriere:</label>
    <input type='text' name='descriere' placeholder='Descriere' style='width: 100%; padding: 8px; margin-bottom: 10px;'>
    <input type='submit' value='Adaugă programare' style='background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;'>
</form>
<?php
$db->close();
?>

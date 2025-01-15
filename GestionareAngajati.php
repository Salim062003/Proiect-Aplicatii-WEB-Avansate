<?php
// accesam baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");

// verificam daca s-a efectuat conexiunea
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

// functie pentru a afisa tabelul cu angajatii
function afiseazaAngajati($db, $sortOrder = 'ASC') {
    $query = "SELECT * FROM angajat ORDER BY CNP_ANGAJAT $sortOrder";
    $rezultate = $db->query($query);

    $output = "<h2 style='color: #333; text-align: center;'>Listă angajați</h2>";

    //daca avem cel putin o linie in tabel, afisam headerul tabelului

    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>CNP Angajat</th>";
        $output .= "<th>Nume</th>";
        $output .= "<th>Prenume</th>";
        $output .= "<th>Adresă</th>";
        $output .= "<th>Telefon</th>";
        $output .= "<th>Email</th>";
        $output .= "<th>Salariu</th>";
        $output .= "<th>Data Angajare</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

             //parcurgem rezultatele linie cu linie si le adaugam si pe ele la output

        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["CNP_ANGAJAT"]}</td>";
            $output .= "<td>{$rand["NUME"]}</td>";
            $output .= "<td>{$rand["PRENUME"]}</td>";
            $output .= "<td>{$rand["ADRESA"]}</td>";
            $output .= "<td>{$rand["TELEFON"]}</td>";
            $output .= "<td>{$rand["EMAIL"]}</td>";
            $output .= "<td>{$rand["SALARIU"]}</td>";
            $output .= "<td>{$rand["DATA_ANGAJARE"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionareAngajati.php?action=edit&id={$rand["CNP_ANGAJAT"]}'>Editează</a>";
            $output .= " | ";
            $output .= "<a style='color: #E53935; text-decoration: none;' href='GestionareAngajati.php?action=delete&id={$rand["CNP_ANGAJAT"]}' onclick='return confirm(\"Sunteți sigur că doriți să ștergeți acest angajat?\")'>Șterge</a>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "<p style='color: #E53935;'>Nu există angajați în baza de date.</p>";
    }

    return $output;
}


//functie ce va fi apelata pentru a insera o linie in tabel printr-un query de SQL.
function adaugaAngajat($db, $cnpAngajat, $nume, $prenume, $adresa, $telefon, $email, $salariu, $dataAngajare) {
    $query = "INSERT INTO angajat (CNP_ANGAJAT, NUME, PRENUME, ADRESA, TELEFON, EMAIL, SALARIU, DATA_ANGAJARE) 
              VALUES ('$cnpAngajat', '$nume', '$prenume', '$adresa', '$telefon', '$email', $salariu, '$dataAngajare')";
    if ($db->query($query) === TRUE) {
        echo "<p style='color: #4CAF50; text-align: center;'>Angajat adăugat cu succes!</p>";
    } else {
        echo "<p style='color: #E53935; text-align: center;'>Eroare: " . $db->error . "</p>";
    }
}

// Șterge angajat din baza de date
function stergeAngajat($db, $cnpAngajat) {
    $query = "DELETE FROM angajat WHERE CNP_ANGAJAT = '$cnpAngajat'";
    $db->query($query);
}

// Modifică angajat în baza de date
function modificaAngajat($db, $cnpAngajat, $nume, $prenume, $adresa, $telefon, $email, $salariu, $dataAngajare) {
    $query = "UPDATE angajat SET NUME = '$nume', PRENUME = '$prenume', ADRESA = '$adresa', TELEFON = '$telefon', EMAIL = '$email', SALARIU = $salariu, DATA_ANGAJARE = '$dataAngajare' WHERE CNP_ANGAJAT = '$cnpAngajat'";
    if ($db->query($query) === TRUE) {
        echo "<p style='color: #4CAF50; text-align: center;'>Angajatul a fost modificat cu succes!</p>";
    } else {
        echo "<p style='color: #E53935; text-align: center;'>Eroare: " . $db->error . "</p>";
    }
}

//obtinem parametrii angajatului in functie de CNP, folosing SELECT cu WHERE
function getDetaliiAngajat($db, $cnpAngajat) {
    $query = "SELECT * FROM angajat WHERE CNP_ANGAJAT = '$cnpAngajat'";
    $result = $db->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Procesarea formularului de editare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaAngajat") {
    $cnpAngajat = $_POST["cnp_angajat"];
    $nume = $_POST["nume"];
    $prenume = $_POST["prenume"];
    $adresa = $_POST["adresa"];
    $telefon = $_POST["telefon"];
    $email = $_POST["email"];
    $salariu = $_POST["salariu"];
    $dataAngajare = $_POST["data_angajare"];

    modificaAngajat($db, $cnpAngajat, $nume, $prenume, $adresa, $telefon, $email, $salariu, $dataAngajare);
}

// Procesarea formularului de adăugare a unui angajat
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaAngajat") {
    $cnpAngajat = $_POST["cnp_angajat"];
    $nume = $_POST["nume"];
    $prenume = $_POST["prenume"];
    $adresa = $_POST["adresa"];
    $telefon = $_POST["telefon"];
    $email = $_POST["email"];
    $salariu = $_POST["salariu"];
    $dataAngajare = $_POST["data_angajare"];

    adaugaAngajat($db, $cnpAngajat, $nume, $prenume, $adresa, $telefon, $email, $salariu, $dataAngajare);
}

// Procesarea ștergerii unui angajat
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["id"])) {
        $cnpAngajat = $_GET["id"];
        stergeAngajat($db, $cnpAngajat);
    }
}

// Formularul pentru a edita un angajat
$detaliiAngajat = null;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "edit") {
    if (isset($_GET["id"])) {
        $cnpAngajat = $_GET["id"];
        $detaliiAngajat = getDetaliiAngajat($db, $cnpAngajat);
    }
}

?>

<!-- Tabelul cu angajați va fi afișat sus pe pagină -->
<?php echo afiseazaAngajati($db); ?>

<!-- Formularul pentru adăugarea unui angajat (este mereu vizibil) -->
<h2 style="text-align: center; color: #333;">Adaugă Angajat</h2>
<form method="POST" style="width: 50%; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <input type="hidden" name="action" value="adaugaAngajat">
    <label for="cnp_angajat">CNP Angajat:</label>
    <input type="text" name="cnp_angajat" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="nume">Nume:</label>
    <input type="text" name="nume" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="prenume">Prenume:</label>
    <input type="text" name="prenume" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="adresa">Adresă:</label>
    <input type="text" name="adresa" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="telefon">Telefon:</label>
    <input type="text" name="telefon" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="email">Email:</label>
    <input type="text" name="email" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="salariu">Salariu:</label>
    <input type="text" name="salariu" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <label for="data_angajare">Data Angajare:</label>
    <input type="date" name="data_angajare" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    
    <input type="submit" value="Adaugă Angajat" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">
</form>

<!-- Formularul pentru modificarea unui angajat, se afiseaza doar daca este selectat un angajat pentru modificare -->
<?php if ($detaliiAngajat): ?>
    <h2 style="text-align: center; color: #333;">Modifică Angajat</h2>
    <form method="POST" style="width: 50%; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <input type="hidden" name="action" value="modificaAngajat">
        <input type="hidden" name="cnp_angajat" value="<?php echo $detaliiAngajat['CNP_ANGAJAT']; ?>">
        
        <label for="nume">Nume:</label>
        <input type="text" name="nume" value="<?php echo $detaliiAngajat['NUME']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="prenume">Prenume:</label>
        <input type="text" name="prenume" value="<?php echo $detaliiAngajat['PRENUME']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="adresa">Adresă:</label>
        <input type="text" name="adresa" value="<?php echo $detaliiAngajat['ADRESA']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="telefon">Telefon:</label>
        <input type="text" name="telefon" value="<?php echo $detaliiAngajat['TELEFON']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="email">Email:</label>
        <input type="text" name="email" value="<?php echo $detaliiAngajat['EMAIL']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="salariu">Salariu:</label>
        <input type="text" name="salariu" value="<?php echo $detaliiAngajat['SALARIU']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <label for="data_angajare">Data Angajare:</label>
        <input type="date" name="data_angajare" value="<?php echo $detaliiAngajat['DATA_ANGAJARE']; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        
        <input type="submit" value="Modifică Angajat" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">
    </form>
<?php endif; ?>

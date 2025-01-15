<?php
// Conectare la baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");

// Verificăm conexiunea la baza de date
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

// Funcția pentru a afișa serviciile din baza de date, sortate după ID_SERVICIU
function afiseazaServicii($db, $sortOrder = 'ASC') {
    $query = "SELECT * FROM servicii ORDER BY ID_SERVICIU $sortOrder";
    $rezultate = $db->query($query);

    $output = "<h2 style='color: #333; text-align: center;'>Listă servicii</h2>";

    //daca avem cel putin o linie in tabel, afisam headerul tabelului(id serviciu, pret ,  etc etc) 
    //concatenand variabila output, care va contine un string cu cod de html

    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>ID Serviciu</th>";
        $output .= "<th>Denumire Serviciu</th>";
        $output .= "<th>Pret</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

         //parcurgem rezultatele linie cu linie si le adaugam si pe ele la output
            

        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["ID_SERVICIU"]}</td>";
            $output .= "<td>{$rand["DENUMIRE_SERVICIU"]}</td>";
            $output .= "<td>{$rand["PRET"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionareServicii.php?action=edit&id={$rand["ID_SERVICIU"]}'>Editează</a>";
            $output .= " | ";
            // Formular pentru ștergerea serviciului
            $output .= "<form action='GestionareServicii.php' method='POST' style='display:inline;'>";
            $output .= "<input type='hidden' name='action' value='stergeServiciu'>";
            $output .= "<input type='hidden' name='id_serviciu' value='{$rand["ID_SERVICIU"]}'>";
            $output .= "<button type='submit' style='background-color: #E53935; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;'>Șterge</button>";
            $output .= "</form>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "Nu există servicii în baza de date.";
    }

    return $output;
}

// Verificăm dacă utilizatorul a selectat sortarea
$sortOrder = 'ASC'; 
if (isset($_GET['sortOrder']) && ($_GET['sortOrder'] == 'ASC' || $_GET['sortOrder'] == 'DESC')) {
    $sortOrder = $_GET['sortOrder'];
}
echo afiseazaServicii($db, $sortOrder);

//functie ce va fi apelata pentru a insera o linie in tabel printr-un query de SQL.

function adaugaServiciu($db, $denumireServiciu, $pret) {
    $query = "INSERT INTO servicii (DENUMIRE_SERVICIU, PRET) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sd", $denumireServiciu, $pret); 
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green; text-align: center;'>Serviciul a fost adăugat cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la adăugarea serviciului.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}

// Funcția pentru a șterge un serviciu și a reseta auto-increment
function stergeServiciu($db, $idServiciu) {
    // Se pregătește interogarea de ștergere
    $query = "DELETE FROM servicii WHERE ID_SERVICIU = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $idServiciu);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Resetarea auto-incrementului pentru a reîncepe de la ID-ul corect
            $queryReset = "ALTER TABLE servicii AUTO_INCREMENT = 1";
            $db->query($queryReset); // Resetarea auto-incrementului

            echo "<p style='color: green; text-align: center;'>Serviciul a fost șters cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la ștergerea serviciului.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}



// Funcția pentru a modifica un serviciu
function modificaServiciu($db, $idServiciu, $denumireServiciu, $pret) {
    $query = "UPDATE servicii SET DENUMIRE_SERVICIU = ?, PRET = ? WHERE ID_SERVICIU = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sdi", $denumireServiciu, $pret, $idServiciu);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green; text-align: center;'>Serviciul a fost modificat cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la modificarea serviciului.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}

// Codul pentru adăugarea unui serviciu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaServiciu") {
    $denumireServiciu = $_POST["denumire_serviciu"];
    $pret = $_POST["pret"];

    if (!empty($denumireServiciu) && !empty($pret)) {
        adaugaServiciu($db, $denumireServiciu, $pret);
    } else {
        echo "<p style='color: red; text-align: center;'>Te rugăm să completezi toate câmpurile!</p>";
    }
}

// Codul pentru a șterge un serviciu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "stergeServiciu") {
    $idServiciu = $_POST["id_serviciu"];
    stergeServiciu($db, $idServiciu);
}

// Codul pentru a modifica un serviciu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaServiciu") {
    $idServiciu = $_POST["id_serviciu"];
    $denumireServiciu = $_POST["denumire_serviciu"];
    $pret = $_POST["pret"];

    if (!empty($idServiciu) && !empty($denumireServiciu) && !empty($pret)) {
        modificaServiciu($db, $idServiciu, $denumireServiciu, $pret);
    } else {
        echo "<p style='color: red; text-align: center;'>Te rugăm să completezi toate câmpurile!</p>";
    }
}

// Verificăm dacă vrem să edităm un serviciu
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $idServiciu = $_GET['id'];
    $query = "SELECT * FROM servicii WHERE ID_SERVICIU = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $idServiciu);
    $stmt->execute();
    $rezultate = $stmt->get_result();
    $serviciu = $rezultate->fetch_assoc();
    $stmt->close();

    if ($serviciu) {
        $denumireServiciu = $serviciu['DENUMIRE_SERVICIU'];
        $pret = $serviciu['PRET'];
    }
}
?>

<form method="get" action="GestionareServicii.php">
    <label for="sortOrder">Sortare:</label>
    <select name="sortOrder">
        <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Crescător</option>
        <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descrescător</option>
    </select>
    <input type="submit" value="Sortează">
</form>

<h2 style="color: #333; text-align: center;">Adaugă serviciu</h2>
<form action="" method="post" style="width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <input type="hidden" name="action" value="adaugaServiciu">
    <label for="denumire_serviciu">Denumire serviciu:</label>
    <input type="text" name="denumire_serviciu" placeholder="Denumire serviciu" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="pret">Preț:</label>
    <input type="text" name="pret" placeholder="Preț" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <input type="submit" value="Adaugă serviciu" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
</form>

<?php if (isset($serviciu)): ?>
    <h2 style="color: #333; text-align: center;">Editează serviciu</h2>
    <form action="" method="post" style="width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <input type="hidden" name="action" value="modificaServiciu">
        <input type="hidden" name="id_serviciu" value="<?php echo $idServiciu; ?>">
        <label for="denumire_serviciu">Denumire serviciu:</label>
        <input type="text" name="denumire_serviciu" value="<?php echo $denumireServiciu; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <label for="pret">Preț:</label>
        <input type="text" name="pret" value="<?php echo $pret; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <input type="submit" value="Modifică serviciu" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
    </form>
<?php endif; ?>

<?php
$db->close();
?>

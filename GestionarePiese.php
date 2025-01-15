<?php
// Conectare la baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");

// Verificăm conexiunea la baza de date
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

// Funcția pentru a afișa piesele din baza de date, sortate după ID_PIESA
function afiseazaPiese($db, $sortOrder = 'ASC') {
    $query = "SELECT * FROM piese ORDER BY ID_PIESA $sortOrder";
    $rezultate = $db->query($query);

    $output = "<h2 style='color: #333; text-align: center;'>Listă piese</h2>";

    //daca avem cel putin o linie in tabel, afisam headerul tabelului

    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>ID Piesa</th>";
        $output .= "<th>Denumire Piesa</th>";
        $output .= "<th>Pret</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

         //parcurgem rezultatele linie cu linie si le adaugam si pe ele la output

        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["ID_PIESA"]}</td>";
            $output .= "<td>{$rand["DENUMIRE_PIESA"]}</td>";
            $output .= "<td>{$rand["PRET"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionarePiese.php?action=edit&id={$rand["ID_PIESA"]}'>Editează</a>";
            $output .= " | ";
            // Formular pentru ștergerea piesei
            $output .= "<form action='GestionarePiese.php' method='POST' style='display:inline;'>";
            $output .= "<input type='hidden' name='action' value='stergePiesa'>";
            $output .= "<input type='hidden' name='id_piesa' value='{$rand["ID_PIESA"]}'>";
            $output .= "<button type='submit' style='background-color: #E53935; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;'>Șterge</button>";
            $output .= "</form>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "Nu există piese în baza de date.";
    }

    return $output;
}

// Verificăm dacă utilizatorul a selectat sortarea
$sortOrder = 'ASC'; 
if (isset($_GET['sortOrder']) && ($_GET['sortOrder'] == 'ASC' || $_GET['sortOrder'] == 'DESC')) {
    $sortOrder = $_GET['sortOrder'];
}
echo afiseazaPiese($db, $sortOrder);

//functie ce va fi apelata pentru a insera o linie in tabel printr-un query de SQL.
function adaugaPiesa($db, $denumirePiesa, $pret) {
    $query = "INSERT INTO piese (DENUMIRE_PIESA, PRET) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sd", $denumirePiesa, $pret); 
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green; text-align: center;'>Piesa a fost adăugată cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la adăugarea piesei.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}

// Funcția pentru a șterge o piesă și a reseta auto-increment
function stergePiesa($db, $idPiesa) {
    // Se pregătește interogarea de ștergere
    $query = "DELETE FROM piese WHERE ID_PIESA = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $idPiesa);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Resetarea auto-incrementului pentru a reîncepe de la ID-ul corect
            $queryReset = "ALTER TABLE piese AUTO_INCREMENT = 1";
            $db->query($queryReset); // Resetarea auto-incrementului

            echo "<p style='color: green; text-align: center;'>Piesa a fost ștersă cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la ștergerea piesei.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}

// Funcția pentru a modifica o piesă
function modificaPiesa($db, $idPiesa, $denumirePiesa, $pret) {
    $query = "UPDATE piese SET DENUMIRE_PIESA = ?, PRET = ? WHERE ID_PIESA = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sdi", $denumirePiesa, $pret, $idPiesa);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green; text-align: center;'>Piesa a fost modificată cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>A apărut o problemă la modificarea piesei.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Eroare la pregătirea interogării SQL!</p>";
    }
}

// Codul pentru adăugarea unei piese
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaPiesa") {
    $denumirePiesa = $_POST["denumire_piesa"];
    $pret = $_POST["pret"];

    if (!empty($denumirePiesa) && !empty($pret)) {
        adaugaPiesa($db, $denumirePiesa, $pret);
    } else {
        echo "<p style='color: red; text-align: center;'>Te rugăm să completezi toate câmpurile!</p>";
    }
}

// Codul pentru a șterge o piesă
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "stergePiesa") {
    $idPiesa = $_POST["id_piesa"];
    stergePiesa($db, $idPiesa);
}

// Codul pentru a modifica o piesă
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaPiesa") {
    $idPiesa = $_POST["id_piesa"];
    $denumirePiesa = $_POST["denumire_piesa"];
    $pret = $_POST["pret"];

    if (!empty($idPiesa) && !empty($denumirePiesa) && !empty($pret)) {
        modificaPiesa($db, $idPiesa, $denumirePiesa, $pret);
    } else {
        echo "<p style='color: red; text-align: center;'>Te rugăm să completezi toate câmpurile!</p>";
    }
}

// Verificăm dacă vrem să edităm o piesă
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $idPiesa = $_GET['id'];

    //obtinem parametrii piesei in functie de ID Piesa, folosing SELECT cu WHERE
    $query = "SELECT * FROM piese WHERE ID_PIESA = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $idPiesa);
    $stmt->execute();
    $rezultate = $stmt->get_result();
    $piesa = $rezultate->fetch_assoc();
    $stmt->close();

    if ($piesa) {
        $denumirePiesa = $piesa['DENUMIRE_PIESA'];
        $pret = $piesa['PRET'];
    }
}
?>

<form method="get" action="GestionarePiese.php">
    <label for="sortOrder">Sortare:</label>
    <select name="sortOrder">
        <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Crescător</option>
        <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descrescător</option>
    </select>
    <input type="submit" value="Sortează">
</form>

<h2 style="color: #333; text-align: center;">Adaugă piesă</h2>
<form action="" method="post" style="width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <input type="hidden" name="action" value="adaugaPiesa">
    <label for="denumire_piesa">Denumire piesă:</label>
    <input type="text" name="denumire_piesa" placeholder="Denumire piesă" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <label for="pret">Preț:</label>
    <input type="text" name="pret" placeholder="Preț" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
    <input type="submit" value="Adaugă piesă" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
</form>

<?php if (isset($piesa)): ?>
    <h2 style="color: #333; text-align: center;">Editează piesă</h2>
    <form action="" method="post" style="width: 50%; text-align: center; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <input type="hidden" name="action" value="modificaPiesa">
        <input type="hidden" name="id_piesa" value="<?php echo $idPiesa; ?>">
        <label for="denumire_piesa">Denumire piesă:</label>
        <input type="text" name="denumire_piesa" value="<?php echo $denumirePiesa; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <label for="pret">Preț:</label>
        <input type="text" name="pret" value="<?php echo $pret; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <input type="submit" value="Modifică piesă" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
    </form>
<?php endif; ?>

<?php
$db->close();
?>

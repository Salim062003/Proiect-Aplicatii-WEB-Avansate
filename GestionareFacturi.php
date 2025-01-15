<?php
// Conectarea la baza de date
$db = new mysqli("localhost", "root", "", "serviceauto");

// Verificăm dacă s-a efectuat conexiunea
if ($db->connect_error) {
    die("Eroare la conectarea la baza de date: " . $db->connect_error);
}

// Funcție pentru a afișa facturile
function afiseazaFacturi($db, $sortOrder = 'ASC') {
    // Interogare pentru a obține facturile
    $query = "SELECT f.ID_FACTURA, f.DATA_EMITERE, f.TOTAL_PLATA, c.CNP_CLIENT, c.NUME, c.PRENUME
              FROM FACTURA f
              LEFT JOIN client c ON f.CNP_CLIENT = c.CNP_CLIENT
              ORDER BY f.DATA_EMITERE $sortOrder";
    $rezultate = $db->query($query);

    $output = "<h2 style='color: #333;'>Listă Facturi</h2>";

    // Dacă avem rezultate, afisăm tabelul
    if ($rezultate->num_rows > 0) {
        $output .= "<table style='width: 100%; border-collapse: collapse;'>";
        $output .= "<tr style='background-color: #4CAF50; color: white;'>";
        $output .= "<th>ID Factura</th>";
        $output .= "<th>Data Emitere</th>";
        $output .= "<th>Total Plata</th>";
        $output .= "<th>CNP Client</th>";
        $output .= "<th>Nume Client</th>";
        $output .= "<th>Prenume Client</th>";
        $output .= "<th>Acțiuni</th>";
        $output .= "</tr>";

        // Parcurgem rezultatele și le adăugăm în tabel
        while ($rand = $rezultate->fetch_assoc()) {
            $output .= "<tr style='text-align: center;'>";
            $output .= "<td>{$rand["ID_FACTURA"]}</td>";
            $output .= "<td>{$rand["DATA_EMITERE"]}</td>";
            $output .= "<td>{$rand["TOTAL_PLATA"]}</td>";
            $output .= "<td>{$rand["CNP_CLIENT"]}</td>";
            $output .= "<td>{$rand["NUME"]}</td>";
            $output .= "<td>{$rand["PRENUME"]}</td>";
            $output .= "<td>";
            $output .= "<a style='color: #4CAF50; text-decoration: none;' href='GestionareFacturi.php?action=edit&id={$rand["ID_FACTURA"]}'>Editează</a>";
            $output .= " | ";
            $output .= "<a style='color: #E53935; text-decoration: none;' href='GestionareFacturi.php?action=delete&id={$rand["ID_FACTURA"]}' onclick='return confirm(\"Sunteți sigur că doriți să ștergeți această factură?\")'>Șterge</a>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= "</table>";
    } else {
        $output .= "<p style='color: #E53935;'>Nu există facturi în baza de date.</p>";
    }

    return $output;
}

// Funcție pentru a adăuga o factură
function adaugaFactura($db, $dataEmitere, $totalPlata, $cnpClient) {
    // Verificăm dacă CNP-ul clientului este valid (în baza de date)
    $query = "SELECT CNP_CLIENT FROM client WHERE CNP_CLIENT = '$cnpClient'";
    $result = $db->query($query);
    
    if ($result->num_rows == 0) {
        echo "<p style='color: #E53935;'>Clientul cu CNP-ul $cnpClient nu există în baza de date.</p>";
        return;
    }

    // Interogare pentru a adăuga factura
    $query = "INSERT INTO FACTURA (DATA_EMITERE, TOTAL_PLATA, CNP_CLIENT) VALUES ('$dataEmitere', '$totalPlata', '$cnpClient')";
    if ($db->query($query)) {
        echo "<p style='color: green;'>Factura a fost adăugată cu succes!</p>";
    } else {
        echo "<p style='color: #E53935;'>Eroare la adăugarea facturii: " . $db->error . "</p>";
    }
}

// Funcție pentru a șterge o factură
function stergeFactura($db, $idFactura) {
    $query = "DELETE FROM FACTURA WHERE ID_FACTURA = '$idFactura'";
    if ($db->query($query)) {
        echo "<p style='color: green;'>Factura a fost ștearsă cu succes!</p>";
    } else {
        echo "<p style='color: #E53935;'>Eroare la ștergerea facturii: " . $db->error . "</p>";
    }
}

// Funcție pentru a obține detalii despre o factură
function getDetaliiFactura($db, $idFactura) {
    $query = "SELECT * FROM FACTURA WHERE ID_FACTURA = '$idFactura'";
    $result = $db->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Funcție pentru a modifica o factură
function modificaFactura($db, $idFactura, $dataEmitere, $totalPlata, $cnpClient) {
    // Verificăm dacă CNP-ul clientului este valid (în baza de date)
    $query = "SELECT CNP_CLIENT FROM client WHERE CNP_CLIENT = '$cnpClient'";
    $result = $db->query($query);
    
    if ($result->num_rows == 0) {
        echo "<p style='color: #E53935;'>Clientul cu CNP-ul $cnpClient nu există în baza de date.</p>";
        return;
    }

    // Interogare pentru a modifica factura
    $query = "UPDATE FACTURA SET DATA_EMITERE = '$dataEmitere', TOTAL_PLATA = '$totalPlata', CNP_CLIENT = '$cnpClient' WHERE ID_FACTURA = '$idFactura'";
    if ($db->query($query)) {
        echo "<p style='color: green;'>Factura a fost modificată cu succes!</p>";
    } else {
        echo "<p style='color: #E53935;'>Eroare la modificarea facturii: " . $db->error . "</p>";
    }
}

// Codul care se execută când se apasă pe butonul de adăugare factură
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "adaugaFactura") {
    $dataEmitere = $_POST["data_emitere"];
    $totalPlata = $_POST["total_plata"];
    $cnpClient = $_POST["cnp_client"];

    adaugaFactura($db, $dataEmitere, $totalPlata, $cnpClient);
}

// Codul care se execută când se apasă pe butonul de ștergere factură
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["id"])) {
        $idFactura = $_GET["id"];
        stergeFactura($db, $idFactura);
    }
}

// Codul care se execută când se apasă pe butonul de editare factură
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "modificaFactura") {
    if (isset($_POST["id_factura"])) {
        $idFactura = $_POST["id_factura"];
        $dataEmitere = $_POST["data_emitere"];
        $totalPlata = $_POST["total_plata"];
        $cnpClient = $_POST["cnp_client"];

        modificaFactura($db, $idFactura, $dataEmitere, $totalPlata, $cnpClient);
    }
}

// Codul pentru form-ul de adăugare și editare factură
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "edit") {
    if (isset($_GET["id"])) {
        $idFactura = $_GET["id"];
        $detaliiFactura = getDetaliiFactura($db, $idFactura);

        if ($detaliiFactura) {
            echo "<h2>Editează Factura</h2>";
            echo "<form method='POST' action='GestionareFacturi.php'>";
            echo "<input type='hidden' name='action' value='modificaFactura'>";
            echo "<input type='hidden' name='id_factura' value='" . $detaliiFactura["ID_FACTURA"] . "'>";
            echo "Data Emitere: <input type='date' name='data_emitere' value='" . $detaliiFactura["DATA_EMITERE"] . "'><br>";
            echo "Total Plata: <input type='number' step='0.01' name='total_plata' value='" . $detaliiFactura["TOTAL_PLATA"] . "'><br>";
            echo "CNP Client: <input type='text' name='cnp_client' value='" . $detaliiFactura["CNP_CLIENT"] . "'><br>";
            echo "<input type='submit' value='Modifică Factura'>";
            echo "</form>";
        } else {
            echo "<p>Factura nu a fost găsită.</p>";
        }
    }
}

// Form-ul pentru adăugarea unei facturi
echo "<h2>Adaugă Factură</h2>";
echo "<form method='POST' action='GestionareFacturi.php'>";
echo "<input type='hidden' name='action' value='adaugaFactura'>";
echo "Data Emitere: <input type='date' name='data_emitere'><br>";
echo "Total Plata: <input type='number' step='0.01' name='total_plata'><br>";
echo "CNP Client: <input type='text' name='cnp_client'><br>";
echo "<input type='submit' value='Adaugă Factura'>";
echo "</form>";

// Afișăm facturile existente
echo afiseazaFacturi($db);

// Închidem conexiunea la baza de date
$db->close();
?>

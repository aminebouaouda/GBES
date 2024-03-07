<?php
// Database connection parameters (same as before)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gbes1";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to delete a transaction from the "facture_p" and "detail_p" tables
if (isset($_GET["delete"]) && $_GET["delete"] == "transaction" && isset($_GET["facture_p_id"])) {
    $facture_p_id = $_GET["facture_p_id"];
    $sql_delete = "DELETE FROM detail_p WHERE facture_p_id = '$facture_p_id';DELETE FROM facture_p WHERE facture_p_id = '$facture_p_id'";
    if ($conn->multi_query($sql_delete) === TRUE) {
        // Redirect to the same page to display the updated table
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Retrieve transaction history data from the "facture_p," "cheque," and "produit" tables
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT f.facture_p_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde, pa.intitule AS assurance_intitule
        FROM facture_p f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_p dp ON f.facture_p_id = dp.facture_p_id
        INNER JOIN produit p ON dp.produit_id = p.produit_id
        LEFT JOIN patient pt ON f.patient_id = pt.patient_id
        LEFT JOIN assurance pa ON pt.assurance_id = pa.assurance_id
        WHERE p.ref LIKE '%$search_query%' OR f.facture_p_id = '$search_query'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Historique des transactions clients</title>
    <style>
    /* Common Styles */
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    h1 {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-align: center;
          
            font-size: 28px;
            margin-bottom: 20px;
        }
        

    /* Table Styles */
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        font-size: 16px;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

   
        /* Form Styles */
        .search-client-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-client-form label {
            font-weight: bold;
            margin-right: 10px;
        }

        .search-client-input {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-client-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }


    /* Total Solde Styles */
    .total-solde {
        text-align: right;
        font-weight: bold;
        margin-top: 20px;
        font-size: 16px;
    }

    .total-solde span {
        font-size: 14px;
    }

    /* Realiser Facture Button Styles */
    .realiser-facture-btn {
        background-color: #5f5;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-weight: bold;
        text-transform: uppercase;
    }

    .realiser-facture-btn:hover {
        background-color: #4c4;
    }

    /* Improve link styles */
    a {
        color: #4285f4;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Facture Buttons Styles */
    .facture-buttons {
        text-align: center;
        margin-top: 20px;
    }

    .facture-buttons button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        margin-right: 10px;
    }
    .content-container {
            display: flex; /* Use flexbox to arrange content */
            justify-content: space-between; /* Space between the two divs */
        }

        .directeur_side_bar_content,
        .main_content {
            flex: 1; /* Let both divs take equal space */
            padding: 10px; /* Add padding for spacing */
        }
        .content-container {
            display: flex; /* Use flexbox to arrange content */
            justify-content: space-between; /* Space between the two divs */
        }

        
        .directeur_side_bar_content,
        .main_content {
            flex: 1; /* Let both divs take equal space */
            padding: 70px 10px; /* Add padding for spacing */
            max-width: calc(100% - 250px); /* Adjust as needed */
        }
</style>

</head>

<body>
<div class="content-container">
<div class="directeur_side_bar_content">
    <?php
  include 'admin_side_bar.php';    ?>
    </div>
    <div class="main_content">
    <h1>clients factures </h1>

    <!-- Search form -->
    <form class="search-client-form" method="get">
        <label for="search">Référence ou Facture ID:</label>
        <input type="text" id="search" name="search" class="search-client-input" placeholder="Rechercher...">
        <button type="submit" class="search-client-btn"><i class="fas fa-search"></i> Rechercher</button>
    </form>

    <table>
        <tr>
            <th>ID Facture</th>
            <th>Référence</th>
            <th>Date de transaction</th>
            <th>Mode de paiement</th>
            <th>Numéro de chèque</th>
            <th>Assurance</th>
            <th>operation de chirgurie</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>montant (DH)</th>
            <th> facture</th>
        </tr>
        <?php
        $total_solde = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["facture_p_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ref"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_facture"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["mode_paiement"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["numero_cheque"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["assurance_intitule"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["libelle"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["prix_unitaire"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["solde"]) . "</td>";
                // Informations de facture
                echo "<td>";
                echo "<a href='generate_factura.php?facture_p_id=" . $row["facture_p_id"] . "'>Réaliser une facture</a><br>";
                // You can add more information here if needed
                echo "</td>";
                echo "</tr>";

                $total_solde += $row["solde"];
            }
        } else {
            echo "<tr><td colspan='11'>Aucune facture trouvée.</td></tr>";
        }
        ?>
    </table>

    <div class="total-solde">Solde total:
        <?php echo number_format($total_solde, 2); ?> <span>(DH)</span>
    </div>
    <div class="facture-buttons">
    <button onclick="printFacture()"><i class="fas fa-print"></i> Imprimer</button>

</div>
<script>
        function printFacture() {
            window.print();
        }
    </script>
    </div>
    </div>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>

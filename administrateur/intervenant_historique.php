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

$sql_intervenants = "SELECT intervenant_id, nom, prenom FROM intervenant";
$result_intervenants = $conn->query($sql_intervenants);

// Check for errors in the intervenants query
if (!$result_intervenants) {
    die("Error executing the intervenants query: " . $conn->error);
}
// Function to delete a transaction from the "facture_f" and "detail_f" tables
if (isset($_GET["delete"]) && $_GET["delete"] == "transaction" && isset($_GET["facture_i_id"])) {
    $facture_f_id = $_GET["facture_i_id"];
    $sql_delete = "DELETE FROM detail_i WHERE facture_i_id = '$facture_f_id';DELETE FROM facture_i WHERE facture_i_id = '$facture_i_id'";
    if ($conn->multi_query($sql_delete) === TRUE) {
        // Redirect to the same page to display the updated table
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Retrieve transaction history data from the "facture_i," "produit," "intervenant," and "cheque" tables
$search_ref = isset($_GET['search_ref']) ? $_GET['search_ref'] : '';
$sql = "SELECT fi.facture_i_id, fi.date_facture, fi.mode_paiement, c.numero_cheque, i.nom AS intervenant_nom, i.prenom AS intervenant_prenom, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde
        FROM facture_i fi
        INNER JOIN detail_i di ON fi.facture_i_id = di.facture_i_id
        INNER JOIN produit p ON di.produit_id = p.produit_id
        INNER JOIN intervenant i ON fi.intervenant_id = i.intervenant_id
        LEFT JOIN cheque c ON fi.cheque_id = c.cheque_id
        WHERE p.ref LIKE '%$search_ref%'
        ORDER BY fi.date_facture DESC, p.ref";

// Execute the main SQL query
$result = $conn->query($sql);

// Check for errors in the main query
if (!$result) {
    die("Error executing the main SQL query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Historique des transactions intervenants</title>
    <style>
        /* Common Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

    
        .content-container {
            display: flex;
        }

        .directeur_side_bar_content {
            width: 20%;
            min-width: 200px;
            background-color: #f2f2f2;
            padding: 20px;
            box-sizing: border-box;
        }

        .main_content {
            flex: 1;
            padding: 100px 0px; /* Adjust padding for top and sides */
            box-sizing: border-box;
            max-width: calc(100% - 250px); /* Adjust as needed */
        }

        h1 {
            margin: 0;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
            max-width: 230px;
        }

        th {
            background-color: #f2f2f2;
            font-size: 16px;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        /* Search Form Styles */
        .search-intervenant-form {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            max-width: 400px;
        }

        .search-intervenant-form label,
        .search-intervenant-form input[type="text"],
        .search-intervenant-form input[type="submit"] {
            margin-left: 5px;
            margin-right: 5px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-intervenant-form input[type="text"] {
            flex: 1;
        }

        /* Add Transaction Form Styles */
        .add-transaction-form {
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .add-transaction-form h2 {
            margin-top: 0;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .add-transaction-form label,
        .add-transaction-form input[type="text"],
        .add-transaction-form input[type="number"],
        .add-transaction-form input[type="date"],
        .add-transaction-form select,
        .add-transaction-form input[type="submit"] {
            margin-bottom: 10px;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .add-transaction-form select {
            width: 100%;
        }

        /* Styling for Buttons */
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #FF0000;
        }

        .btn-delete:hover {
            background-color: #ff0000;
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

        @media screen and (max-width: 768px) {
            /* Common adjustments for smaller screens */
            body {
                margin: 10px;
            }

            h1 {
                font-size: 24px;
            }

            /* Styles for the transaction table */
            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }

            /* Styles for the add transaction form */
            .add-transaction-form {
                padding: 10px;
            }

            .add-transaction-form h2 {
                font-size: 18px;
            }

            .add-transaction-form label,
            .add-transaction-form input,
            .add-transaction-form select {
                margin-bottom: 5px;
                padding: 5px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="content-container">
<div class="directeur_side_bar_content">
            <?php
  include 'admin_side_bar.php';            ?>
        </div>
        <div class="main_content">
    <h1>Historique des transactions intervenants</h1>

    <!-- Search form -->
    <form class="search-intervenant-form" method="get" action="intervenant_historique.php">
        <label for="search_ref">Référence:</label>
        <input type="text" id="search_ref" name="search_ref" placeholder="Rechercher...">
        <input type="submit" value="Rechercher" class="btn">
    </form>

    <table>
        <tr>
            <th>Date transaction</th>
            <th>Référence</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Intervenant Nom</th>
            <th>Intervenant Prénom</th>
            <th>Mode de paiement</th>
            <th>Numéro de chèque</th>
            <th>Solde (DH)</th>
            <th>Actions</th>
        </tr>
        <?php
        $total_solde = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date_facture = htmlspecialchars($row['date_facture']);
                $mode_paiement = htmlspecialchars($row['mode_paiement']);
                $numero_cheque = htmlspecialchars($row['numero_cheque']);
                $intervenant_nom = htmlspecialchars($row['intervenant_nom']);
                $intervenant_prenom = htmlspecialchars($row['intervenant_prenom']);
                $ref = htmlspecialchars($row['ref']);
                $libelle = htmlspecialchars($row['libelle']);
                $quantite = htmlspecialchars($row['quantite']);
                $prix_unitaire = htmlspecialchars($row['prix_unitaire']);
                $solde = $quantite * $prix_unitaire;
                $total_solde += $solde;
                echo "<tr>";
                echo "<td>$date_facture</td>";
                echo "<td>$ref</td>";
                echo "<td>$libelle</td>";
                echo "<td>$quantite</td>";
                echo "<td>$prix_unitaire</td>";
                echo "<td>$intervenant_nom</td>";
                echo "<td>$intervenant_prenom</td>";
                echo "<td>$mode_paiement</td>";
                echo "<td>$numero_cheque</td>";
                echo "<td>$solde</td>";
                echo "<td class='actions'>";
                echo '<a href="edit_intervenant_transaction.php?facture_i_id=' . $row["facture_i_id"] . '" class="btn" style="background-color: #009688;"><span>&#9998;</span></a>';
                echo ' | ';
                echo '<a href="intervenant_historique.php?delete=transaction&facture_i_id=' . $row["facture_i_id"] . '" onclick="return confirm(\'Voulez-vous supprimer cette transaction ?\')" class="btn btn-delete"><span>&#128465;</span></a>';
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='11'>Aucune transaction trouvée.</td></tr>";
        }
        ?>
    </table>

    <div class="total-solde">
        Solde total : <span>
        <?php echo number_format($total_solde, 2); ?> <span>(DH)</span>        </span>
    </div>

    <!-- Add a new transaction form -->
    <div class="add-transaction-form">
        <h2>Ajouter une nouvelle transaction</h2>
        <form action="add_intervenant_transaction.php" method="post">
            <label for="date_facture">Date Facture:</label>
            <input type="date" id="date_facture" name="date_facture" required>

            <label for="mode_paiement">Mode de paiement:</label>
            <select id="mode_paiement" name="mode_paiement" required onchange="toggleFields()">
                <option value="Cash">Cash</option>
                <option value="Cheque">Cheque</option>
            </select>

            <div id="chequeField" style="display:none;">
                <label for="numero_cheque">Numéro de chèque:</label>
                <input type="text" id="numero_cheque" name="numero_cheque">
            </div>

            <label for="intervenant_id">Intervenant:</label>
            <select id="intervenant_id" name="intervenant_id" required>
                <?php
                if ($result_intervenants->num_rows > 0) {
                    while ($intervenant = $result_intervenants->fetch_assoc()) {
                        $intervenant_id = $intervenant['intervenant_id'];
                        $intervenant_nom = htmlspecialchars($intervenant['nom']);
                        $intervenant_prenom = htmlspecialchars($intervenant['prenom']);
                        echo "<option value='$intervenant_id'>$intervenant_nom $intervenant_prenom</option>";
                    }
                } else {
                    echo "<option disabled>Aucun intervenant disponible</option>";
                }
                ?>
            </select>

            <label for="ref">Référence:</label>
            <input type="text" id="ref" name="ref" required>

            <label for="libelle">Libellé:</label>
            <input type="text" id="libelle" name="libelle" required>

            <label for="quantite">Quantité:</label>
            <input type="number" id="quantite" name="quantite" required>

            <label for="prix_unitaire">Prix unitaire:</label>
            <input type="number" id="prix_unitaire" name="prix_unitaire" required>

            <input type="submit" value="Ajouter" class="btn" style="background-color: #2196F3;">
        </form>
    </div>
    </div>
    </div>
    <script>
        function toggleFields() {
            var modePaiement = document.getElementById("mode_paiement").value;
            var chequeField = document.getElementById("chequeField");
            if (modePaiement === "Cheque") {
                chequeField.style.display = "block";
            } else {
                chequeField.style.display = "none";
            }
        }
    </script>
</body>

</html>


<?php
// Close the database connection
$conn->close();
?>

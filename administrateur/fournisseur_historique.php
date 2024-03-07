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

// Function to delete a transaction from the "facture_f" and "detail_f" tables
if (isset($_GET["delete"]) && $_GET["delete"] == "transaction" && isset($_GET["facture_f_id"])) {
    $facture_f_id = $_GET["facture_f_id"];
    $sql_delete = "DELETE FROM detail_f WHERE facture_f_id = '$facture_f_id';DELETE FROM facture_f WHERE facture_f_id = '$facture_f_id'";
    if ($conn->multi_query($sql_delete) === TRUE) {
        // Redirect to the same page to display the updated table
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Retrieve transaction history data from the "facture_f," "cheque," and "produit" tables
$search_ref = isset($_GET['search_ref']) ? $_GET['search_ref'] : '';
$sql = "SELECT f.facture_f_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde, fo.nom AS fournisseur_nom, fo.prenom AS fournisseur_prenom
        FROM facture_f f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_f df ON f.facture_f_id = df.facture_f_id
        INNER JOIN produit p ON df.produit_id = p.produit_id
        LEFT JOIN fournisseur fo ON f.fournisseur_id = fo.fournisseur_id
        WHERE p.ref LIKE '%$search_ref%'
        ORDER BY f.date_facture DESC, p.ref"; // Changed the order by column
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Historique des transactions fournisseurs</title>
    <style>
        /* Common Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            margin-bottom: 20px;
            text-align: center;
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

        .actions {
            display: flex;
            justify-content: space-between;
        }

        /* Search Form Styles */
        .search-fournisseur-form {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            max-width: 400px;
        }

        .search-fournisseur-form label,
        .search-fournisseur-form input[type="text"],
        .search-fournisseur-form input[type="submit"] {
            margin-left: 5px;
            margin-right: 5px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-fournisseur-form input[type="text"] {
            flex: 1;
        }

        /* Add Transaction Form Styles */
        .add-transaction-form {
            border: 1px solid #ccc;
            padding: 20px;
            width: 60%;
            background-color: #f9f9f9;
            padding-left:200px;
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
            /* Red color */
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

        .content-container {
            display: flex;
            justify-content: space-between;
        }

        .directeur_side_bar_content,
        .main_content {
            flex: 1;
            padding: 10px;
        }

        .content-container {
            display: flex;
            /* Use flexbox to arrange content */
            justify-content: space-between;
            /* Space between the two divs */
        }

        .directeur_side_bar_content,
        .main_content {
            flex: 1;
            /* Let both divs take equal space */
            padding: 80px 0px;
            /* Add padding for spacing */
            max-width: calc(100% - 250px); /* Adjust as needed */

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
            <h1>fournisseurs</h1>

            <!-- Search form -->
            <form class="search-fournisseur-form" method="get" action="fournisseur_historique.php">
                <label for="search_ref">Référence:</label>
                <input type="text" id="search_ref" name="search_ref" placeholder="Rechercher...">
                <input type="submit" value="Rechercher" class="btn">
            </form>

            <table>
                <tr>
                    <th>Date transaction</th>
                    <th>Référence</th> <!-- Changed the order of the columns -->
                    <th>Mode de paiement</th>
                    <th>Numéro de chèque</th>
                    <th>Fournisseur Nom</th>
                    <th>Fournisseur Prénom</th>
                    <th>produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
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
                        $fournisseur_nom = htmlspecialchars($row['fournisseur_nom']);
                        $fournisseur_prenom = htmlspecialchars($row['fournisseur_prenom']);
                        $ref = htmlspecialchars($row['ref']);
                        $libelle = htmlspecialchars($row['libelle']);
                        $quantite = htmlspecialchars($row['quantite']);
                        $prix_unitaire = htmlspecialchars($row['prix_unitaire']);
                        $solde = $quantite * $prix_unitaire;
                        $total_solde += $solde;
                        echo "<tr>";
                        echo "<td>$date_facture</td>";
                        echo "<td>$ref</td>";
                        echo "<td>$mode_paiement</td>";
                        echo "<td>$numero_cheque</td>";
                        echo "<td>$fournisseur_nom</td>";
                        echo "<td>$fournisseur_prenom</td>";
                        echo "<td>$libelle</td>";
                        echo "<td>$quantite</td>";
                        echo "<td>$prix_unitaire</td>";
                        echo "<td>$solde</td>";
                        echo "<td class='actions'>";
                        echo '<a href="edit_fournisseur_transaction.php?facture_f_id=' . $row["facture_f_id"] . '" class="btn" style="background-color: #009688;"><span>&#9998;</span></a>';
                        echo ' | ';
                        echo '<a href="fournisseur_historique.php?delete=transaction&facture_f_id=' . $row["facture_f_id"] . '" onclick="return confirm(\'Voulez-vous supprimer cette transaction ?\')" class="btn btn-delete"><span>&#128465;</span></a>';
                        
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
                    <?php echo number_format($total_solde, 2); ?> <span>(DH)</span>
                </span>
            </div>

            <!-- Add a new transaction form -->
            <div class="add-transaction-form">
                <h2>Ajouter une nouvelle transaction</h2>
                <form action="add_fournisseur_transaction.php" method="post">
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

                    <label for="fournisseur_id">Fournisseur:</label>
                    <select id="fournisseur_id" name="fournisseur_id" required>
                        <?php
                        $sql_fournisseurs = "SELECT fournisseur_id, nom, prenom FROM fournisseur";
                        $result_fournisseurs = $conn->query($sql_fournisseurs);
                        if ($result_fournisseurs->num_rows > 0) {
                            while ($fournisseur = $result_fournisseurs->fetch_assoc()) {
                                $fournisseur_id = $fournisseur['fournisseur_id'];
                                $fournisseur_nom = htmlspecialchars($fournisseur['nom']);
                                $fournisseur_prenom = htmlspecialchars($fournisseur['prenom']);
                                echo "<option value='$fournisseur_id'>$fournisseur_nom $fournisseur_prenom</option>";
                            }
                        } else {
                            echo "<option disabled>Aucun fournisseur disponible</option>";
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
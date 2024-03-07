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

// Function to delete a transaction from the "facture_i" and "detail_i" tables
if (isset($_GET["delete"]) && $_GET["delete"] == "transaction" && isset($_GET["facture_i_id"])) {
    $facture_i_id = $_GET["facture_i_id"];
    $sql_delete = "DELETE FROM detail_i WHERE facture_i_id = '$facture_i_id';DELETE FROM facture_i WHERE facture_i_id = '$facture_i_id'";
    if ($conn->multi_query($sql_delete) === TRUE) {
        // Redirect to the same page to display the updated table
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Retrieve transaction history data from the "facture_i," "cheque," and "produit" tables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT f.facture_i_id, f.date_facture, f.mode_paiement, c.numero_cheque, i.nom AS intervenant_nom, i.prenom AS intervenant_prenom, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde
        FROM facture_i f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_i df ON f.facture_i_id = df.facture_i_id
        INNER JOIN produit p ON df.produit_id = p.produit_id
        LEFT JOIN intervenant i ON f.intervenant_id = i.intervenant_id
        WHERE p.ref LIKE '%$search%' OR f.facture_i_id = '$search'
        ORDER BY f.date_facture DESC, p.ref"; // Changed the order by column
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Historique des transactions intervenants</title>
    <style>
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
        .search-intervenant-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-intervenant-form label {
            font-weight: bold;
            margin-right: 10px;
        }

        .search-intervenant-input {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-intervenant-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Facture Link Styles */
        .facture a {
            display: inline-block;
            padding: 6px 10px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .facture a:hover {
            background-color: #0056b3;
        }

        /* Centered Title */
        h1 {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-align: center;
          
            font-size: 28px;
            margin-bottom: 20px;
        }
        

        .facture-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .facture-buttons button {
            background-color: #4CAF50;
            /* Green color, you can change it to any color you like */
            color: white;
            /* Text color */
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
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
            padding: 80px 100px; /* Add padding for spacing */
            max-width: calc(100% - 250px); /* Adjust as needed */
        }
    </style>
</head>

<body>
    <div class="content-container">
        <div class="directeur_side_bar_content">
            <?php
  include 'admin_side_bar.php';
  ?>
        </div>
        <div class="main_content">
            <h1>Intervenants factures</h1>

            <!-- Search form -->
            <form class="search-intervenant-form" method="get">
                <label for="search">Référence ou Facture ID:</label>
                <input type="text" id="search" name="search" class="search-intervenant-input" placeholder="Rechercher...">
                <button type="submit" class="search-intervenant-btn"><i class="fas fa-search"></i> Rechercher</button>
            </form>
            <table>
                <tr>
                    <th>Facture ID</th>
                    <th>Date facture</th>
                    <th>Référence</th>
                    <th>Mode de paiement</th>
                    <th>Numéro de chèque</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>montant (DH)</th>
                    <th>Facture</th>
                </tr>
                <?php
                $total_solde = 0;
                if ($result) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $facture_i_id = htmlspecialchars($row['facture_i_id']);
                            $date_facture = htmlspecialchars($row['date_facture']);
                            $mode_paiement = htmlspecialchars($row['mode_paiement']);
                            $numero_cheque = htmlspecialchars($row['numero_cheque']);
                            $ref = htmlspecialchars($row['ref']);
                            $libelle = htmlspecialchars($row['libelle']);
                            $quantite = htmlspecialchars($row['quantite']);
                            $prix_unitaire = htmlspecialchars($row['prix_unitaire']);
                            $solde = htmlspecialchars($row['solde']);
                            echo "<tr>";
                            echo "<td>$facture_i_id</td>";
                            echo "<td>$date_facture</td>";
                            echo "<td>$ref</td>";
                            echo "<td>$mode_paiement</td>";
                            echo "<td>$numero_cheque</td>";
                            echo "<td>$libelle</td>";
                            echo "<td>$quantite</td>";
                            echo "<td>$prix_unitaire</td>";
                            echo "<td>$solde</td>";
                            echo "<td class='facture'>";
                            echo '<a href="generate_facturc.php?facture_i_id=' . $facture_i_id . '" class="btn">Réaliser Facture</a>';
                            echo "</td>";
                            echo "</tr>";
                            $total_solde += $row["solde"];
                        }
                    } else {
                        echo "<tr><td colspan='10'>Aucune facture trouvée.</td></tr>";
                    }
                } else {
                    echo "Error: " . $conn->error;
                }
                ?>
            </table>
            <div class="total-solde">
                Total Solde: <span>
                    <?php echo number_format($total_solde, 2); ?> <span>(DH)</span>
                </span>
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
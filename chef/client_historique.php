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
$search_ref = isset($_GET['search_ref']) ? $_GET['search_ref'] : '';
$sql = "SELECT f.facture_p_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde, pa.intitule AS assurance_intitule
        FROM facture_p f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_p dp ON f.facture_p_id = dp.facture_p_id
        INNER JOIN produit p ON dp.produit_id = p.produit_id
        LEFT JOIN patient pt ON f.patient_id = pt.patient_id
        LEFT JOIN assurance pa ON pt.assurance_id = pa.assurance_id
        WHERE p.ref LIKE '%$search_ref%'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Historique des transactions clients</title>
    <style>
        /* Common Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;


        }

        h1 {
            text-align: center;
            font-weight: bold;
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
            max-width: 230px;
    word-wrap: break-word; 
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
        .search-bar-form {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            max-width: 400px;
        }

        .search-bar-form label,
        .search-bar-form input[type="text"],
        .search-bar-form input[type="submit"] {
            margin-left: 5px;
            margin-right: 5px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-bar-form input[type="text"] {
            flex: 1;
        }

        /* Add Transaction Form Styles */
        .add-transaction-form {
            border: 1px solid #ccc;
            padding: 20px;
            width: 60%;
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

            th,
            td {
                padding: 8px;
            }

            /* Styles for the add transaction form */
            .add-transaction-form {
                width: 100%;
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
    include 'chef_side_bar.php';
    ?>
    </div>
    <div class="main_content">
        <h1>clients</h1>

        <!-- Search form -->
        <form class="search-bar-form" method="get" action="client_historique.php">
            <label for="search_ref">Référence:</label>
            <input type="text" id="search_ref" name="search_ref" placeholder="Rechercher...">
            <input type="submit" value="Rechercher" class="btn">
        </form>

        <table>
            <tr>
                <th>Date de transaction</th>
                <th>Mode de paiement</th>
                <th>Numéro de chèque</th>
                <th>Assurance</th>
                <th>Référence</th>
                <th>operation de chirgurie</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Solde (DH)</th>
                <th>Actions</th>
            </tr>
            <?php
            $total_solde = 0;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["date_facture"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["mode_paiement"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["numero_cheque"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["assurance_intitule"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["ref"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["libelle"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["prix_unitaire"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["solde"]) . "</td>";
                    echo '<td class="actions">';
                    echo '<a href="edit_client_transaction.php?facture_p_id=' . $row["facture_p_id"] . '" class="btn" style="background-color: #009688;"><span>&#9998;</span></a>';
                    echo ' | ';
                    echo '<a href="client_historique.php?delete=transaction&facture_p_id=' . $row["facture_p_id"] . '" onclick="return confirm(\'Voulez-vous supprimer cette transaction ?\')" class="btn btn-delete"><span>&#128465;</span></a>';
                    echo "</td>";
                    echo "</tr>";

                    $total_solde += $row["solde"];
                }
            } else {
                echo "<tr><td colspan='10'>Aucune transaction trouvée.</td></tr>";
            }
            ?>
        </table>

        <div class="total-solde">Solde total:
            <?php echo number_format($total_solde, 2); ?> <span>(DH)</span>
        </div>

        <!-- Add a new transaction form -->
        <div class="add-transaction-form">
            <h2>Ajouter une nouvelle transaction</h2>
            <form action="add_client_transaction.php" method="post">
                <form action="add_client_transaction.php" method="post">
                    <label for="patient_id">Patient:</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Select a patient</option>
                        <?php
                        // Fetch patient data from the "patient" table and populate the dropdown select
                        $sql_patient = "SELECT * FROM patient";
                        $result_patient = $conn->query($sql_patient);
                        while ($row_patient = $result_patient->fetch_assoc()) {
                            echo '<option value="' . $row_patient['patient_id'] . '">' . htmlspecialchars($row_patient['nom']) . ' ' . htmlspecialchars($row_patient['prenom']) . '</option>';
                        }
                        ?>
                    </select>

                    </select>
                    <label for="date_facture">Date:</label>
                    <input type="date" id="date_facture" name="date_facture" required>

                    <label for="mode_paiement">Mode de paiement:</label>
                    <select id="mode_paiement" name="mode_paiement" required onchange="toggleFields()">
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Traites">Traites</option>
                        <option value="Mutuelle">Mutuelle</option>
                    </select>

                    <div id="chequeField" style="display:none;">
                        <label for="numero_cheque">Numéro de chèque:</label>
                        <input type="text" id="numero_cheque" name="numero_cheque">
                    </div>

                    <div id="assuranceField" style="display:none;">
                        <label for="assurance_id">Assurance:</label>
                        <select id="assurance_id" name="assurance_id">
                            <?php
                            // Fetch assurance data from the "assurance" table and populate the dropdown select
                            $sql_assurance = "SELECT * FROM assurance";
                            $result_assurance = $conn->query($sql_assurance);
                            while ($row_assurance = $result_assurance->fetch_assoc()) {
                                echo '<option value="' . $row_assurance['assurance_id'] . '">' . htmlspecialchars($row_assurance['intitule']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

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
            var modePaiementInput = document.getElementById('mode_paiement');
            var chequeField = document.getElementById('chequeField');
            var assuranceField = document.getElementById('assuranceField');
            if (modePaiementInput.value === 'Cheque') {
                chequeField.style.display = 'block';
                assuranceField.style.display = 'none';
            } else if (modePaiementInput.value === 'Mutuelle') {
                assuranceField.style.display = 'block';
                chequeField.style.display = 'none';
            } else {
                chequeField.style.display = 'none';
                assuranceField.style.display = 'none';
            }
        }
    </script>
  
</body>

</html>


<?php
// Close the database connection
$conn->close();
?>
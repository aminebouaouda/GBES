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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $facture_f_id = $_POST["facture_f_id"];
    $date_facture = $_POST["date_facture"];
    $ref = $_POST["ref"];
    $libelle = $_POST["libelle"];
    $quantite = $_POST["quantite"];
    $prix_unitaire = $_POST["prix_unitaire"];
    $solde = $_POST["solde"];
    $mode_paiement = $_POST["mode_paiement"];
    $numero_cheque = $_POST["numero_cheque"];

    // Calculate the "Solde" automatically based on "Quantité" and "Prix unitaire"
    $solde = $quantite * $prix_unitaire;

    // Update the "cheque" data in the "cheque" table
    $sql_update_cheque = "UPDATE cheque SET numero_cheque = '$numero_cheque' WHERE cheque_id = (SELECT cheque_id FROM facture_f WHERE facture_f_id = '$facture_f_id')";
    if ($conn->query($sql_update_cheque) === TRUE) {
        // Update the "cheque_id" in the "facture_f" table
        $sql_update_facture_cheque = "UPDATE facture_f SET cheque_id = (SELECT cheque_id FROM cheque WHERE cheque_id = (SELECT cheque_id FROM facture_f WHERE facture_f_id = '$facture_f_id')) WHERE facture_f_id = '$facture_f_id'";
        if ($conn->query($sql_update_facture_cheque) === TRUE) {
            // Next, update the transaction data in the "facture_f" table
            $sql_update_facture = "UPDATE facture_f SET date_facture = '$date_facture', mode_paiement = '$mode_paiement' WHERE facture_f_id = '$facture_f_id'";
            if ($conn->query($sql_update_facture) === TRUE) {
                // Update the details of the transaction in the "produit" table
                $sql_update_produit = "UPDATE produit SET ref = '$ref', libelle = '$libelle', quantite = '$quantite', prix_unitaire = '$prix_unitaire', solde = '$solde' WHERE produit_id = (SELECT produit_id FROM detail_f WHERE facture_f_id = '$facture_f_id')";
                if ($conn->query($sql_update_produit) === TRUE) {
                    // Transaction successfully updated, redirect back to the transaction history page
                    header("Location: fournisseur_historique.php");
                    exit();
                } else {
                    echo "Error: " . $sql_update_produit . "<br>" . $conn->error;
                }
            } else {
                echo "Error: " . $sql_update_facture . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql_update_facture_cheque . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_update_cheque . "<br>" . $conn->error;
    }
}

// Retrieve the transaction data for editing
if (isset($_GET["facture_f_id"])) {
    $facture_f_id = $_GET["facture_f_id"];

    // Get the transaction data from the database
    $sql = "SELECT f.facture_f_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde
            FROM facture_f f
            LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
            INNER JOIN detail_f df ON f.facture_f_id = df.facture_f_id
            INNER JOIN produit p ON df.produit_id = p.produit_id
            WHERE f.facture_f_id = '$facture_f_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Assign transaction data to variables for pre-filling the form fields
        $date_facture = $row["date_facture"];
        $ref = $row["ref"];
        $libelle = $row["libelle"];
        $quantite = $row["quantite"];
        $prix_unitaire = $row["prix_unitaire"];
        $solde = $row["solde"];
        $mode_paiement = $row["mode_paiement"];
        $numero_cheque = $row["numero_cheque"];
    } else {
        // If no transaction is found with the given facture_f_id, redirect back to the transaction history page
        header("Location: fournisseur_historique.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Transaction</title>
    <!-- CSS and other styles remain unchanged -->
    <style>
        /* Common Styles (same as before) */
        /* ... */

        /* Edit Transaction Form Styles */
        .edit-transaction-form {
            border: 1px solid #ccc;
            padding: 20px;
            width: 60%;
            background-color: #f9f9f9;
        }

        .edit-transaction-form h2 {
            margin-top: 0;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .edit-transaction-form label,
        .edit-transaction-form input[type="text"],
        .edit-transaction-form input[type="number"],
        .edit-transaction-form input[type="date"],
        .edit-transaction-form select,
        .edit-transaction-form input[type="submit"] {
            margin-bottom: 10px;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-transaction-form select {
            width: 100%;
        }

        /* Add assurance field styles */
        .edit-transaction-form #assuranceField {
            display: none;
        }

        .edit-transaction-form .btn-cancel {
            background-color: #F44336;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-transaction-form .btn-cancel:hover {
            background-color: #D32F2F;
        }

        /* Submit Button Styles */
        .edit-transaction-form .btn-submit {
            background-color: #2196F3;
            color: white;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Edit Transaction</h1>
    <!-- Edit Transaction Form -->
    <div class="edit-transaction-form">
        <h2>Modifier la transaction</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="facture_f_id" value="<?php echo $facture_f_id; ?>">
            <label for="date_facture">Date opération:</label>
            <input type="date" id="date_facture" name="date_facture" value="<?php echo $date_facture; ?>" required>

            <label for="mode_paiement">Mode de paiement:</label>
            <select id="mode_paiement" name="mode_paiement" required onchange="toggleFields()">
                <option value="Cash" <?php if ($mode_paiement === "Cash") echo "selected"; ?>>Cash</option>
                <option value="Cheque" <?php if ($mode_paiement === "Cheque") echo "selected"; ?>>Cheque</option>
            </select>

            <div id="chequeField" <?php if ($mode_paiement !== "Cheque") echo 'style="display:none;"'; ?>>
                <label for="numero_cheque">Numéro de chèque:</label>
                <input type="text" id="numero_cheque" name="numero_cheque" value="<?php echo $numero_cheque; ?>">
            </div>


            <label for="ref">Référence:</label>
            <input type="text" id="ref" name="ref" value="<?php echo $ref; ?>" required>

            <label for="libelle">Libellé:</label>
            <input type="text" id="libelle" name="libelle" value="<?php echo $libelle; ?>" required>

            <label for="quantite">Quantité:</label>
            <input type="number" id="quantite" name="quantite" value="<?php echo $quantite; ?>" required>

            <label for="prix_unitaire">Prix unitaire:</label>
            <input type="number" id="prix_unitaire" name="prix_unitaire" value="<?php echo $prix_unitaire; ?>" required>

            <label for="solde">Solde (DH):</label>
            <input type="number" id="solde" name="solde" value="<?php echo $solde; ?>" required readonly>

            
            <input type="submit" value="Sauvegarder" class="btn-submit" style="background-color: #2196F3;">
            <a href="fournisseur_historique.php" class="btn-cancel">Annuler</a>
        </form>
    </div>

    <script>
        function toggleFields() {
            var modePaiementInput = document.getElementById('mode_paiement');
            var chequeField = document.getElementById('chequeField');
            if (modePaiementInput.value === 'Cheque') {
                chequeField.style.display = 'block';
            } else {
                chequeField.style.display = 'none';
            }
        }
    </script>
</body>

</html>

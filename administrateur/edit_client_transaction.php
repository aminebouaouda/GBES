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
    $facture_p_id = $_POST["facture_p_id"];
    $date_facture = $_POST["date_facture"];
    $ref = $_POST["ref"];
    $libelle = $_POST["libelle"];
    $quantite = $_POST["quantite"];
    $prix_unitaire = $_POST["prix_unitaire"];
    $solde = $_POST["solde"];
    $mode_paiement = $_POST["mode_paiement"];
    $numero_cheque = $_POST["numero_cheque"];
    $assurance_intitule = $_POST["assurance_intitule"];

    // Calculate the "Solde" automatically based on "Quantité" and "Prix unitaire"
    $solde = $quantite * $prix_unitaire;

    // Update the "cheque" data in the "cheque" table
    $sql_update_cheque = "UPDATE cheque SET numero_cheque = '$numero_cheque' WHERE cheque_id = (SELECT cheque_id FROM facture_p WHERE facture_p_id = '$facture_p_id')";
    if ($conn->query($sql_update_cheque) === TRUE) {
        // Update the "cheque_id" in the "facture_p" table
        $sql_update_facture_cheque = "UPDATE facture_p SET cheque_id = (SELECT cheque_id FROM cheque WHERE cheque_id = (SELECT cheque_id FROM facture_p WHERE facture_p_id = '$facture_p_id')) WHERE facture_p_id = '$facture_p_id'";
        if ($conn->query($sql_update_facture_cheque) === TRUE) {
            // Next, update the transaction data in the "facture_p" table
            $sql_update_facture = "UPDATE facture_p SET date_facture = '$date_facture', mode_paiement = '$mode_paiement' WHERE facture_p_id = '$facture_p_id'";
            if ($conn->query($sql_update_facture) === TRUE) {
                // Update the details of the transaction in the "produit" table
                $sql_update_produit = "UPDATE produit SET ref = '$ref', libelle = '$libelle', quantite = '$quantite', prix_unitaire = '$prix_unitaire', solde = '$solde' WHERE produit_id = (SELECT produit_id FROM detail_p WHERE facture_p_id = '$facture_p_id')";
                if ($conn->query($sql_update_produit) === TRUE) {
                    // Handle assurance update separately (only if mode_paiement is "Mutuelle")
                    if ($mode_paiement === "Mutuelle") {
                        // Retrieve the assurance_id based on the assurance_intitule
                        $sql_assurance_id = "SELECT assurance_id FROM assurance WHERE intitule = '$assurance_intitule'";
                        $result_assurance_id = $conn->query($sql_assurance_id);
                        if ($result_assurance_id->num_rows == 1) {
                            $row_assurance_id = $result_assurance_id->fetch_assoc();
                            $assurance_id = $row_assurance_id["assurance_id"];

                            // Update the patient's assurance_id in the "patient" table
                            $sql_update_patient = "UPDATE patient SET assurance_id = '$assurance_id' WHERE patient_id = (SELECT patient_id FROM facture_p WHERE facture_p_id = '$facture_p_id')";
                            if ($conn->query($sql_update_patient) !== TRUE) {
                                echo "Error: " . $sql_update_patient . "<br>" . $conn->error;
                            }
                        } else {
                            echo "Error: Assurance not found.";
                        }
                    }

                    // Transaction successfully updated, redirect back to the transaction history page
                    header("Location: client_historique.php");
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
if (isset($_GET["facture_p_id"])) {
    $facture_p_id = $_GET["facture_p_id"];

    // Get the transaction data from the database, including assurance_intitule
    $sql = "SELECT f.facture_p_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde, pt.patient_id, pt.assurance_id
            FROM facture_p f
            LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
            INNER JOIN detail_p dp ON f.facture_p_id = dp.facture_p_id
            INNER JOIN produit p ON dp.produit_id = p.produit_id
            LEFT JOIN patient pt ON f.patient_id = pt.patient_id
            WHERE f.facture_p_id = '$facture_p_id'";
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
        $patient_id = $row["patient_id"];
        $assurance_id = $row["assurance_id"];

        // Retrieve the assurance intitule based on the patient_id
        $sql_assurance = "SELECT intitule AS assurance_intitule FROM assurance WHERE assurance_id = '$assurance_id'";
        $result_assurance = $conn->query($sql_assurance);
        if ($result_assurance->num_rows == 1) {
            $row_assurance = $result_assurance->fetch_assoc();
            $assurance_intitule = $row_assurance["assurance_intitule"];
        } else {
            $assurance_intitule = "";
        }
    } else {
        // If no transaction is found with the given facture_p_id, redirect back to the transaction history page
        header("Location: client_historique.php");
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
            <input type="hidden" name="facture_p_id" value="<?php echo $facture_p_id; ?>">
            <label for="date_facture">Date opération:</label>
            <input type="date" id="date_facture" name="date_facture" value="<?php echo $date_facture; ?>" required>
            <label for="ref">Réf:</label>
            <input type="text" id="ref" name="ref" value="<?php echo $ref; ?>" required>
            <label for="mode_paiement">Mode de paiement:</label>
            <select id="mode_paiement" name="mode_paiement" required onchange="toggleFields()">
                <option value="Cash" <?php if ($mode_paiement === "Cash")
                    echo "selected"; ?>>Cash</option>
                <option value="Cheque" <?php if ($mode_paiement === "Cheque")
                    echo "selected"; ?>>Cheque</option>
                <option value="Traites" <?php if ($mode_paiement === "Traites")
                    echo "selected"; ?>>Traites</option>
                <option value="Mutuelle" <?php if ($mode_paiement === "Mutuelle")
                    echo "selected"; ?>>Mutuelle</option>
            </select>

            <div id="chequeField" <?php if ($mode_paiement !== "Cheque")
                echo "style='display:none;'"; ?>>
                <label for="numero_cheque">Numéro de chèque:</label>
                <input type="text" id="numero_cheque" name="numero_cheque" value="<?php echo $numero_cheque; ?>">
            </div>

            <label for="libelle">Libellé:</label>
            <input type="text" id="libelle" name="libelle" value="<?php echo $libelle; ?>" required>
            <label for="quantite">Quantité:</label>
            <input type="number" id="quantite" name="quantite" value="<?php echo $quantite; ?>" required>
            <label for="prix_unitaire">Prix unitaire:</label>
            <input type="number" id="prix_unitaire" name="prix_unitaire" value="<?php echo $prix_unitaire; ?>" required>
            <label for="solde">Solde (DH):</label>
            <input type="number" id="solde" name="solde" value="<?php echo $solde; ?>" required>

            <!-- Add assurance field -->
            <div id="assuranceField" <?php if ($mode_paiement === "Mutuelle")
                echo "style='display:block;'"; ?>>
                <label for="assurance_intitule">Assurance:</label>
                <input type="text" id="assurance_intitule" name="assurance_intitule"
                    value="<?php echo $assurance_intitule; ?>">
            </div>

            <input type="submit" value="Enregistrer les modifications" class="btn-submit">
            <a href="client_historique.php" class="btn-cancel">Annuler</a>
        </form>
    </div>

 

    <!-- JavaScript (same as before) -->
    <script>
        /* ... (JavaScript as before) ... */
    </script>
</body>

</html>
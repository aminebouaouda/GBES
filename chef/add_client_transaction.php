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
    $date_facture = $_POST["date_facture"];
    $ref = $_POST["ref"];
    $libelle = $_POST["libelle"];
    $quantite = $_POST["quantite"];
    $prix_unitaire = $_POST["prix_unitaire"];
    $mode_paiement = $_POST["mode_paiement"];
    $numero_cheque = $_POST["numero_cheque"];
    $assurance_id = $_POST["assurance_id"];
    $patient_id = $_POST["patient_id"];

    // Calculate the "Solde" automatically based on "Quantité" and "Prix unitaire"
    $solde = $quantite * $prix_unitaire;

    // Check if the selected mode of payment is "Mutuelle" (assurance)
    if ($mode_paiement === 'Mutuelle') {
        if (empty($assurance_id)) {
            echo "Error: Please select an assurance.";
            exit();
        }

        // Check if the selected assurance_id exists in the patient table
        $sql_check_assurance = "SELECT assurance_id FROM assurance WHERE assurance_id = '$assurance_id' AND patient_id = '$patient_id'";
        $result = $conn->query($sql_check_assurance);

        if ($result->num_rows === 0) {
            echo "Error: Selected assurance is not valid for this patient.";
            exit();
        }

        // Insert the new transaction into the "facture_p" table with the patient_id and assurance_id
        $sql_insert_facture = "INSERT INTO facture_p (date_facture, mode_paiement, patient_id, assurance_id) 
                               VALUES ('$date_facture', '$mode_paiement', '$patient_id', '$assurance_id')";
    } else if ($mode_paiement === 'Cheque') {
        if (empty($numero_cheque)) {
            echo "Error: Please provide the cheque number.";
            exit();
        }

        // Since it's "Cheque," insert the "cheque" data into the "cheque" table
        $sql_insert_cheque = "INSERT INTO cheque (numero_cheque) VALUES ('$numero_cheque')";
        if ($conn->query($sql_insert_cheque) === TRUE) {
            // Get the last inserted ID (cheque_id) for the new cheque
            $cheque_id = $conn->insert_id;

            // Insert the new transaction into the "facture_p" table with the cheque_id and patient_id
            $sql_insert_facture = "INSERT INTO facture_p (date_facture, mode_paiement, patient_id, cheque_id) 
                                   VALUES ('$date_facture', '$mode_paiement', '$patient_id', '$cheque_id')";
        } else {
            echo "Error: " . $sql_insert_cheque . "<br>" . $conn->error;
            exit();
        }
    } else {
        // For other modes of payment (Cash, Traites), insert the new transaction into the "facture_p" table with the patient_id
        $sql_insert_facture = "INSERT INTO facture_p (date_facture, mode_paiement, patient_id) 
                               VALUES ('$date_facture', '$mode_paiement', '$patient_id')";
    }

    if ($conn->query($sql_insert_facture) === TRUE) {
        // Get the last inserted ID (facture_p_id) for the new transaction
        $facture_p_id = $conn->insert_id;

        // Insert the details of the transaction into the "produit" table
        $sql_insert_produit = "INSERT INTO produit (ref, libelle, quantite, prix_unitaire, solde) 
                              VALUES ('$ref', '$libelle', '$quantite', '$prix_unitaire', '$solde')";
        if ($conn->query($sql_insert_produit) === TRUE) {
            // Get the last inserted ID (produit_id) for the new produit
            $produit_id = $conn->insert_id;

            // Insert the relation between the facture_p and produit into the "detail_p" table
            $sql_insert_detail = "INSERT INTO detail_p (facture_p_id, produit_id) 
                                  VALUES ('$facture_p_id', '$produit_id')";
            if ($conn->query($sql_insert_detail) === TRUE) {
                // Transaction successfully added, redirect back to the transaction history page
                header("Location: client_historique.php");
                exit();
            } else {
                echo "Error: " . $sql_insert_detail . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql_insert_produit . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_insert_facture . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

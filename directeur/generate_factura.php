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

// Check if the transaction ID is provided in the URL
if (isset($_GET["facture_p_id"])) {
    $facture_p_id = $_GET["facture_p_id"];
    $transactionDetails = getTransactionDetails($conn, $facture_p_id);

    if ($transactionDetails === null) {
        // Redirect to the main page if the transaction ID is not found
        header("Location: client_facture.php");
        exit();
    }
} else {
    // Redirect to the main page if the transaction ID is not provided
    header("Location: client_facture.php");
    exit();
}

// Function to retrieve transaction details
function getTransactionDetails($conn, $facture_p_id)
{
    $sql = "SELECT f.facture_p_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde,
            pt.nom AS patient_nom, pt.prenom AS patient_prenom, pt.age AS patient_age, pt.email AS patient_email, pt.adresse AS patient_adresse, pt.telephone AS patient_telephone, pt.genre AS patient_genre, pt.photo AS patient_photo,
            a.intitule AS assurance_intitule
            FROM facture_p f
            LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
            INNER JOIN detail_p dp ON f.facture_p_id = dp.facture_p_id
            INNER JOIN produit p ON dp.produit_id = p.produit_id
            LEFT JOIN patient pt ON f.patient_id = pt.patient_id
            LEFT JOIN assurance a ON pt.assurance_id = a.assurance_id
            WHERE f.facture_p_id = '$facture_p_id'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            echo "No transaction found with ID: $facture_p_id";
            return null;
        }
    } else {
        echo "Error in SQL query: " . $conn->error;
        return null;
    }
}

// Calculate the delai (end date - start date + 7 days)
$date_debut = strtotime($transactionDetails["date_facture"]);
$delai = $date_debut + (7 * 24 * 60 * 60); // 7 days in seconds

// Function to generate PDF using TCPDF
function generatePDF($delai)
{
    define('PDF_DISABLE_IMAGE_SUPPORT', true);

    require('tcpdf/tcpdf.php');

    global $transactionDetails;

    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A3', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('GBES');
    $pdf->SetAuthor('GBES');
    $pdf->SetTitle('Facture N°: ' . $transactionDetails["facture_p_id"]);
    $pdf->SetSubject('Facture');

    // Add a page
    $pdf->AddPage();

    // Add facture content to the PDF using TCPDF's writeHTML function
    $pdf->writeHTML('
        <style>
            /* Your CSS styles here */
            /* Your CSS styles here */
/* Common Styles */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

/* Facture Styles */
.facture {
    border: 1px solid #000;
    padding: 20px;
    width: 80%;
    margin: 0 auto;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
}

/* Facture Header Styles */
.facture-header {
    position: relative; /* To allow absolute positioning of the logo */
}

.facture-header img {
    position: absolute; /* Position the logo absolutely within the header */
    top: 10px;
    left: 10px;
    width: 150px; /* Set the width of the logo */
    height: auto; /* Automatically adjust the height */
}

.facture-header h1 {
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center; /* Center the header title */
}

/* Facture Info Styles */
.facture-info {
    text-align: right; /* Align the info to the right */
    margin-top: 10px;
    float: right; /* Move the facture info to the other side */
}

.facture-info p {
    margin: 5px 0;
}

/* Client Info Styles */
.client-info {
    float: left;
    width: 50%;
    margin-right: 10px;
}

/* Transaction Table Styles */
table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 60px; /* Increased space between the table and the elements under it */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow to the table */
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

/* Total Montant Styles */
.total-montant {
    text-align: right;
    font-weight: bold;
    font-size: 16px;
    margin-top: 40px;
}

.total-montant span {
    font-size: 14px;
}

/* Footer Styles */
.facture-footer {
    text-align: right;
    font-size: 12px;
    margin-top: 40px;
}

/* Calculations Table Styles */
.calculations-table {
    width: 50%;
    margin: 0 auto;
    margin-top: 20px;
    border-collapse: collapse;
}

.calculations-table th,
.calculations-table td {
    border: 1px solid black;
    padding: 10px;
    text-align: left;
}

.calculations-table th {
    background-color: #f2f2f2;
    font-size: 16px;
    font-weight: bold;
}

.calculations-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* CSS for the divider line */
.divider {
    border: none;
    margin: 20px 0;
}

            
        </style>
        <div class="facture">
            <div class="facture-header">
                <img src="images/logo-color.png" alt="Your Company Logo">
                <h1>Facture N°: ' . $transactionDetails["facture_p_id"] . '</h1>
            </div>

            <div class="facture-info">
                <p><strong>Date de facture:</strong> ' . date('Y-m-d') . '</p>
                <p><strong>Nom de l\'entreprise:</strong> GBES</p>
                <p><strong>Adresse:</strong> 123 Rue fkih ben salah</p>
                <p><strong>Code Postal et Ville:</strong> 75001 fkih be salah</p>
                <p><strong>Numéro de téléphone:</strong> 01 23 45 67 89</p>
                <p><strong>Email:</strong> contact@votreentreprise.com</p>
            </div>

            <div class="client-info">
                <p><strong>Nom du client:</strong> ' . htmlspecialchars($transactionDetails["patient_nom"]) . '</p>
                <p><strong>Prénom du client:</strong> ' . htmlspecialchars($transactionDetails["patient_prenom"]) . '</p>
                <p><strong>Adresse:</strong> ' . htmlspecialchars($transactionDetails["patient_adresse"]) . '</p>
                <p><strong>Numéro de téléphone:</strong> ' . htmlspecialchars($transactionDetails["patient_telephone"]) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($transactionDetails["patient_email"]) . '</p>
            </div>

            <table>
                <tr>
                    <th>Référence</th>
                    <th>Opération</th>
                    <th>Quantité</th>
                    <th>Prix unitaire (HT)</th>
                    <th>TVA</th>
                    <th>Montant (DH)</th>
                </tr>
                <tr>
                    <td>' . htmlspecialchars($transactionDetails["ref"]) . '</td>
                    <td>' . htmlspecialchars($transactionDetails["libelle"]) . '</td>
                    <td>' . htmlspecialchars($transactionDetails["quantite"]) . '</td>
                    <td>' . htmlspecialchars($transactionDetails["prix_unitaire"]) . '</td>
                    <td>20%</td>
                    <td>' . ($transactionDetails["solde"] + (($transactionDetails["prix_unitaire"] * 20 * $transactionDetails["quantite"]) / 100)) . '</td>
                </tr>
            </table>
            <div class="total-montant">Montant total (HT): ' . number_format($transactionDetails["solde"], 2) . ' <span>(DH)</span></div>
            <div class="total-montant">Montant total (TVA): ' . number_format((($transactionDetails["prix_unitaire"] * 20 * $transactionDetails["quantite"]) / 100), 2) . ' <span>(DH)</span></div>
            <div class="total-montant">Montant total (TTC): ' . number_format(($transactionDetails["solde"] + (($transactionDetails["prix_unitaire"] * 20 * $transactionDetails["quantite"]) / 100)), 2) . ' <span>(DH)</span></div>
            <hr class="divider" style="clear: both;">
            <div class="total-montant">Net à Payer: ' . number_format(($transactionDetails["solde"] + (($transactionDetails["prix_unitaire"] * 20 * $transactionDetails["quantite"]) / 100)), 2) . ' <span>(DH)</span></div>

            <div class="facture-footer">
                <p>Mode de règlement: ' . htmlspecialchars($transactionDetails["mode_paiement"]) . '</p>
                <p>Conditions de règlement: À réception de facture</p>
                <p>Date limite de règlement: ' . date('Y-m-d', $delai) . '</p>
                <p>La loi n°92/1442 du 31 décembre 1992 nous fait l\'obligation de vous indiquer que le non-respect des conditions de paiement entraîne des intérêts de retard suivant les modalités et le taux défini par la loi. Une indemnité forfaitaire de 40€ sera due pour frais de recouvrement en cas de retard de paiement.</p>
            </div>
        </div>
    ');

    // Save the PDF with a filename
    $pdf->Output('facture.pdf', 'D');
}

// Check if the "Generer PDF" button is clicked
if (isset($_GET["generate_pdf"])) {
    // Call the generatePDF function if the 'generate_pdf' parameter is present in the URL
    generatePDF($delai);
    exit(); // Exit the script after generating the PDF to prevent further HTML output
}
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Facture</title>
    <style>
        /* Add your CSS styles here */

        /* Common Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        /* Facture Styles */
        .facture {
            border: 1px solid #000;
            padding: 20px;
            width: 80%;
            margin: 0 auto;
        }

        /* Facture Header Styles */
        .facture-header {
            text-align: center;
        }

        .facture-header img {
            max-width: 150px;
            float: left;
            /* Align the logo to the left */
        }

        .facture-header h1 {
            font-size: 24px;
            margin-bottom: 20px;
            display: inline-block;
            /* Center the header title */
        }

        /* Facture Info Styles */
        .facture-info {
            text-align: right;
            margin-top: 10px;
        }

        .facture-info p {
            margin: 5px 0;
        }

        /* Client Info Styles */
        .client-info {
            float: left;
            width: 50%;
        }

        /* Transaction Table Styles */
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

        /* Total Montant Styles */
        .total-montant {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
        }

        .total-montant span {
            font-size: 14px;
        }

        /* Footer Styles */
        .facture-footer {
            text-align: right;
            font-size: 12px;
            margin-top: 40px;
        }

        /* Calculations Table Styles */
        .calculations-table {
            width: 50%;
            margin: 0 auto;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .calculations-table th,
        .calculations-table td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        .calculations-table th {
            background-color: #f2f2f2;
            font-size: 16px;
            font-weight: bold;
        }

        .calculations-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* CSS for the divider line */
        .divider {
            border: none;
            height: 2px;
            background-color: #ccc;
            margin: 20px 0;
        }
        .facture-buttons {
        text-align: center;
        margin-top: 20px;
    }
    .facture-buttons button {
        background-color: #4CAF50; /* Green color, you can change it to any color you like */
        color: white; /* Text color */
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        margin-right: 10px;
    }

    /* Add this style to set the background color for the "Generer PDF" button */
    .facture-buttons a button {
        background-color: #f44336; /* Red color, you can change it to any color you like */
        color: white; /* Text color */
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="facture">
        <div class="facture-header">
            <img src="images/logo-color.png" alt="Your Company Logo">
            <h1>Facture N°:
                <?php echo $transactionDetails["facture_p_id"]; ?>
</h1>
</div>

<div class="facture-info">
    <p><strong>Date de facture:</strong>
        <?php echo date('Y-m-d'); ?>
    </p>
    <p><strong>Nom de l'entreprise:</strong> GBES</p>
    <p><strong>Adresse:</strong> 123 Rue fkih ben salah</p>
    <p><strong>Code Postal et Ville:</strong> 75001 fkih be salah</p>
    <p><strong>Numéro de téléphone:</strong> 01 23 45 67 89</p>
    <p><strong>Email:</strong> contact@votreentreprise.com</p>
</div>

<div class="client-info">
    <p><strong>Nom du client:</strong>
        <?php echo htmlspecialchars($transactionDetails["patient_nom"]); ?>
    </p>
    <p><strong>Prénom du client:</strong>
        <?php echo htmlspecialchars($transactionDetails["patient_prenom"]); ?>
    </p>
    <p><strong>Adresse:</strong>
        <?php echo htmlspecialchars($transactionDetails["patient_adresse"]); ?>
    </p>
    <p><strong>Numéro de téléphone:</strong>
        <?php echo htmlspecialchars($transactionDetails["patient_telephone"]); ?>
    </p>
    <p><strong>Email:</strong>
        <?php echo htmlspecialchars($transactionDetails["patient_email"]); ?>
    </p>
</div>

<table>
    <tr>
        <th>Référence</th>
        <th>Opération</th>
        <th>Quantité</th>
        <th>Prix unitaire (HT)</th>
        <th>TVA</th>
        <th>Montant (DH)</th>
    </tr>
    <?php
    $total_ht = 0;
    $total_tva = 0;
    $total_ttc = 0;
    $tva_percentage = 20; // 20% TVA constant
    echo "<tr>";
    echo "<td>" . htmlspecialchars($transactionDetails["ref"]) . "</td>";
    echo "<td>" . htmlspecialchars($transactionDetails["libelle"]) . "</td>";
    echo "<td>" . htmlspecialchars($transactionDetails["quantite"]) . "</td>";
    echo "<td>" . htmlspecialchars($transactionDetails["prix_unitaire"]) . "</td>";
    echo "<td>" . $tva_percentage . "%</td>";
    $tva_amount = ($transactionDetails["prix_unitaire"] * $tva_percentage * $transactionDetails["quantite"]) / 100;
    $total_ht += $transactionDetails["solde"];
    $total_tva += $tva_amount;
    $total_ttc += ($transactionDetails["solde"] + $tva_amount);
    echo "<td>" . ($transactionDetails["solde"] + $tva_amount) . "</td>";
    echo "</tr>";
    ?>
</table>
<?php
$net_a_payer = $total_ttc;
?>
<div class="total-montant">Montant total (HT):
    <?php echo number_format($total_ht, 2); ?> <span>(DH)</span>
</div>
<div class="total-montant">Montant total (TVA):
    <?php echo number_format($total_tva, 2); ?> <span>(DH)</span>
</div>
<div class="total-montant">Montant total (TTC):
    <?php echo number_format($total_ttc, 2); ?> <span>(DH)</span>
</div>
<hr class="divider" style="clear: both;">
<div class="total-montant">Net à Payer:
    <?php echo number_format($net_a_payer, 2); ?> <span>(DH)</span>
</div>

<div class="facture-footer">
    <p>Mode de règlement:
        <?php echo htmlspecialchars($transactionDetails["mode_paiement"]); ?>
    </p>
    <p>Conditions de règlement: À réception de facture</p>
    <p>Date limite de règlement:
        <?php echo date('Y-m-d', $delai); ?>
    </p>
    <p>La loi n°92/1442 du 31 décembre 1992 nous fait l'obligation de vous indiquer que le non-respect des
        conditions de paiement entraîne des intérêts de retard suivant les modalités et le taux défini par la
        loi. Une
        indemnité forfaitaire de 40€ sera due pour frais de recouvrement en cas de retard de paiement.</p>
</div>
</div>

<div class="facture-buttons">
    <button onclick="printFacture()"><i class="fas fa-print"></i> Imprimer</button>
    <a href="?facture_p_id=<?php echo $facture_p_id; ?>&generate_pdf=1">
        <button><i class="fas fa-file-pdf"></i> Generer PDF</button>
    </a>
</div>

<script>
    // Add any JavaScript code here if needed

    // Function to print the facture
    function printFacture() {
        window.print();
    }

</script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>
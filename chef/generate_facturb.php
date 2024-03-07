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
if (isset($_GET["facture_f_id"])) {
    $facture_f_id = $_GET["facture_f_id"];
    $transactionDetails = getTransactionDetails($conn, $facture_f_id);

    if ($transactionDetails === null) {
        // Redirect to the main page if the transaction ID is not found
        header("Location: fournisseur_facture.php");
        exit();
    }
} else {
    // Redirect to the main page if the transaction ID is not provided
    header("Location: fournisseur_facture.php");
    exit();
}

// Function to retrieve transaction details
function getTransactionDetails($conn, $facture_f_id)
{
    $sql = "SELECT f.facture_f_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde,
            fo.nom AS fournisseur_nom, fo.prenom AS fournisseur_prenom
            FROM facture_f f
            LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
            INNER JOIN detail_f df ON f.facture_f_id = df.facture_f_id
            INNER JOIN produit p ON df.produit_id = p.produit_id
            LEFT JOIN fournisseur fo ON f.fournisseur_id = fo.fournisseur_id
            WHERE f.facture_f_id = '$facture_f_id'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            echo "No transaction found with ID: $facture_f_id";
            return null;
        }
    } else {
        echo "Error in SQL query: " . $conn->error;
        return null;
    }
}

// Calculate the total amount
$total_amount = $transactionDetails["solde"] * $transactionDetails["quantite"];

// Function to generate PDF using TCPDF
function generatePDF($transactionDetails, $total_amount)
{
    define('PDF_DISABLE_IMAGE_SUPPORT', true);

    require('tcpdf/tcpdf.php');

    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A3', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('GBES');
    $pdf->SetAuthor('GBES');
    $pdf->SetTitle('Facture Fournisseur N°: ' . $transactionDetails["facture_f_id"]);
    $pdf->SetSubject('Facture Fournisseur');

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
                <h1>Facture  N°: ' . $transactionDetails["facture_f_id"] . '</h1>
            </div>
            <div class="facture-info">
            <p><strong>Date de facture:</strong> ' . date('Y-m-d') . '</p>
            <p><strong>Nom de l\'entreprise:</strong> GBES</p>
            <p><strong>Adresse:</strong> 123 Rue fkih ben salah</p>
            <p><strong>Code Postal et Ville:</strong> 75001 fkih be salah</p>
            <p><strong>Numéro de téléphone:</strong> 01 23 45 67 89</p>
            <p><strong>Email:</strong> contact@votreentreprise.com</p>
        </div>

        <div class="fournisseur-info">
            <p><strong>Nom du fournisseur:</strong> ' . htmlspecialchars($transactionDetails["fournisseur_nom"] . ' ' . $transactionDetails["fournisseur_prenom"]) . '</p>
        </div>

        <!-- Add the transaction table to the PDF -->
        <table>
            <tr>
                <th>Référence</th>
                <th>Designation</th>
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
                <td>20%</td> <!-- Assuming 20% TVA constant -->
                <td>' . number_format($total_amount, 2) . '</td>
            </tr>
        </table>

        <!-- Add the total montant information to the PDF -->
        <div class="total-montant">Montant total (HT): ' . number_format($total_amount, 2) . ' <span>(DH)</span></div>
        <div class="total-montant">Montant total (TVA): ' . number_format($total_amount * 0.2, 2) . ' <span>(DH)</span></div>
        <div class="total-montant">Montant total (TTC): ' . number_format($total_amount * 1.2, 2) . ' <span>(DH)</span></div>
        <div class="facture-footer">
        <p>Mode de règlement: ' . htmlspecialchars($transactionDetails["mode_paiement"]) . '</p>        </div>
    ');

    // Save the PDF with a filename
    $pdf->Output('facture_fournisseur.pdf', 'D');
}

// Check if the "Generer PDF" button is clicked
if (isset($_GET["generate_pdf"])) {
    // Call the generatePDF function if the 'generate_pdf' parameter is present in the URL
    generatePDF($transactionDetails, $total_amount);
    exit(); // Exit the script after generating the PDF to prevent further HTML output
}
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Facture Fournisseur</title>
 <style>
    /* Common Styles */
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
            position: relative; /* To allow absolute positioning of the logo */
        }

        .facture-header h1 {
            font-size: 24px;
            margin-bottom: 40px;
        }
        .facture-header img {
            max-width: 150px;
            position: absolute; /* Position the logo absolutely within the header */
            top: 0;
            left: 0;
            /* Add some space between the logo and the elements below it */
            margin-bottom: 20px;
        }

        /* Facture Info Styles */
        .facture-info {
            text-align: right;
            margin-top: 10px;
        }

        .facture-info p {
            margin: 5px 0;
        }

        /* Fournisseur Info Styles */
        .fournisseur-info {
            float: left;
            width: 50%;
            /* Add some space between the logo and the fournisseur info */
            margin-top: 170px;
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

        /* Buttons Styles */
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
            <h1>Facture  N°:
                <?php echo $transactionDetails["facture_f_id"]; ?>
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

        <div class="fournisseur-info">
            <p><strong>Nom du fournisseur:</strong>
                <?php echo htmlspecialchars($transactionDetails["fournisseur_nom"] . ' ' . $transactionDetails["fournisseur_prenom"]); ?>
            </p>
        </div>


        <table>
    <tr>
        <th>Référence</th>
        <th>Designation</th>
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



        <div class="facture-footer">
            <p>Mode de paiement: <?php echo htmlspecialchars($transactionDetails["mode_paiement"]); ?></p>
        </div>
    </div>

    <div class="facture-buttons">
    <button onclick="printFacture()"><i class="fas fa-print"></i> Imprimer</button>
    <a href="?facture_f_id=<?php echo $facture_f_id; ?>&generate_pdf=1">
        <button><i class="fas fa-file-pdf"></i> Generer PDF</button>
    </a>
</div>

    <script>
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

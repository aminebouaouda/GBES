<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Directeur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS Code */
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f1f2f6;
        }

        /* New top bar */
        .top-bar {
            background-color: #324659;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .search-form {
            display: flex;
            align-items: center;
            background-color: #34495e;
            border-radius: 20px;
            padding: 5px 15px;
        }

        .search-form input[type="text"] {
            border: none;
            background-color: transparent;
            color: #fff;
            margin-left: 5px;
            width: 180px;
        }

        .search-form button {
            border: none;
            background-color: transparent;
            color: #fff;
            cursor: pointer;
        }

        .search-form button i {
            font-size: 18px;
        }

        .user-info {
            display: flex;
            align-items: center;
            color: #fff;
            margin-right: 20px;
        }

        .user-info i {
            margin-right: 5px;
        }

        /* End of new CSS for top bar */

        .left-side-menu {
            background-color: #2c3e50;
            color: #fff;
            height: 100%;
            width: 230px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 80px;
            /* Adjusted to make space for the top bar */
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
            z-index: 998;
            /* Ensure the menu is behind the top bar */
        }

        .left-side-menu.expanded {
            width: 300px;
        }

        .left-side-menu a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            transition: background-color 0.3s ease;
            width: 100%;
            font-weight: 500;
            font-size: 14px;
        }

        .left-side-menu a i {
            margin-right: 10px;
        }

        .left-side-menu a:hover {
            background-color: #34495e;
        }

        .left-side-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: none;
            padding-left: 20px;
        }

        .left-side-menu li {
            padding: 7px 0;
            display: flex;
            align-items: center;
            font-weight: 400;
            cursor: pointer;
            transition: 0.2s;
        }

        .left-side-menu li a {
            color: #fff;
            text-decoration: none;
            flex-grow: 1;
        }

        .left-side-menu li:hover {
            transform: translateX(5px);
        }

        .left-side-menu li a:hover {
            background-color: #34495e;
        }

        .left-side-menu h2 {
            padding: 12px 20px;
            margin: 0;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            width: 100%;
            border-bottom: 1px solid #445566;
            letter-spacing: 1px;
        }

        .left-side-menu .logout {
            margin-top: auto;
            margin-right: 10px;
            border-top: 1px solid #445566;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
            padding: 10px 20px;
            margin-bottom: 100px;
        }

        .left-side-menu .logout:hover {
            transform: translateX(5px);
        }

        .left-side-menu .logout a {
            color: #fff;
            text-decoration: none;
        }

        .toggle-button {
            position: absolute;
            top: 30px;
            /* Adjusted to align with top bar height */
            right: -20px;
            width: 40px;
            height: 40px;
            background-color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .toggle-button i {
            color: #fff;
            font-size: 20px;
        }

        .toggle-button.expanded {
            transform: rotate(180deg);
        }

        /* Animations */
        .left-side-menu.expanded .left-side-menu ul {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-5px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .left-side-menu.expanded .left-side-menu a {
            animation: slideInLeft 0.3s ease;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-10px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Additional styles for Directeur option */
        .directeur-option {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 12px 20px;
            transition: background-color 0.3s ease;
        }

        .directeur-option:hover {
            background-color: #34495e;
        }

        .directeur-photo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .directeur-option a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .directeur-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-top: none;
            width: 160px;
            z-index: 1000;
        }

        .directeur-option:hover .directeur-dropdown {
            display: block;
        }

        .directeur-dropdown a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
        }

        .directeur-dropdown a:hover {
            background-color: #f0f0f0;
        }

        /* Adjustments for smaller screens */
        @media screen and (max-width: 768px) {
            .top-bar {
                padding: 10px 15px;
            }

            .left-side-menu {
                padding-top: 70px;
                width: 220px;
                /* Adjusted width */
            }

            .left-side-menu.expanded {
                width: 250px;
                /* Adjusted width */
            }

            /* Other responsive adjustments for content */
        }
        .search-highlight {
    background-color: #ffc107; /* Change to the desired background color */
    color: #000; /* Change to the desired text color */
}

    </style>
</head>

<body>
    <!-- New top bar -->
    <div class="top-bar">
        <div class="logo-container">
            <img class="logo" src="images/logo-color.png" alt="Logo">
        </div>
        <button class="toggle-button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <form class="search-form" action="#" method="GET" onsubmit="return performSearch()">           
                <input type="text" id="searchInput" placeholder="Search in current page...">
                <button type="submit"><i class="fas fa-search"></i></button>
</form>

        <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>Directeur</span>
            </div>
            <div class="directeur-option">
                <img src="images/user.png" alt="Directeur Photo" class="directeur-photo">
                <a href="#">Directeur <i class="fas fa-chevron-down"></i></a>
                <div class="directeur-dropdown">
                    <a href="changer_mot_passe.php"><i class="fas fa-key"></i> Changer Mot de Passe</a>
                    <a href="directeur_deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>
        </div>

    <div class="left-side-menu" id="leftMenu">
        <div class="toggle-button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <h2 onclick="toggleSubMenu('historique')"><i class="fas fa-history"></i> Historique</h2>
        <ul id="historique">
            <li><a href="client_historique.php"><i class="fas fa-user-plus"></i> Client</a></li>
            <li><a href="fournisseur_historique.php"><i class="fas fa-box"></i> Fournisseur</a></li>
            <li><a href="intervenant_historique.php"><i class="fas fa-users"></i> Intervenant</a></li>
        </ul>
        <a href="add_client.php"><i class="fas fa-user-plus"></i> Ajouter client</a>
        <a href="add_fournisseur.php"><i class="fas fa-box"></i> Ajouter fournisseur</a>
        <a href="add_intervenant.php"><i class="fas fa-users"></i> Ajouter intervenant</a>
        <h2 onclick="toggleSubMenu('factures')"><i class="fas fa-file-invoice"></i> Factures</h2>
        <ul id="factures">
            <li><a href="client_facture.php"><i class="fas fa-user"></i> Clients</a></li>
            <li><a href="fournisseur_facture.php"><i class="fas fa-boxes"></i> Fournisseurs</a></li>
            <li><a href="intervenant_facture.php"><i class="fas fa-user-friends"></i> Intervenants</a></li>
        </ul>
        <a href="changer_mot_passe.php"><i class="fas fa-key"></i> Changer mot de passe</a>
        <a href="calculer_gbes_solde.php"><i class="fas fa-file-alt"></i> Rapport</a>
       <!-- Inside your HTML code -->
 <div class="logout">
            <a href="directeur_deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
        
    </div>

    <script>
        function toggleSubMenu(elementId) {
            var subMenu = document.getElementById(elementId);
            if (subMenu.style.display === "block") {
                subMenu.style.display = "none";
            } else {
                subMenu.style.display = "block";
            }
        }

        function logout() {
            // Implement your logout logic here
            alert("Logout clicked!");
        }

        const leftMenu = document.getElementById('leftMenu');
        const toggleButton = document.querySelector('.toggle-button');
        let isExpanded = false;

        function toggleSidebar() {
            const body = document.getElementsByTagName('body')[0];
            isExpanded = !isExpanded;
            if (isExpanded) {
                leftMenu.classList.add('expanded');
                toggleButton.classList.add('expanded');
                body.style.overflowX = 'hidden'; // Hide horizontal scrollbar
            } else {
                leftMenu.classList.remove('expanded');
                toggleButton.classList.remove('expanded');
                body.style.overflowX = 'auto'; // Show horizontal scrollbar
            }
        }
        function performSearch() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const pageContent = document.documentElement.innerHTML.toLowerCase(); // Get page content in lowercase

    if (pageContent.includes(searchText)) {
        // Scroll to the first occurrence of the searched text
        const searchResult = new RegExp(searchText, 'gi'); // 'g' for global, 'i' for case-insensitive
        const matches = pageContent.match(searchResult);
        const firstMatchPosition = pageContent.indexOf(matches[0]);

        // Wrap matched text in a <span> with CSS class
        const highlightedPageContent = pageContent.replace(searchResult, '<span class="search-highlight">$&</span>');

        // Update page content and scroll to the first match
        document.documentElement.innerHTML = highlightedPageContent;
        window.scrollTo(0, firstMatchPosition);

        return false; // Prevent the form from submitting
    } else {
        alert('No matches found.');
        return false; // Prevent the form from submitting
    }
}


    </script>
</body>

</html>

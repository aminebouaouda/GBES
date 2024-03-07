CREATE DATABASE IF NOT EXISTS gbes1;
USE gbes1;


-- Table "admins"
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL
);
INSERT INTO admins (username, admin_id, password, role)
VALUES
    ('aminebouaouda01@gmail.com', 1, '12345678', 'directeur'),
    ('aminebouaouda01', 2, '12345678', 'chef'),
    ('admin_administrateur', 3, 'hashed_password_3', 'administrateur');


-- Table "assurance"
CREATE TABLE assurance (
    assurance_id INT PRIMARY KEY AUTO_INCREMENT,
    intitule VARCHAR(255) NOT NULL
);

-- Insertion des exemples dans la table "assurance"
INSERT INTO assurance (intitule) VALUES
    ('Assurance1'),
    ('Assurance2'),
    ('Assurance3');


-- Table "cheque"
CREATE TABLE cheque (
    cheque_id INT PRIMARY KEY AUTO_INCREMENT,
    montant DECIMAL(10, 2),
    date_emission DATE,
    beneficiaire VARCHAR(255),
    numero_cheque VARCHAR(255)
);

-- Table "produit"
CREATE TABLE produit (
    produit_id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(255),
    ref VARCHAR(255),
    quantite INT,
    prix_unitaire INT,
    description TEXT,
    solde DECIMAL(10, 2)
);


-- Table "fonction"
CREATE TABLE fonction (
    fonction_id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50)
);

-- Table "Fournisseur"
CREATE TABLE Fournisseur (
    fournisseur_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    email VARCHAR(255),
    adresse VARCHAR(255),
    telephone VARCHAR(20),
    mot_passe VARCHAR(255),
    genre VARCHAR(10),
    photo BLOB
);

-- Table "intervenant"
CREATE TABLE intervenant (
    intervenant_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(100),
    adresse VARCHAR(100),
    mot_passe VARCHAR(100),
    genre VARCHAR(100),
    fonction_id INT,
    FOREIGN KEY (fonction_id) REFERENCES fonction(fonction_id)
);

-- Table "patient"
CREATE TABLE patient (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    age INT,
    email VARCHAR(50),
    adresse VARCHAR(100),
    telephone VARCHAR(20),
    genre VARCHAR(100),
    mot_passe VARCHAR(100),
    assurance_id INT,
    photo BLOB,
    FOREIGN KEY (assurance_id) REFERENCES assurance(assurance_id)
);

-- Table "facture_f"
CREATE TABLE facture_f (
    facture_f_id INT PRIMARY KEY AUTO_INCREMENT,
    date_facture DATE,
    delai VARCHAR(100),
    fournisseur_id INT,
    cheque_id INT,
    mode_paiement VARCHAR(234),
    FOREIGN KEY (fournisseur_id) REFERENCES Fournisseur(fournisseur_id),
    FOREIGN KEY (cheque_id) REFERENCES cheque(cheque_id)
);

-- Table "facture_p"
CREATE TABLE facture_p (
    facture_p_id INT PRIMARY KEY AUTO_INCREMENT,
    date_facture VARCHAR(100),
    delai VARCHAR(100),
    patient_id INT,
    mode_paiement VARCHAR(234),
    cheque_id INT,
    FOREIGN KEY (patient_id) REFERENCES patient(patient_id),
    FOREIGN KEY (cheque_id) REFERENCES cheque(cheque_id)
);

-- Table "facture_i"
CREATE TABLE facture_i (
    facture_i_id INT PRIMARY KEY AUTO_INCREMENT,
    date_facture VARCHAR(100),
    intervenant_id INT,
    delai VARCHAR(100),
    mode_paiement VARCHAR(234),
    cheque_id INT,
    FOREIGN KEY (intervenant_id) REFERENCES intervenant(intervenant_id),
    FOREIGN KEY (cheque_id) REFERENCES cheque(cheque_id)
);

-- Table "OpChirurgie"
CREATE TABLE OpChirurgie (
    op_chirgurie_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    date_operation VARCHAR(100),
    date_debut VARCHAR(255),
    date_fin VARCHAR(255),
    heure_debut VARCHAR(255),
    heure_fin VARCHAR(255),
    montant DECIMAL(10, 2),
    produit_id INT,
    patient_id INT,
    intervenant_id INT,
    FOREIGN KEY (patient_id) REFERENCES patient(patient_id),
    FOREIGN KEY (intervenant_id) REFERENCES intervenant(intervenant_id),
    FOREIGN KEY (produit_id) REFERENCES produit(produit_id)
);

-- Table "detail_p"
CREATE TABLE detail_p (
    detail_p_id INT AUTO_INCREMENT PRIMARY KEY,
    facture_p_id INT,
    produit_id INT,
    FOREIGN KEY (facture_p_id) REFERENCES facture_p(facture_p_id),
    FOREIGN KEY (produit_id) REFERENCES produit(produit_id)
);

-- Table "detail_f"
CREATE TABLE detail_f (
    detail_f_id INT AUTO_INCREMENT PRIMARY KEY,
    facture_f_id INT,
    produit_id INT,
    FOREIGN KEY (facture_f_id) REFERENCES facture_f(facture_f_id),
    FOREIGN KEY (produit_id) REFERENCES produit(produit_id)
);

-- Table "detail_i"
CREATE TABLE detail_i (
    detail_i_id INT AUTO_INCREMENT PRIMARY KEY,
    facture_i_id INT,
    produit_id INT,
    FOREIGN KEY (facture_i_id) REFERENCES facture_i(facture_i_id),
    FOREIGN KEY (produit_id) REFERENCES produit(produit_id)
);

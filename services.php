<?php
session_start();
require_once("connection.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Nos Services</title>
    <link rel="stylesheet" href="styles/services.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="services-section">
        <h1>Nos Services Immobiliers</h1>

        <div class="services-intro">
            <p>Chez Nous, nous offrons une gamme complète de services pour répondre à tous vos besoins immobiliers. Notre équipe dévouée est là pour vous aider à trouver la maison de vos rêves et vous guider à chaque étape du processus.</p>
        </div>

        <div class="services-list">
            <h2>Ce Que Nous Offrons</h2>
            <ul>
                <li>Recherche de propriétés personnalisée</li>
                <li>Estimation immobilière professionnelle</li>
                <li>Conseil en investissement immobilier</li>
                <li>Services juridiques et notariés</li>
                <li>Assistance au financement et prêts immobiliers</li>
                <li>Suivi après-vente et gestion de propriété</li>
            </ul>
        </div>

        <div class="services-extra">
            <h2>Pourquoi Choisir Notre Entreprise ?</h2>
            <p>Notre équipe se consacre à fournir un service exceptionnel, mettant l'accent sur la satisfaction du client. Avec des années d'expérience et une connaissance approfondie du marché immobilier, nous vous assurons une expérience fluide et agréable.</p>

            <h2>Contactez-Nous</h2>
            <p>Si vous avez des questions ou souhaitez en savoir plus sur nos services, n'hésitez pas à <a href="contact.php">nous contacter</a>. Nous serons ravis de vous aider à trouver la maison de vos rêves!</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

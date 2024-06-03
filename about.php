<?php
session_start();
require_once("connection.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>À Propos de Nous</title>
    <link rel="stylesheet" type="text/css" href="styles/about.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="about-section">
        <h1>À Propos de Notre Société</h1>

        <div class="company-overview">
            <h2>Qui Sommes-Nous ?</h2>
            <p>Nous sommes une entreprise immobilière dédiée à offrir les meilleures maisons et villas à nos clients. Notre mission est de vous aider à trouver la maison de vos rêves avec facilité et confiance. Avec des années d'expérience, notre équipe s'engage à fournir un excellent service client et une expertise inégalée.</p>
        </div>

        <div class="mission-statement">
            <h2>Notre Mission</h2>
            <p>Notre mission est de connecter les gens avec leur maison idéale. Nous nous efforçons d'offrir une large gamme de propriétés adaptées à différents styles de vie et budgets. Notre objectif est de rendre le processus d'achat aussi fluide et agréable que possible.</p>
        </div>

        <div class="team-introduction">
            <h2>Rencontrez Notre Équipe</h2>
            <p>Notre équipe est composée de professionnels de l'immobilier expérimentés qui sont passionnés par le fait de vous aider à trouver la maison idéale. Nous croyons en une approche personnalisée et travaillons en étroite collaboration avec chaque client pour comprendre ses besoins uniques.</p>
        </div>

        <div class="contact-information">
            <h2>Contactez-Nous</h2>
            <p>Si vous avez des questions ou souhaitez plus d'informations sur nos services, n'hésitez pas à <a href="contact.php">nous contacter</a>. Nous sommes là pour vous aider à trouver votre maison de rêve!</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

<style>
.container-emplacement {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
    margin: 50px 50px;
}

.texte-emplacement {
    flex: 1;
    padding: 20px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 16px;
    text-align: left;
}

.item-emplacement {
    margin-bottom: 20px;
}

.item-emplacement h3 {
    font-size: 24px;
    color: #333;
}

.item-emplacement p {
    font-size: 16px;
    color: #666;
}

.carte-emplacement {
    flex: 1;
    height: 300px;
    margin-left: 20px;
}
</style>

<div class="container-emplacement">
    <div class="texte-emplacement">
        <div class="item-emplacement">
            <h3>Informations pour les acheteurs</h3>
            <p>Trouvez les meilleures propriétés immobilières. Nous proposons une variété d'options pour les acheteurs à la recherche de leur maison idéale.</p>
        </div>

        <div class="item-emplacement">
            <h3>Informations pour les vendeurs</h3>
            <p>Découvrez comment vendre votre propriété avec nous. Nous fournissons des services complets pour vous aider à obtenir le meilleur prix pour votre maison.</p>
        </div>

        <div class="item-emplacement">
            <h3>Les avantages de notre site immobilier</h3>
            <p>Nous proposons une vaste sélection de propriétés, des outils de recherche avancés, et un service client exceptionnel.</p>
        </div>
    </div>

    <div class="carte-emplacement">
        <iframe
            width="100%"
            height="300px"
            frameborder="0"
            src="https://www.openstreetmap.org/export/embed.html?bbox=6.896%2C36.856%2C6.916%2C36.876&layer=mapnik&marker=36.866%2C6.906"
            allowfullscreen>
        </iframe>
    </div>
</div>
---
marp: true
theme: default
size: 16:9
paginate: true
style: |
  section {
    font-family: "Aptos", "Segoe UI", sans-serif;
    background: #f8fafc;
    color: #172033;
    padding: 48px 64px;
  }
  h1, h2 {
    color: #243b6b;
  }
  h1 {
    font-size: 2.4em;
  }
  h2 {
    font-size: 1.8em;
    margin-bottom: 0.7em;
  }
  ul {
    font-size: 1.1em;
    line-height: 1.55;
  }
  section.image-only {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 24px;
  }
  section.image-only img {
    max-width: 96%;
    max-height: 66vh;
    width: auto;
    height: auto;
    object-fit: contain;
  }
  section.split-left {
    display: grid;
    grid-template-columns: 1.25fr 0.75fr;
    grid-template-rows: auto 1fr;
    column-gap: 28px;
    align-items: center;
  }
  section.split-left h2 {
    grid-column: 1 / -1;
    align-self: start;
    margin-bottom: 0.35em;
  }
  section.split-left img {
    max-width: 100%;
    max-height: 76vh;
    width: auto;
    height: auto;
    object-fit: contain;
    justify-self: center;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 18px rgba(23, 32, 51, 0.12);
  }
  section.split-left ul {
    font-size: 0.8em;
    line-height: 1.5;
    margin: 0;
    padding-left: 1.1em;
  }
  section table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
    margin-top: 0.3em;
  }
  section th, section td {
    border: 1px solid #d7deea;
    padding: 8px 12px;
    text-align: left;
    vertical-align: top;
  }
  section th {
    background: #eef2f8;
    color: #243b6b;
  }
  section.compact ul {
    font-size: 1em;
    line-height: 1.55;
  }
  section.statement blockquote {
    margin: 0.2em 0 0.7em;
    padding: 20px 26px;
    background: #eef2f8;
    border-left: 6px solid #4f46e5;
    border-radius: 8px;
    font-size: 1.15em;
    line-height: 1.5;
    font-weight: 600;
  }
  section.wide-image {
    display: flex;
    flex-direction: column;
  }
  section.wide-image img {
    width: 100%;
    max-height: 50vh;
    object-fit: contain;
  }
  section.wide-image ul {
    font-size: 1em;
    line-height: 1.5;
  }
  section.gallery table {
    border: none;
  }
  section.gallery td {
    border: none;
    text-align: center;
    vertical-align: middle;
    padding: 4px 10px;
  }
  section.gallery img {
    max-width: 100%;
    max-height: 52vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(23, 32, 51, 0.14);
  }
  section.gallery.three img {
    max-height: 44vh;
  }
  section.gallery td.caption {
    color: #475569;
    font-size: 0.95em;
  }
  section.title-slide {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    text-align: center;
    padding: 34px 70px 40px;
    background: linear-gradient(160deg, #f8fafc 0%, #eef2ff 100%);
  }
  section.title-slide .logos {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }
  section.title-slide .logos img {
    height: 104px;
    width: auto;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(36, 59, 107, 0.12);
  }
  section.title-slide .kicker {
    margin: 0 0 10px;
    color: #4f46e5;
    font-size: 0.95em;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
  }
  section.title-slide h1 {
    margin: 0;
    font-size: 3.6em;
    line-height: 1.05;
    letter-spacing: -1px;
  }
  section.title-slide .subtitle {
    margin: 14px 0 0;
    font-size: 1.5em;
    color: #475569;
    font-weight: 500;
  }
  section.title-slide .rule {
    width: 96px;
    height: 5px;
    border-radius: 999px;
    background: #4f46e5;
    margin: 22px auto 0;
  }
  section.title-slide .meta {
    display: flex;
    gap: 64px;
    align-items: flex-start;
    justify-content: center;
    padding: 18px 40px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(36, 59, 107, 0.08);
  }
  section.title-slide .meta p {
    margin: 0;
    font-size: 1.05em;
    line-height: 1.5;
  }
  section.title-slide .meta .meta-label {
    display: block;
    font-size: 0.72em;
    font-weight: 700;
    letter-spacing: 1.6px;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
  }
  section.title-slide .meta strong {
    color: #172033;
    font-size: 1.15em;
  }
---

<!-- _class: title-slide -->
<!-- _paginate: false -->

<div class="logos">
  <img src="ofppt.jpg" alt="Logo OFPPT" />
  <img src="solicode.jpg" alt="Logo Solicode" />
</div>

<div class="title-block">
  <p class="kicker">Projet fil rouge · Développement Web</p>
  <h1>SmartMatch</h1>
  <p class="subtitle">Plateforme de Recrutement Intelligent</p>
  <div class="rule"></div>
</div>

<div class="meta">
  <p>
    <span class="meta-label">Réalisé par</span>
    <strong>Marwan Bouhamid</strong>
  </p>
  <p>
    <span class="meta-label">Encadré par</span>
    <strong>ES-SARRAJ Fouad</strong>
  </p>
</div>

---

## Sommaire

1. Contexte et objectifs du projet
2. Méthodologie : Scrum, 2TUP et Design Thinking
3. Planification et branche fonctionnelle
4. Conception technique : architecture, données et classes
5. Réalisation : maquettes
6. Bilan et perspectives

---

## Introduction

**SmartMatch** est une plateforme de recrutement intelligent qui met en relation les candidats et les offres d'emploi à partir du profil, du domaine et des compétences.

---

## Contexte du projet

Aujourd'hui, la recherche d'emploi reste **chronophage et peu ciblée** :

- Les offres sont dispersées sur de multiples plateformes et difficiles à comparer.
- Les candidats ont du mal à évaluer leur adéquation avec une offre.
- Les recruteurs reçoivent de nombreuses candidatures peu qualifiées.
- Les administrateurs manquent d'outils pour structurer le référentiel de compétences.

**SmartMatch** centralise les offres, les compétences et les utilisateurs dans un espace unique, avec un accès adapté à chaque rôle.

---
<!-- _class: image-only -->

## Scrum

![Méthode Scrum](../rapport/SCRUM/scrum.png)

---
<!-- _class: image-only -->

## Processus 2TUP

![Processus 2TUP](../rapport/2TUP/images.jpg)

---
<!-- _class: image-only -->

## Design Thinking

![Design Thinking](../rapport/design_thinking/design_thinking.jpg)

---

<!-- _class: compact -->

## Planification

| Sprint | Période | Objectif | Livrable |
|---|---|---|---|
| **Sprint 1** | 22 sept. 2026 | Socle : page publique et espace admin | CRUD offres et compétences (MySQL / PDO) — `Season-1` |
| **Sprint 2** | 04 oct. 2026 | Refactorisation orientée objet | API REST JSON et frontend dynamique — `Session-2` |
| **Sprint 3** | 12 nov. 2026 | Sécurité et administration | Authentification, sessions, tableau de bord, uploads — `Session-3` |
| **Sprint 4** | 17 nov. 2026 | Documentation et soutenance | Rapport, diagrammes et présentation — `Session-4` |

---

## Branche fonctionnelle

| Acteur | Besoins | Fonctions principales |
|---|---|---|
| **Candidat** | Trouver une offre adaptée à son profil | Consulter les offres, créer un profil, postuler |
| **Recruteur** | Publier et suivre ses offres | Créer, modifier et supprimer une offre ; consulter les candidatures |
| **Administrateur** | Structurer et modérer la plateforme | Gérer les compétences, les recruteurs et les publications |

**Périmètre livré** · Consultation publique et espace administrateur (offres, compétences, utilisateurs). Les candidatures et le score de matching constituent la prochaine itération.

---

<!-- _class: compact -->

## Carte d'empathie

| Axe | Candidat | Recruteur |
|---|---|---|
| **Pense** | « Je perds du temps à trier les offres. » | « Je reçois trop de profils non qualifiés. » |
| **Voit** | Des offres dispersées et peu comparables. | Des candidatures difficiles à comparer. |
| **Ressent** | Un doute sur l'adéquation de son profil. | Une charge de tri et de publication. |
| **Attend** | Des recommandations claires et rapides. | Des candidats ciblés et un suivi simple. |

---

<!-- _class: statement -->

## Définition du problème

> Comment aider un candidat à identifier rapidement les offres réellement adaptées à son profil, tout en permettant aux recruteurs de publier et de gérer leurs offres efficacement ?

- **Enjeu** · Passer d'une recherche par mots-clés à une mise en relation par profil.
- **Cible** · Un parcours simple, transparent et responsive pour les trois acteurs.
- **Résultat attendu** · Un temps de recherche réduit et des candidatures plus pertinentes.

---

<!-- _class: compact -->

## Idéation

**Solutions explorées**

- Matching par domaine, années d'expérience et compétences.
- Référentiel unique de compétences géré par l'administrateur.
- Tableaux de bord et permissions adaptés à chaque rôle.
- API REST et interface dynamique sans rechargement.
- Upload et gestion des images des offres.
- Modération des offres avant publication.

**Priorisation** · MVP livré : consultation des offres, authentification admin, CRUD offres et compétences. Évolutions : inscription candidat, candidatures, score de matching et notifications.

---

<!-- _class: wide-image -->

## Diagramme de cas d'utilisation

![Diagramme de cas d'utilisation](image1.png)

- **Candidat** · consulter les offres
- **Recruteur** · gérer ses offres et son profil
- **Administrateur** · gérer les compétences, les recruteurs et les publications

---

## Stack technologique

- **Backend** · PHP 8+ · Architecture DAO & Services · Contrôleurs orientés objet
- **API** · REST API native en PHP avec gestion des en-têtes JSON et CORS
- **Frontend** · HTML5 · Tailwind CSS · JavaScript (Vanilla ES6) · Modules dynamiques
- **Stockage** · Fichiers JSON sécurisés (gestion concurrente avec verrouillage `flock`)
- **Outils & Diagrammes** · Mermaid · PlantUML · Git

---

## Prototype SmartMatch

- **Offres d'emploi** · Titre, description, domaine, années d'expérience, image
- **Compétences** · Référentiel des compétences techniques (PHP, React, Docker, etc.)
- **CRUD complet** · Création, consultation, modification et suppression
- **Espace Administrateur** · Tableau de bord sécurisé et gestion des recruteurs
- **Gestion des médias** · Upload d'images pour les offres d'emploi

---

## Relations des données

- **Utilisateur / Recruteur** → gère plusieurs offres d'emploi
- **Offre d'emploi** → rattachée à un recruteur et associée à des critères de compétences
- **Compétence** → référentiel global évaluable dans les profils et offres

---

## Structure technique

- **Frontend** · `frontend/` (Pages publiques et administration, composants JS modulaires)
- **Backend** · `backend/` (Contrôleurs, Services, DAO, Helpers, Bootstrap)
- **API** · Endpoints REST centralisés dans `backend/api/`
- **Données** · Fichiers JSON stockés dans `backend/data/` avec gestion d'uploads d'images

---

<!-- _class: split-left -->

## Diagramme de classes

![Diagramme de classes](image.png)

- **USER** · Recruteur : profil, rôle, domaine, expérience
- **JOB_OFFER** · Offre publiée par le recruteur
- **SKILL** · Compétence liée à une offre
- **APPLICATION** · Candidature à une offre

---

<!-- _class: gallery -->

## Maquettes

| Espace public | Espace administrateur |
|---|---|
| ![Page d'accueil SmartMatch](capture-accueil.png) | ![Connexion administrateur](capture-connexion.png) |
| Page d'accueil responsive · hero, parcours en trois étapes et aperçu des offres | Connexion sécurisée · accès réservé à l'administrateur |

---

<!-- _class: gallery three -->

## Interfaces

| Tableau de bord | Gestion des offres | Formulaire d'offre |
|---|---|---|
| ![Tableau de bord](capture-dashboard.png) | ![Liste des offres](capture-offres.png) | ![Formulaire de création](capture-formulaire.png) |
| Statistiques en temps réel · offres, compétences et utilisateurs | CRUD complet · image, domaine, expérience et recruteur | Création et modification · validation et upload d'image |

---

## Conclusion

- **Prototype fonctionnel** · Parcours public, espace admin sécurisé, CRUD offres et compétences, upload d'images.
- **Architecture maîtrisée** · PHP orienté objet, API REST JSON et séparation contrôleur / service / DAO, sans framework lourd.
- **Compétences mobilisées** · Analyse des besoins, UML, développement web, Git et méthodes agiles.
- **Perspectives** · Inscription candidat, candidatures, score de matching, base MySQL et authentification JWT.

**Merci de votre attention.**

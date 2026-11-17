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
    font-size: 1.25em;
    line-height: 1.65;
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
    max-height: 78%;
    object-fit: contain;
  }
---

# SmartMatch
## Plateforme de Recrutement Intelligent

---

## Remerciements

---

## Introduction

---

## Contexte du projet

---

## Objectif de formation (Solicode)

---

## Cahier des charges

---
<!-- _class: image-only -->

## Scrum

![Méthode Scrum](/rapport/SCRUM/scrum.png)

---
<!-- _class: image-only -->

## Processus 2TUP

![Processus 2TUP](/rapport/2TUP/images.jpg)

---
<!-- _class: image-only -->

## Design Thinking

![Design Thinking](/rapport/design_thinking/design_thinking.jpg)

---

## Planification

---

## Branche fonctionnelle

---

## Carte d'empathie

---

## Définition du problème

---

## Idéation

---

## Diagramme de cas d'utilisation

---

## Stack technologique

- **Backend** · PHP 8+ · Architecture DAO & Services · Contrôleurs orientés objet
- **API** · REST API native en PHP avec gestion des en-têtes JSON et CORS
- **Frontend** · HTML5 · Tailwind CSS · JavaScript (Vanilla ES6) · Modules dynamiques
- **Stockage** · Fichiers JSON sécurisés (gestion concurrente avec verrouillage `flock`)
- **Outils & Diagrammes** · Mermaid · PlantUML · Git

---

## Architecture

**Présentation** → **Logique métier** → **Accès aux données**

Navigateur · HTML / Tailwind CSS / JavaScript

↓ Requêtes Fetch API (REST / JSON)

PHP · Services · DAO

↓

Stockage Fichiers JSON (`offres.json`, `competences.json`, `users.json`)

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

## Diagramme de classes

---

## Maquettes

---

## Interfaces

---

## Conclusion

- Prototype web complet de recrutement intelligent (SmartMatch) fonctionnel
- Architecture MVC / DAO en PHP sans framework lourd, offrant une excellente maîtrise technique
- Base solide pour des évolutions futures (matching algorithmique avancé, base de données relationnelle MySQL, authentification JWT)

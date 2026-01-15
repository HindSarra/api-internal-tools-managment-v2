# Internal Tools API

API REST permettant la gestion des outils SaaS internes d’une entreprise
(listing, filtres, coûts, statuts, création et mise à jour).

---

## Technologies

- PHP 8.3
- Symfony 7.4
- Doctrine ORM
- MySQL (Docker)
- Swagger / OpenAPI (NelmioApiDocBundle)

---

## Quick Start

### 1. Lancer la base de données MySQL (Docker)
`docker-compose --profile mysql up -d`
### 2.Installer les dépendances PHP
``composer install``
### 3. Exécuter les migrations Doctrine
php bin/console doctrine:migrations:migrate

### 4. Démarrer le serveur Symfony
php -S 127.0.0.1:8000 -t public public/index.php

### 5. Accès à l’API

API : http://127.0.0.1:8000/api/tools
Swagger UI : http://127.0.0.1:8000/api/doc
OpenAPI JSON : http://127.0.0.1:8000/api/doc.json

## Configuration
La connexion à la base de données est gérée via des variables d’environnement.
Le fichier .env.exemple contient la variable DATABASE_URL.
### Exemple de configuration
``DATABASE_URL="mysql://dev:dev_password@127.0.0.1:3306/internal_tools?serverVersion=8.0&charset=utf8mb4"``

Doctrine utilise automatiquement cette variable pour établir la connexion
à la base de données, sans configuration supplémentaire dans le code.

## Endpoints principaux
### GET /api/tools
Liste des outils avec filtres, pagination et tri.

-Filtres disponibles :
department
status
min_cost
max_cost
category

-Pagination :
page
limit

-Tri :
sort = name | monthly_cost | created_at
order = asc | desc
### GET /api/tools/{id}

Récupération du détail complet d’un outil.
Retour 404 si l’outil n’existe pas
Calcul du total_monthly_cost
POST /api/tools
Création d’un nouvel outil.
Validation des champs
Valeurs par défaut appliquées (status, active_users_count)
Retour 201 en cas de succès
### PUT /api/tools/{id}
Mise à jour partielle d’un outil existant.
Champs non fournis conservés
Validation des données
Retour 404 si l’outil n’existe pas
Format des erreurs
Erreur de validation (400)
{
  "error": "Validation failed",
  "details": {
    "field": "message"
  }
}

Ressource inexistante (404)
{
  "error": "Tool not found",
  "message": "Tool with ID X does not exist"
}

## Architecture

Controller
Gestion de la couche HTTP (routes, requêtes, codes de statut)

DTO
Validation des données d’entrée (POST / PUT)

Repository
Requêtes complexes (filtres, tri, pagination)

Entity
Mapping Doctrine / base de données

Architecture pensée pour être maintenable, claire et évolutive.

## Tests

Dans le cadre du temps imparti (8h), aucun test automatisé (PHPUnit) n’a été mis en place.
En revanche, l’ensemble des endpoints a été testé manuellement de manière exhaustive
via PowerShell (Invoke-RestMethod) et via la documentation Swagger (Nelmio).
Outils utilisés
PowerShell (Invoke-RestMethod)
Swagger UI (/api/doc)
Base de données MySQL (Docker)
Exemples de tests effectués
GET /api/tools
Invoke-RestMethod http://127.0.0.1:8000/api/tools
PUT /api/tools/{id} — Mise à jour partielle
$update = @{
  monthly_cost = 7.00
  status = "deprecated"
  description = "Updated description after renewal"
} | ConvertTo-Json -Depth 5

Invoke-RestMethod -Method Put `
  -Uri "http://127.0.0.1:8000/api/tools/6" 
  -ContentType "application/json" `
  -Body $update

Données invalides (400)
$badUpdate = @{
  status = "wrong"
  monthly_cost = -1
} | ConvertTo-Json -Depth 5

Invoke-RestMethod -Method Put `
  -Uri "http://127.0.0.1:8000/api/tools/6" `
  -ContentType "application/json" `
  -Body $badUpdate

ID inexistant (404)
Invoke-RestMethod -Method Put `
  -Uri "http://127.0.0.1:8000/api/tools/999999" `
  -ContentType "application/json"   
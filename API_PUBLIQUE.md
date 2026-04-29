# Documentation de l'API Publique XML

L'API Publique de DAZO permet d'exposer de manière sécurisée et structurée les décisions de la plateforme vers des systèmes tiers (tels que des sites vitrines, des CMS comme WordPress ou Drupal, ou d'autres applications métier).

Les données sont retournées au format **XML**, afin de faciliter la génération de flux (type RSS structuré) ou de snippets HTML.

---

## 1. Sécurité et Authentification

Bien qu'il s'agisse d'une API "Publique", l'accès est protégé pour éviter le scraping massif ou l'accès non autorisé aux données de l'organisation.

### Clé d'API (API Key)
Vous devez générer une clé d'API depuis l'interface d'administration de DAZO (Menu **Système** > **Publication API**).

Cette clé doit être fournie avec chaque requête, de l'une des deux manières suivantes :

**Méthode 1 : En-tête HTTP (Recommandé)**
```http
X-API-Key: dz_votreCleSecreteGenereeIci
```

**Méthode 2 : Paramètre d'URL (GET)**
```http
GET /api/v1/public/decisions?api_key=dz_votreCleSecreteGenereeIci
```

### Limite de requêtes (Rate Limiting)
Afin de préserver les performances de la plateforme, l'API est limitée à **60 requêtes par minute** par adresse IP.
En cas de dépassement, le serveur renverra un code HTTP `429 Too Many Requests`.

---

## 2. Configuration et Visibilité

Toutes les décisions ne sont pas exposées sur l'API. Pour qu'une décision apparaisse dans les résultats, elle doit respecter les règles strictes suivantes :

1. **Visibilité Individuelle** : L'auteur de la décision doit avoir sélectionné "Publique" lors de la création ou l'édition de la décision. (Une décision "Privée" n'est jamais exposée).
2. **Cercles Autorisés** : La décision doit appartenir à un cercle coché dans l'interface de configuration "Publication API".
3. **Catégories Autorisées** : La décision doit posséder au moins une catégorie cochée dans la configuration.
4. **Statuts Valides** : La décision doit avoir un statut coché dans la configuration (généralement `adopted` ou `rejected`).

---

## 3. Endpoints

L'API de base répond sur le préfixe : `https://votre-dazo.tld/api/v1/public`

### 3.1. Liste des décisions

`GET /decisions`

Retourne la liste paginée des décisions publiques.

**Paramètres de filtres supportés (optionnels) :**
*(Note : Ces filtres doivent être activés par l'administrateur dans l'interface de publication pour être pris en compte).*

- `page` : Numéro de la page (défaut: 1)
- `category` : Filtrer par ID ou nom de catégorie exact
- `circle` : Filtrer par ID ou nom de cercle exact
- `status` : Filtrer par statut exact (ex: `adopted`)
- `author` : Filtrer par nom d'auteur
- `search` : Recherche partielle dans le titre de la décision

**Exemple de Requête :**
```http
GET /api/v1/public/decisions?status=adopted&category=Finance&api_key=dz_ABC123
```

**Exemple de Réponse XML :**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<dazo>
  <metadata>
    <total>42</total>
    <per_page>20</per_page>
    <current_page>1</current_page>
    <last_page>3</last_page>
  </metadata>
  <decisions>
    <decision id="9ea9a8b1-1234-abcd-5678-000000000000">
      <title>Adopter le télétravail hybride</title>
      <status>adopted</status>
      <visibility>public</visibility>
      <created_at>2026-04-29T14:30:00Z</created_at>
      <updated_at>2026-04-29T14:45:00Z</updated_at>
      <circle id="9ea9a..."><name>Direction</name></circle>
      <categories>
         <category id="..."><name>RH</name></category>
      </categories>
    </decision>
    <!-- ... autres décisions ... -->
  </decisions>
</dazo>
```

---

### 3.2. Détail d'une décision

`GET /decisions/{id}`

Retourne le détail d'une décision spécifique, en incluant le contenu textuel complet formaté en HTML.

**Paramètres :**
- `id` : L'identifiant UUID de la décision.

**Exemple de Requête :**
```http
GET /api/v1/public/decisions/9ea9a8b1-1234-abcd-5678-000000000000?api_key=dz_ABC123
```

**Exemple de Réponse XML :**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<dazo>
  <decision id="9ea9a8b1-1234-abcd-5678-000000000000">
    <title>Adopter le télétravail hybride</title>
    <status>adopted</status>
    <visibility>public</visibility>
    <created_at>2026-04-29T14:30:00Z</created_at>
    <updated_at>2026-04-29T14:45:00Z</updated_at>
    <circle id="9ea9a..."><name>Direction</name></circle>
    <categories>
        <category id="..."><name>RH</name></category>
    </categories>
    <content><![CDATA[<h2>Objectif</h2><p>Mettre en place une politique hybride pour tous les salariés.</p>]]></content>
  </decision>
</dazo>
```

> **Note sur le `<content>` :**
> Le texte de la décision est encodé en `CDATA` car il contient des balises HTML provenant de l'éditeur riche de DAZO. Lors de l'intégration dans votre CMS (WP, Drupal, etc.), vous pouvez récupérer ce contenu et l'afficher directement dans un bloc HTML.

---

## 4. Codes d'erreurs courants

- `401 Unauthorized` : La clé d'API est manquante ou invalide.
- `404 Not Found` : La décision n'existe pas, ou bien elle est bloquée par les paramètres de confidentialité (décision privée, cercle non public, etc.).
- `429 Too Many Requests` : Vous avez dépassé la limite de 60 requêtes par minute.
- `503 Service Unavailable` : L'API publique n'a pas encore été configurée (la clé secrète n'a pas été générée par l'administrateur).

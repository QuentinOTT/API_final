# Explication de la configuration de l'entité Livre

Ce document explique la configuration API Platform de l'entité Livre dans le fichier `src/Entity/Livre.php`.

## Configuration générale

```php
@ApiResource(
  attributes={
      "order"={
          "titre":"ASC",
          "prix":"ASC"
      }
  },
  ...
)
```

**Explication** : Cette section définit le tri par défaut pour les collections de livres. Les livres seront triés d'abord par titre puis par prix (ordre croissant).

## Opérations de collection

```php
collectionOperations={
    "get"={
        "method"="GET"
    },
    "post"={
        "method"="POST"
    },
    "get_role_adherent"={
        "method"="GET",
        "path"="/adherents/livres",
        "security"="is_granted('ROLE_ADHERENT')",
        "normalization_context"={
            "groups"={"get_role_adherent"}
        },
        "defaults"={
            "_controller"="api_platform.action.get_collection"
        }
    },
    "get_role_manager"={
        "method"="GET",
        "path"="/manager/livres",
        "security"="is_granted('ROLE_MANAGER')",
        "security_message"="Vous n'avez pas accès à cette ressource",
        "defaults"={
            "_controller"="api_platform.action.get_collection"
        }
    },
    "post"={
        "method"="GET"
        "security"="is_granted('ROLE_MANAGER')",
        "security_message"="Vous n'avez pas accès à cette ressource",
        "defaults"={
            "_controller"="api_platform.action.get_collection"
        }
    }
}
```

**Explication** :
- `"get"` : Opération standard pour récupérer tous les livres (GET /apiplatform/livres)
- `"post"` : Opération standard pour créer un nouveau livre (POST /apiplatform/livres)
- `"get_role_adherent"` : Opération personnalisée pour les adhérents (GET /apiplatform/adherents/livres)
  - Accessible uniquement aux utilisateurs ayant le rôle ROLE_ADHERENT
  - Utilise le groupe de sérialisation "get_role_adherent" pour contrôler les champs retournés
  - Utilise le contrôleur standard d'API Platform pour les collections
- `"get_role_manager"` : Opération personnalisée pour les managers (GET /apiplatform/manager/livres)
  - Accessible uniquement aux utilisateurs ayant le rôle ROLE_MANAGER
  - Affiche un message personnalisé en cas d'accès non autorisé
- **Attention** : Il y a une duplication de l'opération "post" avec une méthode GET, ce qui pourrait causer des conflits

## Opérations d'item

```php
itemOperations={
    "get"={
        "method"="GET"
    },
    "put"={
        "method"="PUT"
    },
    "delete"={
        "method"="DELETE"
    },
    "patch"={
        "method"="PATCH"
    }
}
```

**Explication** :
- `"get"` : Opération pour récupérer un livre spécifique par son ID (GET /apiplatform/livres/{id})
- `"put"` : Opération pour mettre à jour complètement un livre (PUT /apiplatform/livres/{id})
- `"delete"` : Opération pour supprimer un livre (DELETE /apiplatform/livres/{id})
- `"patch"` : Opération pour mettre à jour partiellement un livre (PATCH /apiplatform/livres/{id})

## Filtres

```php
@ApiFilter(
    SearchFilter::class,
    properties={
        "titre": "ipartial",
        "auteur": "exact",
        "genre": "exact"
    }
)
```

**Explication** : Filtre de recherche qui permet de filtrer les livres selon différents critères
- `"titre": "ipartial"` : Recherche partielle insensible à la casse dans le titre
- `"auteur": "exact"` : Recherche exacte sur l'auteur (relation)
- `"genre": "exact"` : Recherche exacte sur le genre (relation)

```php
@ApiFilter(
    OrderFilter::class,
    properties={
        "titre"="asc",
    }
)
```

**Explication** : Filtre de tri qui permet de trier les résultats selon différents critères
- `"titre"="asc"` : Permet de trier par titre (par défaut en ordre croissant)

```php
@ApiFilter(
    PropertyFilter::class,
    arguments={
        "parameterName"="properties",
       "overrideDefaultProperties": false,
        "whitelist"={
                      "isbn",
                      "titre",
                      "prix"
                      }
    }
)
```

**Explication** : Filtre de propriétés qui permet de sélectionner les propriétés à inclure dans la réponse
- `"parameterName"="properties"` : Nom du paramètre dans l'URL pour filtrer les propriétés
- `"overrideDefaultProperties": false` : Ne pas remplacer les propriétés par défaut
- `"whitelist"` : Liste des propriétés autorisées à être filtrées
  - `"isbn"` : Numéro ISBN du livre
  - `"titre"` : Titre du livre
  - `"prix"` : Prix du livre

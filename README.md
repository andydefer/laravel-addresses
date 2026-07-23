# Laravel Addresses

Un package Laravel complet pour gérer des adresses polymorphiques avec le pattern Repository, des DTOs, des Value Objects et une gestion des adresses principales.

---

## ✨ Fonctionnalités

- ✅ **Adresses polymorphiques** - Attachez des adresses à n'importe quel modèle Eloquent
- ✅ **Types d'adresses multiples** - PRINCIPALE, FACTURATION, LIVRAISON, PROFESSIONNELLE, AUTRE
- ✅ **Gestion de l'adresse principale** - Définissez et récupérez l'adresse principale
- ✅ **Pattern Repository** - Séparation propre de la logique d'accès aux données
- ✅ **Support des DTOs** - Objets de transfert de données typés
- ✅ **Value Objects** - Pays, Code postal, Coordonnées, Date/Heure
- ✅ **Support des métadonnées** - Stockez des données supplémentaires au format JSON
- ✅ **Suppression douce** - Suppression sécurisée avec possibilité de restauration
- ✅ **Filtrage avancé** - Filtrez par type, ville, pays, code postal
- ✅ **Mises à jour brutes** - Mettez à jour avec support des valeurs NULL
- ✅ **Tests complets** - Couverture complète des tests d'intégration

---

## 📦 Installation

Installez le package via Composer :

```bash
composer require andydefer/laravel-addresses
```

### Publier les migrations

```bash
php artisan vendor:publish --tag=Addresses-migrations
```

### Exécuter les migrations

```bash
php artisan migrate
```

---

## ⚙️ Configuration

Le package est automatiquement découvert par Laravel. Aucune configuration supplémentaire n'est requise.

Si vous devez personnaliser le Service Provider, ajoutez-le manuellement dans `config/app.php` :

```php
'providers' => [
    // ...
    AndyDefer\LaravelAddresses\AddressesServiceProvider::class,
],
```

---

## 📖 Utilisation

### Ajouter une adresse

```php
use AndyDefer\LaravelAddresses\Services\AddressService;
use AndyDefer\LaravelAddresses\Records\AddressRecord;
use AndyDefer\LaravelAddresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;

class UserController extends Controller
{
    public function store(AddressService $addressService)
    {
        $user = User::find(1);

        $record = AddressRecord::from([
            'street' => '123 Rue Principale',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $addressService->add($user, $record);
        
        return $address;
    }
}
```

### Récupérer des adresses

#### Toutes les adresses

```php
$addresses = $addressService->all($user);
```

#### Par type

```php
$billingAddresses = $addressService->byType($user, AddressType::BILLING);
```

#### Adresse principale

```php
$primary = $addressService->primary($user);
```

#### Trouver par ID

```php
$address = $addressService->find($addressId);
```

#### Compter les adresses

```php
$count = $addressService->count($user);
```

#### Vérifier si un type existe

```php
$hasShipping = $addressService->hasType($user, AddressType::SHIPPING);
```

### Mettre à jour une adresse

#### Mise à jour avec un Record (DTO)

```php
$updateRecord = AddressRecord::from([
    'street' => '456 Nouvelle Rue',
    'city' => 'Lyon',
    'country' => Country::FR,
    'postal_code' => PostalCodeVO::from('69001'),
]);

$updated = $addressService->update($addressId, $updateRecord);
```

#### Mise à jour avec des données brutes (incluant NULL)

Utilisez `updateRaw()` lorsque vous devez définir des champs à `NULL` dans la base de données :

```php
// Définir les métadonnées à NULL
$updated = $addressService->updateRaw($addressId, [
    'metadata' => null,
]);

// Définir les coordonnées à NULL
$updated = $addressService->updateRaw($addressId, [
    'geo_coordinates' => null,
]);

// Mise à jour partielle
$updated = $addressService->updateRaw($addressId, [
    'street' => 'Rue Mise à Jour',
    'address_type' => AddressType::BILLING->value,
]);
```

### Supprimer une adresse

```php
$deleted = $addressService->delete($addressId);
// Retourne true si supprimé, false sinon
```

### Gestion de l'adresse principale

#### Définir une adresse comme principale

```php
$addressService->setPrimary($user, $addressId);
```
> **Note :** Cela rétrogradera automatiquement l'ancienne adresse principale en `AddressType::OTHER`.

#### Récupérer l'adresse principale

```php
$primary = $addressService->primary($user);
```

---

## 🔍 Filtrer les adresses

Utilisez `AddressFilterRecord` pour un filtrage avancé :

```php
use AndyDefer\LaravelAddresses\Records\AddressFilterRecord;
use AndyDefer\Repository\Records\FindByRecord;

$filter = new AddressFilterRecord(
    addressable_type: 'App\Models\User',
    addressable_id: 1,
    address_type: AddressType::BILLING,
    city: 'Paris',
    country: Country::FR,
    postal_code: '75001'
);

$findByRecord = new FindByRecord(
    filters: $filter,
    limit: 10,
    offset: 0,
    order_by: 'created_at',
    order_direction: 'desc'
);

$addresses = $addressRepository->findBy($findByRecord);
```

---

## 📚 Référence de l'API

### AddressService

| Méthode | Description | Retourne |
|---------|-------------|----------|
| `add(Model $addressable, AddressRecord $record)` | Créer une nouvelle adresse | `Model` |
| `update(int $addressId, AddressRecord $record)` | Mettre à jour une adresse avec DTO | `Model` |
| `updateRaw(int $addressId, array $data)` | Mettre à jour avec données brutes (NULL) | `Model` |
| `delete(int $addressId)` | Supprimer une adresse | `bool` |
| `all(Model $addressable)` | Récupérer toutes les adresses | `Collection` |
| `byType(Model $addressable, AddressType $type)` | Récupérer les adresses par type | `Collection` |
| `primary(Model $addressable)` | Récupérer l'adresse principale | `?Model` |
| `setPrimary(Model $addressable, int $addressId)` | Définir l'adresse principale | `void` |
| `find(int $addressId)` | Trouver une adresse par ID | `?Model` |
| `count(Model $addressable)` | Compter les adresses | `int` |
| `hasType(Model $addressable, AddressType $type)` | Vérifier si un type existe | `bool` |

### AddressType Enum

| Cas | Valeur |
|-----|--------|
| `AddressType::PRIMARY` | `'primary'` |
| `AddressType::BILLING` | `'billing'` |
| `AddressType::SHIPPING` | `'shipping'` |
| `AddressType::WORK` | `'work'` |
| `AddressType::OTHER` | `'other'` |

---

## 🎯 Value Objects

Le package supporte les Value Objects suivants de `andydefer/php-vo` :

| Value Object | Description | Exemple |
|--------------|-------------|---------|
| `Country` | Enum des pays | `Country::FR`, `Country::US` |
| `PostalCodeVO` | Code postal | `PostalCodeVO::from('75001')` |
| `CoordinatesVO` | Coordonnées géographiques | `CoordinatesVO::from(['latitude' => 48.8566, 'longitude' => 2.3522])` |
| `DateTimeVO` | Date/heure | `DateTimeVO::from('2024-01-01 12:00:00')` |

### Accesseurs dans le modèle Address

Le modèle `Address` fournit des accesseurs pratiques :

```php
$address = Address::find(1);

// Accès sous forme de Value Objects
$postalCode = $address->postalCode;      // PostalCodeVO
$coordinates = $address->coordinates;     // CoordinatesVO
$metadata = $address->metadata;           // StrictDataObject
$createdAt = $address->createdAt;         // DateTimeVO
$updatedAt = $address->updatedAt;         // DateTimeVO
$deletedAt = $address->deletedAt;         // DateTimeVO
```

---

## 🧪 Tests

### Exécuter les tests

```bash
composer test
```

### Exécuter uniquement les tests unitaires

```bash
composer test-unit
```

### Exécuter uniquement les tests d'intégration

```bash
composer test-integration
```

### Configuration des tests

Le package utilise `orchestra/testbench` pour les tests d'intégration avec une base de données SQLite en mémoire.

---

## 📝 Schéma de la base de données

```sql
CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    addressable_type VARCHAR(255) NOT NULL,
    addressable_id BIGINT UNSIGNED NOT NULL,
    street VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    country VARCHAR(2) NULL,
    postal_code VARCHAR(20) NULL,
    geo_coordinates JSON NULL,
    address_type VARCHAR(20) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_addressable (addressable_type, addressable_id),
    INDEX idx_address_type (addressable_type, addressable_id, address_type),
    INDEX idx_country (country),
    INDEX idx_postal_code (postal_code)
);
```

---

## 🔧 Développement

### Style de code

```bash
./vendor/bin/pint
```

### Analyse statique

```bash
./vendor/bin/phpstan analyse
./vendor/bin/psalm
```

---

## 📄 Journal des modifications

Veuillez consulter le [CHANGELOG](CHANGELOG.md) pour plus d'informations sur les modifications récentes.

---

## 🤝 Contribuer

Veuillez consulter [CONTRIBUTING](CONTRIBUTING.md) pour plus de détails.

### Flux de développement

1. Forkez le dépôt
2. Créez une branche de fonctionnalité (`git checkout -b feature/amazing-feature`)
3. Apportez vos modifications
4. Exécutez les tests (`composer test`)
5. Committez vos modifications (`git commit -m 'Ajouter une fonctionnalité géniale'`)
6. Poussez vers la branche (`git push origin feature/amazing-feature`)
7. Ouvrez une Pull Request

---

## 📦 Dépendances

- [`andydefer/php-vo`](https://github.com/andydefer/php-vo) - Value Objects
- [`andydefer/laravel-repository`](https://github.com/andydefer/laravel-repository) - Implémentation du pattern Repository

---

## 👨‍💻 Auteur

**Andy Kani**
- GitHub: [@andydefer](https://github.com/andydefer)
- Email: andykanidimbu@gmail.com

---

## 📄 Licence

La licence MIT (MIT). Veuillez consulter le [Fichier de licence](LICENSE.md) pour plus d'informations.

---

## ⭐ Support

Si vous trouvez ce package utile, n'hésitez pas à lui donner une ⭐ sur GitHub !

---

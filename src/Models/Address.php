<?php

declare(strict_types=1);

namespace AndyDefer\LaravelAddresses\Models;

use AndyDefer\DomainStructures\Services\EnumService;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelAddresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;
use AndyDefer\Repository\Proxies\AttributeProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $addressable_type
 * @property int $addressable_id
 * @property string|null $street
 * @property string|null $city
 * @property Country $country
 * @property PostalCodeVO|null $postal_code
 * @property array|null $geo_coordinates
 * @property AddressType $address_type
 * @property array|null $metadata
 * @property CoordinatesVO|null $coordinates
 * @property DateTimeVO|null $created_at
 * @property DateTimeVO|null $updated_at
 * @property DateTimeVO|null $deleted_at
 * @property-read Model|null $addressable
 */
final class Address extends Model
{
    use HasUuids;
    use SoftDeletes;

    private static ?EnumService $enumService = null;

    protected $table = 'addresses';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'street',
        'city',
        'country',
        'postal_code',
        'geo_coordinates',
        'address_type',
        'metadata',
    ];

    protected $casts = [
        'geo_coordinates' => 'array',
        'metadata' => 'array',
        'postal_code' => 'array',
        'country' => Country::class,
        'address_type' => AddressType::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    protected function postalCode(): Attribute
    {
        return AttributeProxy::nullable(
            PostalCodeVO::class,
            column: 'postal_code',
        );
    }

    protected function coordinates(): Attribute
    {
        return AttributeProxy::nullable(
            CoordinatesVO::class,
            column: 'geo_coordinates',
        );
    }

    protected function metadata(): Attribute
    {
        return AttributeProxy::nullable(
            StrictDataObject::class,
            column: 'metadata',
        );
    }

    protected function createdAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'created_at',
        );
    }

    protected function updatedAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'updated_at',
        );
    }

    protected function deletedAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'deleted_at',
        );
    }
}

<?php

declare(strict_types=1);

namespace AndyDefer\LaravelAddresses\Models;

use AndyDefer\DomainStructures\Services\EnumService;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelAddresses\Enums\AddressType;
use AndyDefer\LaravelUtils\Proxies\AttributeProxy;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Address model representing physical addresses in the system.
 *
 * This model handles polymorphic addresses that can be attached to any
 * addressable model (User, Pharmacy, Hospital, etc.).
 *
 * @property int $id
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
    use SoftDeletes;

    private static ?EnumService $enumService = null;

    /** @var string The database table name */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get the parent addressable model (polymorphic).
     *
     * @return MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Get the postal code as a value object.
     */
    protected function postalCode(): Attribute
    {
        return AttributeProxy::nullable(
            PostalCodeVO::class,
            column: 'postal_code',
        );
    }

    /**
     * Get the geo coordinates as a CoordinatesVO.
     */
    protected function coordinates(): Attribute
    {
        return AttributeProxy::nullable(
            CoordinatesVO::class,
            column: 'geo_coordinates',
        );
    }

    /**
     * Get the metadata as a StrictDataObject.
     */
    protected function metadata(): Attribute
    {
        return AttributeProxy::nullable(
            StrictDataObject::class,
            column: 'metadata',
        );
    }

    /**
     * Get the created at timestamp as a DateTimeVO.
     */
    protected function createdAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'created_at',
        );
    }

    /**
     * Get the updated at timestamp as a DateTimeVO.
     */
    protected function updatedAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'updated_at',
        );
    }

    /**
     * Get the deleted at timestamp as a DateTimeVO (soft delete).
     */
    protected function deletedAt(): Attribute
    {
        return AttributeProxy::nullable(
            DateTimeVO::class,
            column: 'deleted_at',
        );
    }
}

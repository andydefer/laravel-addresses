<?php

declare(strict_types=1);

namespace AndyDefer\LaravelAddresses\Datas;

use AndyDefer\DomainStructures\Abstracts\AbstractData;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelAddresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;

final class AddressData extends AbstractData
{
    public function __construct(
        public readonly int $id,
        public readonly string $addressableType,
        public readonly int $addressableId,
        public readonly string $street,
        public readonly string $city,
        public readonly Country $country,
        public readonly ?PostalCodeVO $postalCode = null,
        public readonly ?CoordinatesVO $geoCoordinates = null,
        public readonly AddressType $addressType = AddressType::PRIMARY,
        public readonly ?StrictDataObject $metadata = null,
        public readonly ?DateTimeVO $createdAt = null,
        public readonly ?DateTimeVO $updatedAt = null,
        public readonly ?DateTimeVO $deletedAt = null,
    ) {}
}

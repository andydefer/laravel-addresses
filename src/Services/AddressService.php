<?php

declare(strict_types=1);

namespace AndyDefer\LaravelAddresses\Services;

use AndyDefer\LaravelAddresses\Enums\AddressType;
use AndyDefer\LaravelAddresses\Records\AddressFilterRecord;
use AndyDefer\LaravelAddresses\Records\AddressRecord;
use AndyDefer\LaravelAddresses\Repositories\AddressRepository;
use AndyDefer\Repository\Records\FindByRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AddressService
{
    public function __construct(
        private readonly AddressRepository $addressRepository,
    ) {}

    public function add(Model $addressable, AddressRecord $record): Model
    {
        $record = AddressRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            ...$record->toArrayWithoutNulls(),
        ]);

        return $this->addressRepository->create($record);
    }

    public function update(int|string $addressId, AddressRecord $record): Model
    {
        return $this->addressRepository->update($addressId, $record);
    }

    /**
     * Update an address with raw array data.
     * Use this when you need to set fields to NULL in the database.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateRaw(int|string $addressId, array $data): Model
    {
        return $this->addressRepository->updateRaw($addressId, $data);
    }

    public function delete(int|string $addressId): bool
    {
        return $this->addressRepository->delete($addressId);
    }

    public function all(Model $addressable): Collection
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        return $this->addressRepository->findBy(new FindByRecord(filters: $filter));
    }

    public function byType(Model $addressable, AddressType $type): Collection
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        return $this->addressRepository->findBy(new FindByRecord(filters: $filter));
    }

    public function primary(Model $addressable): ?Model
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => AddressType::PRIMARY,
        ]);

        $findByRecord = new FindByRecord(
            filters: $filter,
            limit: 1,
        );

        return $this->addressRepository->findBy($findByRecord)->first();
    }

    public function setPrimary(Model $addressable, int|string $addressId): void
    {
        $oldPrimary = $this->primary($addressable);

        if ($oldPrimary) {
            $oldPrimaryRecord = AddressRecord::from([
                'address_type' => AddressType::OTHER,
            ]);
            $this->addressRepository->update($oldPrimary->id, $oldPrimaryRecord);
        }

        $newPrimaryRecord = AddressRecord::from([
            'address_type' => AddressType::PRIMARY,
        ]);
        $this->addressRepository->update($addressId, $newPrimaryRecord);
    }

    public function find(int|string $addressId): ?Model
    {
        return $this->addressRepository->find($addressId);
    }

    public function count(Model $addressable): int
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        return $this->addressRepository->count($filter);
    }

    public function hasType(Model $addressable, AddressType $type): bool
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        return $this->addressRepository->exists($filter);
    }
}

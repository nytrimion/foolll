<?php

declare(strict_types=1);

namespace Fulll\Domain\Aggregate;

use Fulll\Domain\Entity\Vehicle;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Exception\VehicleNotRegisteredException;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;

final class Fleet
{
    /** @var array<string, Vehicle> */
    private array $vehicles = [];

    public function __construct(
        public readonly FleetId $id,
        public readonly UserId $userId,
    ) {
    }

    /**
     * @throws VehicleAlreadyRegisteredException
     */
    public function register(PlateNumber $plateNumber): void
    {
        if ($this->hasVehicle($plateNumber)) {
            throw VehicleAlreadyRegisteredException::in($this->id, $plateNumber);
        }

        $this->vehicles[$plateNumber->value] = new Vehicle($plateNumber);
    }

    public function hasVehicle(PlateNumber $plateNumber): bool
    {
        return isset($this->vehicles[$plateNumber->value]);
    }

    /**
     * @throws VehicleNotRegisteredException
     */
    public function localize(PlateNumber $plateNumber, Location $location): void
    {
        $this->vehicle($plateNumber)->localize($location);
    }

    /**
     * @throws VehicleNotRegisteredException
     */
    public function locationOf(PlateNumber $plateNumber): ?Location
    {
        return $this->vehicle($plateNumber)->location;
    }

    /**
     * @throws VehicleNotRegisteredException
     */
    private function vehicle(PlateNumber $plateNumber): Vehicle
    {
        if (!$this->hasVehicle($plateNumber)) {
            throw VehicleNotRegisteredException::in($this->id, $plateNumber);
        }

        return $this->vehicles[$plateNumber->value];
    }
}

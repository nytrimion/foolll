<?php

declare(strict_types=1);

namespace Fulll\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommand;
use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommandHandler;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQuery;
use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQueryHandler;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQuery;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQueryHandler;
use Fulll\Domain\Exception\VehicleAlreadyParkedException;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\Assert;

final class FeatureContext implements Context
{
    private readonly FleetRepository $fleetRepository;
    private ?FleetId $myFleetId = null;
    private ?FleetId $otherFleetId = null;
    private ?PlateNumber $plateNumber = null;
    private ?Location $location = null;
    private ?\Throwable $caughtException = null;
    private int $sequence = 0;

    public function __construct()
    {
        $this->fleetRepository = new InMemoryFleetRepository();
    }

    #[Given('my fleet')]
    public function myFleet(): void
    {
        $this->myFleetId = $this->createFleet();
    }

    #[Given('the fleet of another user')]
    public function theFleetOfAnotherUser(): void
    {
        $this->otherFleetId = $this->createFleet();
    }

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->plateNumber = new PlateNumber('AB-123-CD');
    }

    #[Given('a location')]
    public function aLocation(): void
    {
        $this->location = new Location(48.85, 2.35);
    }

    #[Given("this vehicle has been registered into the other user's fleet")]
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet(): void
    {
        new RegisterVehicleCommandHandler($this->fleetRepository)->handle(
            new RegisterVehicleCommand($this->otherFleetId(), $this->plateNumber()),
        );
    }

    #[Given('I have registered this vehicle into my fleet')]
    #[When('I register this vehicle into my fleet')]
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        $this->registerVehicle();
    }

    #[When('I try to register this vehicle into my fleet')]
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        try {
            $this->registerVehicle();
        } catch (\Throwable $exception) {
            $this->caughtException = $exception;
        }
    }

    #[Then('I should be informed this this vehicle has already been registered into my fleet')]
    public function iShouldBeInformedThisVehicleHasAlreadyBeenRegistered(): void
    {
        Assert::assertInstanceOf(VehicleAlreadyRegisteredException::class, $this->caughtException);
    }

    #[Then('this vehicle should be part of my vehicle fleet')]
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        $isRegistered = new IsVehicleRegisteredQueryHandler($this->fleetRepository)->handle(
            new IsVehicleRegisteredQuery($this->myFleetId(), $this->plateNumber()),
        );

        Assert::assertTrue($isRegistered);
    }

    #[Given('my vehicle has been parked into this location')]
    #[When('I park my vehicle at this location')]
    public function iParkMyVehicleAtThisLocation(): void
    {
        $this->parkVehicle();
    }

    #[When('I try to park my vehicle at this location')]
    public function iTryToParkMyVehicleAtThisLocation(): void
    {
        try {
            $this->parkVehicle();
        } catch (\Throwable $exception) {
            $this->caughtException = $exception;
        }
    }

    #[Then('the known location of my vehicle should verify this location')]
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation(): void
    {
        $location = new GetVehicleLocationQueryHandler($this->fleetRepository)->handle(
            new GetVehicleLocationQuery($this->myFleetId(), $this->plateNumber()),
        );

        Assert::assertTrue($location?->equals($this->location()));
    }

    #[Then('I should be informed that my vehicle is already parked at this location')]
    public function iShouldBeInformedMyVehicleIsAlreadyParked(): void
    {
        Assert::assertInstanceOf(VehicleAlreadyParkedException::class, $this->caughtException);
    }

    private function parkVehicle(): void
    {
        new LocalizeVehicleCommandHandler($this->fleetRepository)->handle(
            new LocalizeVehicleCommand($this->myFleetId(), $this->plateNumber(), $this->location()),
        );
    }

    private function registerVehicle(): void
    {
        new RegisterVehicleCommandHandler($this->fleetRepository)->handle(
            new RegisterVehicleCommand($this->myFleetId(), $this->plateNumber()),
        );
    }

    private function createFleet(): FleetId
    {
        $fleetId = new FleetId('fleet-' . ++$this->sequence);

        new CreateFleetCommandHandler($this->fleetRepository)->handle(
            new CreateFleetCommand($fleetId, new UserId('user-' . $this->sequence)),
        );

        return $fleetId;
    }

    private function myFleetId(): FleetId
    {
        if ($this->myFleetId === null) {
            throw new \LogicException('No fleet in the scenario context.');
        }

        return $this->myFleetId;
    }

    private function otherFleetId(): FleetId
    {
        if ($this->otherFleetId === null) {
            throw new \LogicException('No other fleet in the scenario context.');
        }

        return $this->otherFleetId;
    }

    private function plateNumber(): PlateNumber
    {
        if ($this->plateNumber === null) {
            throw new \LogicException('No vehicle in the scenario context.');
        }

        return $this->plateNumber;
    }

    private function location(): Location
    {
        if ($this->location === null) {
            throw new \LogicException('No location in the scenario context.');
        }

        return $this->location;
    }
}

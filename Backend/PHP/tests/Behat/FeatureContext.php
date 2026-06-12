<?php

declare(strict_types=1);

namespace Fulll\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQuery;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQueryHandler;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\Assert;

final class FeatureContext implements Context
{
    private readonly FleetRepository $fleetRepository;
    private ?FleetId $myFleetId = null;
    private ?PlateNumber $plateNumber = null;
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

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->plateNumber = new PlateNumber('AB-123-CD');
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

    private function plateNumber(): PlateNumber
    {
        if ($this->plateNumber === null) {
            throw new \LogicException('No vehicle in the scenario context.');
        }

        return $this->plateNumber;
    }
}

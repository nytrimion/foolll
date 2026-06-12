<?php

declare(strict_types=1);

namespace Fulll\Tests\Feature\Infra\Console;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQuery;
use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQueryHandler;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use Fulll\Infra\Console\FleetConsole;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class FleetLocalizeVehicleCommandTest extends TestCase
{
    private InMemoryFleetRepository $fleetRepository;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->tester = new CommandTester(
            FleetConsole::application($this->fleetRepository)->find('localize-vehicle'),
        );
    }

    public function testLocalizesVehicle(): void
    {
        $fleetId = $this->registerVehicle();

        $this->tester->execute([
            'fleetId' => $fleetId,
            'plateNumber' => 'AB-123-CD',
            'lat' => '48.85',
            'lng' => '2.35',
        ]);

        $this->tester->assertCommandIsSuccessful();
        self::assertTrue($this->locationOf($fleetId, 'AB-123-CD')?->equals(new Location(48.85, 2.35)));
    }

    public function testLocalizesVehicleWithAltitude(): void
    {
        $fleetId = $this->registerVehicle();

        $this->tester->execute([
            'fleetId' => $fleetId,
            'plateNumber' => 'AB-123-CD',
            'lat' => '48.85',
            'lng' => '2.35',
            'alt' => '35',
        ]);

        $this->tester->assertCommandIsSuccessful();
        self::assertSame(35.0, $this->locationOf($fleetId, 'AB-123-CD')?->altitude);
    }

    public function testFailsWhenVehicleIsNotRegistered(): void
    {
        $fleetId = $this->createFleet();

        $exitCode = $this->tester->execute([
            'fleetId' => $fleetId,
            'plateNumber' => 'AB-123-CD',
            'lat' => '48.85',
            'lng' => '2.35',
        ]);

        self::assertSame(Command::FAILURE, $exitCode);
    }

    public function testRejectsNonNumericCoordinates(): void
    {
        $fleetId = $this->registerVehicle();

        $exitCode = $this->tester->execute([
            'fleetId' => $fleetId,
            'plateNumber' => 'AB-123-CD',
            'lat' => 'not a number',
            'lng' => '2.35',
        ]);

        self::assertSame(Command::INVALID, $exitCode);
    }

    private function registerVehicle(): string
    {
        $fleetId = $this->createFleet();

        new RegisterVehicleCommandHandler($this->fleetRepository)->handle(
            new RegisterVehicleCommand(new FleetId($fleetId), new PlateNumber('AB-123-CD')),
        );

        return $fleetId;
    }

    private function createFleet(): string
    {
        $fleetId = new FleetId('fleet-1');

        new CreateFleetCommandHandler($this->fleetRepository)->handle(
            new CreateFleetCommand($fleetId, new UserId('user-1')),
        );

        return $fleetId->value;
    }

    private function locationOf(string $fleetId, string $plateNumber): ?Location
    {
        return new GetVehicleLocationQueryHandler($this->fleetRepository)->handle(
            new GetVehicleLocationQuery(new FleetId($fleetId), new PlateNumber($plateNumber)),
        );
    }
}

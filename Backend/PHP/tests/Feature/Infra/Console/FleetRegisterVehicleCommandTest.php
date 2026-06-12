<?php

declare(strict_types=1);

namespace Fulll\Tests\Feature\Infra\Console;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQuery;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQueryHandler;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use Fulll\Infra\Console\FleetConsole;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class FleetRegisterVehicleCommandTest extends TestCase
{
    private InMemoryFleetRepository $fleetRepository;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->tester = new CommandTester(
            FleetConsole::application($this->fleetRepository)->find('register-vehicle'),
        );
    }

    public function testRegistersVehicle(): void
    {
        $fleetId = $this->createFleet();

        $this->tester->execute(['fleetId' => $fleetId, 'plateNumber' => 'AB-123-CD']);

        $this->tester->assertCommandIsSuccessful();
        self::assertTrue($this->isRegistered($fleetId, 'AB-123-CD'));
    }

    public function testFailsWhenFleetIsUnknown(): void
    {
        $exitCode = $this->tester->execute(['fleetId' => 'unknown', 'plateNumber' => 'AB-123-CD']);

        self::assertSame(Command::FAILURE, $exitCode);
    }

    private function createFleet(): string
    {
        $fleetId = new FleetId('fleet-1');

        new CreateFleetCommandHandler($this->fleetRepository)->handle(
            new CreateFleetCommand($fleetId, new UserId('user-1')),
        );

        return $fleetId->value;
    }

    private function isRegistered(string $fleetId, string $plateNumber): bool
    {
        return new IsVehicleRegisteredQueryHandler($this->fleetRepository)->handle(
            new IsVehicleRegisteredQuery(new FleetId($fleetId), new PlateNumber($plateNumber)),
        );
    }
}

<?php
namespace App\Tests\Command;

use App\Command\PullWeatherDataCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\Error;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PullWeatherDataCommandTest extends KernelTestCase {
    /**
     * @expectedException Error
     */
    public function testFailingSourceSyntax() {
        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:pull-weather-data');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'source' => 'example.com',
        ));
    }

    /**
     * @expectedException Error
     */
    public function testFailingSourceNotFound() {
        $kernel = static::createKernel();

        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:pull-weather-data');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'source' => 'this.does.not.exist.localhost/really/it/does/not',
        ));
    }
}

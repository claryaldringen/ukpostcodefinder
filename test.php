<?php

require_once 'main.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SearchCommandTest extends KernelTestCase {

    public function testExecute() {

        self::bootKernel();
        $application = new Application(self::$kernel);

        $response = ['GetUKLocationByTownResult' => '<Table><Town>London Arena</Town><PostCode>E14</PostCode></Table>'];

        $soapMock = $this->createMock('SoapClient')->disableOriginalConstructor()->getMock();
        $soapMock->method('GetUKLocationByTown')->with(['Town' => 'London'])->willReturn($response);

        $soapClientBuilderMock = $this->createMock('SoapClientBuilder')->getMock();
        $soapClientBuilderMock->method('getSoapClient')->with('http://www.webservicex.net/uklocation.asmx?WSDL')->willReturn($soapMock);

        $application->add(new SearchCommand('search', $soapClientBuilderMock, 'http://www.webservicex.net/uklocation.asmx?WSDL'));
        $command = $application->find('search');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'cities' => '']);
        $output = $commandTester->getDisplay();
        $this->assertContains('London Arena: E14', $output);
    }
}
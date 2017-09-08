<?php

require_once './vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SearchCommand extends Command {

    private $soapClientBuilder = null;
    private $wdsl = null;

    public function __construct($name, SoapClientBuilder $builder, $wdsl)
    {
        parent::__construct($name);
        $this->soapClientBuilder = $builder;
        $this->wdsl = $wdsl;
    }

    /**
     * Returns array of cities, thrwos Exception if number of cities is less than 2 or higher than 3.
     *
     * @param $string City names separated by comma.
     * @return string[]
     * @throws Exception
     */
    protected function parseCities($string) {
        $cities = explode(',', $string);
        $cnt = count($cities);
        if($cnt < 2 || $cnt > 3) throw new Exception('Exactly two or three towns separated by comma are required.');
        foreach ($cities as $i => $city) {
            $cities[$i] = trim($city);
        }
        return $cities;
    }

    protected function configure() {
        $this->setDescription('Returns postcodes of the selected UK cities.')
            ->setHelp('')
            ->addArgument('cities', InputArgument::REQUIRED, 'Two or three names of cities in the UK.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $cities = $this->parseCities($input->getArgument('cities'));
        } catch(Exception $ex) {
            $output->writeln($ex->getMessage());
            return;
        }
        $towns = [];
        foreach ($cities as $city) {
            try {
                $response = $this->soapClientBuilder->getSoapClient($this->wdsl)->GetUKLocationByTown(['Town' => $city]);
            } catch(Exception $ex) {
                $output->writeln('SOAP Server has gone away.');
                return;
            }
            $xml = simplexml_load_string($response->GetUKLocationByTownResult);
            foreach($xml->Table as $cityElement) {
                $town = $cityElement->Town->__toString();
                if(empty($towns[$town])) $towns[$town] = [];
                $towns[$town][] = $cityElement->PostCode->__toString();
            }
        }

        foreach ($towns as $town => $postCodes) {
            $output->writeln($town . ': ' . implode(', ', $postCodes));
        }
    }
}

class SoapClientBuilder {

    private $soapClients = [];

    /**
     * Returns instance of SoapClient.
     *
     * @param $wdsl Url of WDSL source.
     * @return SoapClient
     * @throws Exception
     */
    public function getSoapClient($wdsl) {
        if(empty($this->soapClients[$wdsl])) {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $wdsl);
            curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
            curl_exec($handle);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            if($httpCode != 200) {
                throw new Exception('SOAP Server has gone away.');
            }
            $this->soapClients[$wdsl] = new SoapClient($wdsl);
        }
        return $this->soapClients[$wdsl];
    }
}

$application = new Application();
$application->add(new SearchCommand('search', new SoapClientBuilder(), 'http://www.webservicex.net/uklocation.asmx?WSDL'));
$application->run();

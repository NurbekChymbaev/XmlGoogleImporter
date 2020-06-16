<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    protected function configure()
    {
        $this->addOption('source_file', null, InputOption::VALUE_REQUIRED)
            ->addOption('client_credentials', null, InputOption::VALUE_REQUIRED)
            ->addOption('ftp_username', null, InputOption::VALUE_OPTIONAL)
            ->addOption('ftp_password', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source_file = $input->getOption('source_file');

        if ($this->isRemote($source_file)) {
            $source_file = $this->downloadFile($input);
        }

        $importer = new Importer($source_file);
        $importer->doImport();
    }

    private function isRemote($source)
    {
        return filter_var($source, FILTER_VALIDATE_URL);
    }

    protected function downloadFile($input)
    {
        $url = $input->getOption('source_file');
        $username = $input->getOption('ftp_username');
        $password = $input->getOption('ftp_password');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (($username || $password) && substr($url, 0, 3) === 'ftp') {
            curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        }
        $con = curl_exec($curl);
        curl_close($curl);
        $path = date('Y-m-d-H-i') . '-' . basename($url);

        file_put_contents($path, $con);

        return $path;
    }
}
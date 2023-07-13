<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Patient;

class ImportExistingPatientsCommand extends Command
{
    /** @var PatientRepository */
    private $patientRepository;

    protected static $defaultName = 'app:import-existing-patients';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import existing patients')
            ->setHelp('This command imports existing patients into the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    { 
        // Update the path to the existing patients file
        $filename = __DIR__ . '/../../public/csv/patient.csv';

        if (($handle = fopen($filename, 'r')) !== false) {
            // Skip the header row if it exists
            fgetcsv($handle);

            // Import each patient
            while (($data = fgetcsv($handle)) !== false) {
                $patient = new Patient();
                $patient->setTitle($data[0]);
                $patient->setFirstName($data[1]);
                $patient->setSurname($data[2]);
                $patient->setDob(new \DateTime($data[3]));
                $patient->setEmail($data[4]);
                $patient->setPhone($data[5]);
                $patient->setAddress($data[6]);
                $patient->setStatus('referred');
                $patient->setCreatedAt(new \DateTime());
                $this->entityManager->persist($patient);
            }

            $this->entityManager->flush();

            fclose($handle);

            $output->writeln('Existing patients imported successfully.');
        } else {
            $output->writeln('Failed to open the existing patients file.');
        }

        return Command::SUCCESS;
    }
}

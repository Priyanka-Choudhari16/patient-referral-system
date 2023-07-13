<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Patient;
use Symfony\Component\HttpFoundation\JsonResponse;

class PatientController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PatientRepository $patientRepository,
    ) {
        $this->entityManager  = $entityManager;
        $this->patientRepository   = $patientRepository;
    }

    private function isAdmin()
    {
        // Logic to check if the user is an admin staff
        // Return true if the user is an admin, otherwise return false
        return true;
    }

    public function newPatient(Request $request)
    {
        if (!$this->isAdmin()) {
            throw throw new Exception("Access Denied: Admin staff only can access the system.");
        }

        try {
            if ($request->isMethod('POST')) {
                // Retrieve form data
                $title = $request->request->get('title');
                $firstName = $request->request->get('first_name');
                $surname = $request->request->get('surname');
                $dob = $request->request->get('dob');
                $email = $request->request->get('email');
                $phone = $request->request->get('phone');
                $address = $request->request->get('address');

                // Create a new patient entity
                $patient = new Patient();
                $patient->setTitle($title);
                $patient->setFirstName($firstName);
                $patient->setSurname($surname);
                $patient->setDob(new \DateTime($dob));
                $patient->setEmail($email);
                $patient->setPhone($phone);
                $patient->setAddress($address);
                $patient->setStatus("referred");
                $patient->setCreatedAt(new \DateTime());

                //check if patient record already exists
                $patientRecord = $this->entityManager->getRepository(Patient::class)->findOneBy(['email' => $email]);

                if (empty($patientRecord)) {

                    // Persist the patient to the database
                    $this->entityManager->persist($patient);
                    $this->entityManager->flush();
                }
                return $this->redirectToRoute('app_patient_review');
            }
            return $this->render('patient/index.html.twig');
        } catch (Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }


    //show list of referred, accepted and rejected patients
    public function reviewPatients(Request $request)
    {
        if (!$this->isAdmin()) {
            throw throw new Exception("Access Denied: Admin staff only can access the system.");
        }

        try {
            $repository = $this->entityManager->getRepository(Patient::class);
            $referredPatients = $repository->findBy(['status' => 'referred']);
            $acceptedPatients = $repository->findBy(['status' => 'accepted']);
            $rejectedPatients = $repository->findBy(['status' => 'rejected']);

            return $this->render('patient/review.html.twig', [
                'referredPatients' => $referredPatients,
                'acceptedPatients' => $acceptedPatients,
                'rejectedPatients' => $rejectedPatients,
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }


    //Update the patient status to accepted
    public function acceptedPatients(Patient $patient)
    {
        $patient->setStatus('accepted');
        $this->entityManager->persist($patient);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_patient_review');
    }

    //Update the patient status to rejected
    public function rejectedPatients(Patient $patient)
    {
        $patient->setStatus('rejected');
        $this->entityManager->persist($patient);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_patient_review');
    }
}

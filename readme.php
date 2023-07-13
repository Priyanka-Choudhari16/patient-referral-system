<!-- database configuration is done inside .env file -->
<!-- DB name : referral_system -->

<!-- For maintaing patient data, Patient entity is created with with required fields -->
<!-- To create database and entity run following command -->

<!-- 
    php bin/console doctrine:database:create 

    migration file is already created for Patient table. To create table inside db run below command
    php bin/console doctrine:migrations:migrate
-->

<!-- all the functionality has written inside src/controller/PatientController -->

<!-- 
    Importing existing data functionality written inside src/command/ImportExistingPatientsCommand 

    To run above method run following command:
    bin/console app:import-existing-patients

-->

<!-- 
    To see the referral new patient form hit below route from localhost
    http://localhost/new-patients
 -->

 <!-- after creating new patient it will redirect to review page, where you can see the list of referred patient, 
 accepted and rejected patients -->

 <!-- For restricting other users from accessing resourse, we can maintain user table with user type.
   If user is not staff then isAdmin method will throw exception and restrict to user.
   If user is staff then isAdmin method will return true and it will allow you to access the system.
-->
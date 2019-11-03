<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Output\OutputInterface;

class Validator extends BasicRules
{
    public function index(OutputInterface $output)
    {
        $attempts = array();
        $handle = fopen(dirname(__FILE__) . "/input.csv", "r");
        
        $basicRule = new BasicRules();
        
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $request_date = new \DateTime($data[0]);
            $country_code = $data[1];
            $document_type = $data[2];
            $document_number = $data[3];
            $issue_date = new \DateTime($data[4]);
            $id_number = $data[5];
            $request_track = $data[5] . '0';
            $object = new \stdClass();
            $object->id = $id_number;
            
            if (isset($attempts[$id_number])) {
                $day_range = ($request_date->diff($attempts[$request_track]->first))->days;
                if ($day_range <= 7) {
                    $attempts[$id_number] ++;
                }
            } else {
                $object->first = $request_date;
                $last_request_date = clone $request_date;
                $last_request_date = $last_request_date->modify('+7 days');
                $attempts[$request_track] = $last_request_date;
                $attempts[$id_number] = 1;
                $attempts[$request_track] = $object;
            }

            $diff = $request_date->diff($issue_date);
            $day = date('l', strtotime($data[4]));

            $validYear = 5;
            $document_length = 8;

            $weekends = array(
                'Saturday',
                'Sunday');
            $allowed_documents_for_all = array(
                'passport',
                'identity_card',
                'residence_permit');
            $output->writeln('');

            if ($country_code == 'de') {
                //If issue date greater than 2010-01-01 then expiry 10 years
                if ($document_type == 'identity_card') {
                    $validYear = 10;
                }
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } elseif ($country_code == 'es') {
                //2013-02-14    15 years            passports  50001111 to 50009999 not valid
                if (($document_type == 'passport') && ($issue_date >= new \DateTime('2013-02-14'))) {
                    $validYear = 15;
                }
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } elseif ($country_code == 'fr') {
                //5 years also driving license allowed
                array_push($allowed_documents_for_all, "drivers_license");
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } elseif ($country_code == 'pl') {
                //residence permits, issued after 2015-06-01 after 2018-09-01 the lenght will be 10 symbols
                if (($document_type == 'identity_card') && ($issue_date >= new \DateTime('2018-09-01'))) {
                    $document_length = 10;
                }
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } elseif ($country_code == 'it') {
                //2019-01-01 document office will be working overtime on Saturdays until 2019-01-31
                if (($issue_date >= new \DateTime('2019-01-01')) && ($issue_date <= new \DateTime('2019-01-31'))) {
                    $weekends = array('Sunday');
                }
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } elseif ($country_code == 'uk') {
                //after 2019-01-01 only passports will be accepted
                if ($request_date > new \DateTime('2019-01-01')) {
                    $allowed_documents_for_all = array('passport');
                }
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            } else {
                $basicRule->bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number);
            }
        }
        fclose($handle);
        exit;
    }
}

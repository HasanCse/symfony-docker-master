<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class BasicRules
{
    const MAX_ATTEMPT = 3;
    const STOLEN_PASSPORT_COUNTRY = 'es';
    const STOLEN_PASSPORT_START = 50001111;
    const STOLEN_PASSPORT_END = 50009999;
    
    public function bascicRules($country_code, $document_type, $document_number, $document_length, $day, $weekends, $allowed_documents_for_all, $diff, $validYear, $attempts, $id_number)
    {
        if ($attempts[$id_number] < self::MAX_ATTEMPT) {
            if (in_array($document_type, $allowed_documents_for_all)) {
                if (!(($country_code == self::STOLEN_PASSPORT_COUNTRY) && (in_array($document_number, range(self::STOLEN_PASSPORT_START, self::STOLEN_PASSPORT_END))))) {
                    if ($this->dateChecker($diff->y, $diff->m, $diff->d, $validYear)) {
                        $this->docLengthChecker($day, $weekends, $document_number, $document_length);
                    } else {
                        echo 'document_is_expired';
                    }
                } else {
                    echo 'document_number_invalid';
                }
            } else {
                echo 'document_type_is_invalid';
            }
        } else {
            echo 'request_limit_exceeded';
        }
    }

    public function dateChecker($year, $month, $day, $validYear)
    {
        if ($year < $validYear) {
            return true;
            if (($year == $validYear) && ($month > 0 || $day > 0)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function weekendChecker($day, $weekends)
    {
        if (!in_array($day, $weekends)) {
            $response = 'valid';
            echo $response;
            return $response;
        } else {
            $response = 'document_issue_date_invalid';
            echo $response;
            return $response;
        }
    }

    public function docLengthChecker($day, $weekends, $document_number, $document_length)
    {
        if (strlen($document_number) == $document_length) {
            $response = $this->weekendChecker($day, $weekends);
            return $response;
        } else {
            $response = 'document_number_length_invalid';
            echo $response;
            return $response;
        }
    }
}

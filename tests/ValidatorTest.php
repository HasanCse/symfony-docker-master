<?php

namespace Tests;

use App\Controller;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{

    /**
     * @dataProvider weekDataProviderForTest
     */
    public function testWeekend($day, $response)
    {
        $validatorTest = new Controller\Validator();
        $weekends = array(
            'Saturday',
            'Sunday');

        $weekendCheck = $validatorTest->weekendChecker($day, $weekends);
        $this->assertEquals($response, $weekendCheck);
    }

    public function weekDataProviderForTest()
    {
        return [
            'Weekend' => ['Sunday', 'document_issue_date_invalid'],
            'Working Day' => ['Monday', 'valid'],
            'Working Day' => ['Tuesday', 'document_issue_date_invalid'] //Testing failing case
        ];
    }

    /**
     * @dataProvider lengthDataProviderForTest
     */
    public function testDocLength($document_number, $document_length, $response)
    {
        $validatorTest = new Controller\Validator();
        $weekends = array(
            'Saturday',
            'Sunday');

        $docLengthCheck = $validatorTest->docLengthChecker('Monday', $weekends, $document_number, $document_length);
        $this->assertEquals($response, $docLengthCheck);
    }

    public function lengthDataProviderForTest()
    {
        return [
            'Correct Length' => [12345678, 8, 'valid'],
            'Incorrect Length' => [1234567, 8, 'document_number_length_invalid'],
            'Incorrect Length' => [9012345678, 11, 'valid'] //Testing failing case
        ];
    }

    /**
     * @dataProvider dateDataProviderForTest
     */
    public function testDateChecker($year, $month, $day, $validYear, $response)
    {
        $validatorTest = new Controller\Validator();

        $dateCheck = $validatorTest->dateChecker($year, $month, $day, $validYear);
        $this->assertTrue($response);
    }

    public function dateDataProviderForTest()
    {
        return [
            'Correct Year' => [2018,0,0, 2019, true],
            'Incorrect Year' => [2020,0,0, 2019, true],
            'Incorrect Year' => [2021,0,0, 2019, false] //Testing failing case
        ];
    }
}

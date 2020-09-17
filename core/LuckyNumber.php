<?php

namespace Core;

/**
 * Class LuckyNumber
 * @package Сore
 */
class LuckyNumber
{

    const ITERATION_BORDER = 3;

    /**
     * Get the number of the last three numbers. Recurse.
     * @param integer $number
     * @return integer
     */
    private function getIntEndThree(int $number): int
    {
        $snumber = strval($number);
        $splitNumber = str_split($snumber);
        $splitNumber = count($splitNumber) < self::ITERATION_BORDER ? $splitNumber : array_slice($splitNumber, count($splitNumber) - self::ITERATION_BORDER, count($splitNumber));
        $countSplit = count($splitNumber) > self::ITERATION_BORDER ? self::ITERATION_BORDER : count($splitNumber);
        $iCountOfSlice = count(array_slice($splitNumber, 0, $countSplit));
        $iCountOfSlice = $countSplit == 1 ? 1 : $iCountOfSlice;
        $responseNumber = 0;
        for ($i = 0; $i < $iCountOfSlice; $i++) {
            $responseNumber += $splitNumber[$i];
        }
        return $responseNumber > 9 ? $this->getIntEndThree($responseNumber) : $responseNumber;
    }

    /**
     * Get the number of the first three numbers. Recurse.
     * @param integer $number
     * @return integer
     */
    private function getIntFirstThree(int $number): int
    {
        $snumber = strval($number);
        $splitNumber = str_split($snumber);
        $countSplit = count($splitNumber) > self::ITERATION_BORDER ? self::ITERATION_BORDER : count($splitNumber);
        $iCountOfSlice = count(array_slice(str_split($snumber), 0, $countSplit));
        $iCountOfSlice = $countSplit == 1 ? 1 : $iCountOfSlice;
        $responseNumber = 0;
        for ($i = 0; $i < $iCountOfSlice; $i++) {
            $responseNumber += $snumber[$i];
        }
        return $responseNumber > 9 ? $this->getIntEndThree($responseNumber) : $responseNumber;
    }

    /**
     * Get an identity check
     * @param integer $number
     * @return mixed
     */
    private function getCheckOnIdentity(int $number)
    {
        if ($this->getIntFirstThree($number) == $this->getIntEndThree($number)) {
            return $number;
        }
        return false;
    }

    /**
     * Getting a lucky ticket number
     * @param array $getData
     * @return mixed
     */
    public function gettingLuckyNumber(array $getData)
    {
        $start = microtime(true);
        $listArray = [];
        if (is_array($getData) && !empty($getData) && array_key_exists('first', $getData) && array_key_exists('end', $getData)) {
            $firstNumber = $getData['first'];
            $EndNumber = $getData['end'];
            if ($firstNumber > $EndNumber && ($firstNumber - $EndNumber) > 100) {
                for ($numberI = $EndNumber; $numberI <= $firstNumber; $numberI++) {
                    if ($response = $this->getCheckOnIdentity($numberI))
                        $listArray[] = $response;
                }
            } elseif ($firstNumber < $EndNumber && ($EndNumber - $firstNumber) > 100) {
                for ($numberI = $firstNumber; $numberI <= $EndNumber; $numberI++) {
                    if ($response = $this->getCheckOnIdentity($numberI))
                        $listArray[] = $response;
                }
            } else {
                throw new \Exception('The difference between the range of the first and the end must be at least 100.');
            }
            $time = microtime(true) - $start;
            array_push($listArray, ['time' => 'Затраченное время(млс): ' . $time]);
            return $listArray;
        }
        throw new \Exception('Array processing error!');
    }

}

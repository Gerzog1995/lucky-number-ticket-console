<?php

namespace Core;

/**
 * Class LuckyNumber
 * @package Сore
 */
class LuckyNumber
{
    const ITERATION_BORDER = 3;
    const BORDER_NUMBER = 6;

    /**
     * Get an identity check
     * @param integer $number
     * @return mixed
     */
    public function getCheckOnIdentity(int $number)
    {
        if (count($this->getListOfNumber($number)) == self::BORDER_NUMBER) {
            return $this->getIntFirstThree($number) == $this->getIntEndThree($number) ? $number : false;
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
        $modelPcntlFork = new PcntlFork();
        if (is_array($getData) && !empty($getData) && array_key_exists('first', $getData) && array_key_exists('end', $getData)) {
            $firstNumber = $getData['first'];
            $EndNumber = $getData['end'];
            if (count($this->getListOfNumber($firstNumber)) <= self::BORDER_NUMBER && count($this->getListOfNumber($EndNumber)) <= self::BORDER_NUMBER) {
                if ($firstNumber > $EndNumber && ($firstNumber - $EndNumber) > 100) {
                    return $modelPcntlFork->sendForkOfNumber($firstNumber);
                } elseif ($firstNumber < $EndNumber && ($EndNumber - $firstNumber) > 100) {
                    return $modelPcntlFork->sendForkOfNumber($EndNumber);
                } else {
                    throw new \Exception('The difference between the range of the first and the end must be at least 100.');
                }
            }
            throw new \Exception('The range of numbers must be from 000001 to 999999!');
        }
        throw new \Exception('Array processing error!');
    }

    /**
     * Get the count of lucky ticket
     * @param array $listLuckyNumbers
     * @return array
     */
    public function getCountLuckyTicket(array $listLuckyNumbers): int
    {
        return count($listLuckyNumbers);
    }

    /**
     * Get the list of numbers.
     * @param integer $number
     * @return array
     */
    private function getListOfNumber(int $number): array
    {
        return str_split(strval($number));
    }

    /**
     * Get count of split
     * @param array $splitNumber
     * @return integer
     */
    private function getCountSplit(array $splitNumber): int
    {
        return count($splitNumber) > self::ITERATION_BORDER ? self::ITERATION_BORDER : count($splitNumber);;
    }

    /**
     * Get the number of the last three numbers. Recurse.
     * @param integer $number
     * @return integer
     */
    private function getIntEndThree(int $number): int
    {
        $splitNumber = $this->getListOfNumber($number);
        $splitNumber = count($splitNumber) < self::ITERATION_BORDER ? $splitNumber : array_slice($splitNumber, count($splitNumber) - self::ITERATION_BORDER, count($splitNumber));
        $countSplit = $this->getCountSplit($splitNumber);
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
        $splitNumber = $this->getListOfNumber($number);
        $countSplit = $this->getCountSplit($splitNumber);
        $iCountOfSlice = count(array_slice($splitNumber, 0, $countSplit));
        $iCountOfSlice = $countSplit == 1 ? 1 : $iCountOfSlice;
        $responseNumber = 0;
        for ($i = 0; $i < $iCountOfSlice; $i++) {
            $responseNumber += $splitNumber[$i];
        }
        return $responseNumber > 9 ? $this->getIntEndThree($responseNumber) : $responseNumber;
    }

}

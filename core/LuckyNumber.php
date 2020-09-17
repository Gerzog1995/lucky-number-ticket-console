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
        return $this->getIntFirstThree($number) == $this->getIntEndThree($number) ? $number : false;
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
                $oneNumber = round($EndNumber / 3);
                for ($i = 2; $i <= 6; $i++) {
                    $pid = pcntl_fork();
                    if ($pid == -1) {
                        echo 'Fork -1';
                        exit;
                    } elseif ($pid) {
                        $childPids[] = $pid;
                        if ($i == 6) {
                            $arrayRuntime = [];

                            foreach ($childPids as $childPid) {
                                pcntl_waitpid($childPid, $status);
                                $sharedId = shmop_open($childPid, 'a', 0, 0);
                                $shareData = shmop_read($sharedId, 0, shmop_size($sharedId));
                                $arrayRuntime = $arrayRuntime ? array_merge($arrayRuntime, json_decode($shareData, 1)) : json_decode($shareData, 1);
                                shmop_delete($sharedId);
                                shmop_close($sharedId);
                            }
                            $time = microtime(true) - $start;

                                print_r($arrayRuntime);
                            echo 'Затраченное время(млс): ' . $time . PHP_EOL;
                        }
                    } else {
                        $myPid = getmypid();
                        $listArray = [];

                        for ($numberI = (($oneNumber * $i) - $oneNumber); $numberI <= $oneNumber * $i; $numberI++) {
                            if ($response = $this->getCheckOnIdentity($numberI))
                                $listArray[] = $response;
                        }
                        $sharedId = shmop_open($myPid, 'c', 0644, strlen(json_encode($listArray)));
                        shmop_write($sharedId, json_encode($listArray), 0);
                        exit(0);
                    }
                }
            } else {
                throw new \Exception('Разница между диапазаоном first и end должна быть минимум 100.');
            }
            return $listArray;
        }
        throw new \Exception('Ошибка обработки массива!');
    }
	
}

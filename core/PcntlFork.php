<?php

namespace Core;

/**
 * Class PcntlFork
 * @package Сore
 */
class PcntlFork
{
    public function sendForkOfNumber($EndNumber)
    {
        $modelLuckyNumber = new LuckyNumber;
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
                    return $arrayRuntime;
                }
            } else {
                $myPid = getmypid();
                $listArray = [];
                for ($numberI = (($oneNumber * $i) - $oneNumber); $numberI <= $oneNumber * $i; $numberI++) {
                    if ($response = $modelLuckyNumber->getCheckOnIdentity($numberI))
                        $listArray[] = $response;
                }
                $sharedId = shmop_open($myPid, 'c', 0644, strlen(json_encode($listArray)));
                shmop_write($sharedId, json_encode($listArray), 0);
                exit(0);
            }
        }
    }

}

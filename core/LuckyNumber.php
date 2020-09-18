<?php

namespace Core;

//use ParserCore\interfaces\IParser;


/**
 * Class Parser
 * @package ParserCore
 */
class LuckyNumber //implements IParser
{


    private function getIntEndThree(int $number): int
    {
        $snumber = strval($number);
        $is = 3;
        $splitNumber = str_split($snumber);
        $splitNumber = $splitNumber < 3 ?: array_slice($splitNumber, count($splitNumber) - 3, count($splitNumber));
        $countSplit = count($splitNumber) > $is ? $is : count($splitNumber);
        $ic = count(array_slice($splitNumber, 0, $countSplit));
        $ic = $countSplit == 1 ? 1 : $ic;
        $v3 = 0;
        for ($i = 0; $i < $ic; $i++) {
            $v3 += $splitNumber[$i];
        }
        if ($v3 > 9) {
            return $this->getIntEndThree($v3);
        }
        return $v3;
    }

    private function getIntFirstThree(int $number): int
    {
        $snumber = strval($number);
        $splitNumber = str_split($snumber);
        $countSplit = count($splitNumber) > 3 ? 3 : count($splitNumber);
        $ic = count(array_slice(str_split($snumber), 0, $countSplit));
        $ic = $countSplit == 1 ? 1 : $ic;
        $v3 = 0;
        for ($i = 0; $i < $ic; $i++) {
            $v3 += $snumber[$i];
        }
        if ($v3 > 9) {
            return $this->getIntFirstThree($v3);
        }
        return $v3;
    }

    private function getCheckOnIdentity(int $number)
    {
        if ($this->getIntFirstThree($number) == $this->getIntEndThree($number)) {
            return $number;
        }
        return false;
    }

    /**
     * Initialize and get information
     * @param string $argv
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
                /*
               for ($numberI = $firstNumber; $numberI <= $EndNumber; $numberI++) {
                   if ($response = $this->getCheckOnIdentity($numberI))
                       $listArray[] = $response;
               }
               */
            } else {
                throw new \Exception('Разница между диапазаоном first и end должна быть минимум 100.');
            }
            return $listArray;
        }
        throw new \Exception('Ошибка обработки массива!');
    }

    /**
     * Getting link from the FormData header
     * @param string $csrf
     * @param string $argv
     * @return string
     */
    private function getUrlFormData(string $csrf, string $argv): string
    {
        $url = "_csrf={$csrf}&wv%5B0%5D={$argv}&wt%5B0%5D=PART&weOp%5B0%5D=AND&wv%5B1%5D=&wt%5B1%5D=PART&wrOp=AND&wv%5B2%5D=&wt%5B2%5D=PART&weOp%5B1%5D=AND&wv%5B3%5D=&wt%5B3%5D=PART&iv%5B0%5D=&it%5B0%5D=PART&ieOp%5B0%5D=AND&iv%5B1%5D=&it%5B1%5D=PART&irOp=AND&iv%5B2%5D=&it%5B2%5D=PART&ieOp%5B1%5D=AND&iv%5B3%5D=&it%5B3%5D=PART&wp=&_sw=on&classList=&ct=A&status=&dateType=LODGEMENT_DATE&fromDate=&toDate=&ia=&gsd=&endo=&nameField%5B0%5D=OWNER&name%5B0%5D=&attorney=&oAcn=&idList=&ir=&publicationFromDate=&publicationToDate=&i=&c=&originalSegment=";
        return $url;
    }

    /**
     * Getting the last page number from pagination
     * @param object $html
     * @return int
     */
    private function getNumberLastPageOfPagination(object $html): int
    {
        return $html->find("a.goto-last-page", 0)->getAttribute('data-gotopage') ?: 0;
    }

    /**
     * Getting a token for a subquery
     * @param object $html
     * @return string
     */
    private function getTokenOfResult($html): string
    {
        return $html->find("input[name=s]", 0)->value ?: strval(0);
    }

    /**
     * Getting Csrf token
     * @param string $out
     * @return string
     */
    private function getCsrf($out): string
    {
        $html = str_get_html($out);
        $csrf = strval(0);
        foreach ($html->find("#basicSearchForm input[name=_csrf]") as $element) {
            $csrf = $element->value;
        }
        return $csrf;
    }

    /**
     * Setting item in last iteration of fork in shmop
     * @param array $childPids
     * @return array
     */
    private function setItemLastAtShmop(array $childPids): array
    {
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

    /**
     * Getting a list of parsing information for each page
     * @param array $childPids
     * @return array
     */
    private function getListInfoParseOfPage(string $url): array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($curl);
        $html = str_get_html($out);
        $arrayRuntime = [];
        foreach ($html->find("#resultsTable tbody tr") as $element) {
            $arrayRuntime[] = [
                'number' => $element->children(2)->plaintext,
                'logo_url' => $element->find('img', 0) ? $element->find('img', 0)->getAttribute('src') : '',
                'name' => $element->find('.trademark.words', 0)->plaintext,
                'classes' => $element->find('.classes', 0)->plaintext,
                'status' => $element->find('.status div span', 0)->plaintext,
                'details_page_url' => DOMAIN . "/trademarks/search/view/" . trim($element->children(2)->plaintext),
            ];
        }
        curl_close($curl);
        return $arrayRuntime;
    }

    /**
     *  Getting list of information from data parsing
     * @param int $count
     * @param string $token_result
     * @return array
     */
    private function getListInfoOfParse(int $count, string $token_result): array
    {
        $childPids = [];
        for ($i = 0; $i <= $count; $i++) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                echo 'Fork -1';
                exit;
            } elseif ($pid) {
                $childPids[] = $pid;
                if ($i == $count) {
                    return $this->setItemLastAtShmop($childPids);
                }
            } else {
                $myPid = getmypid();
                $array = $this->getListInfoParseOfPage(DOMAIN . '/trademarks/search/result?s=' . $token_result . '&p=' . $i);
                $sharedId = shmop_open($myPid, 'c', 0644, strlen(json_encode($array)));
                shmop_write($sharedId, json_encode($array), 0);
                exit(0);
            }
        }
    }

}


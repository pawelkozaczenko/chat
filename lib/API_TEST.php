<?php
class API_TEST{
   
    private $notPassedLog = null;
    private $apiResponseArr = null;
    private $expectedApiResponseArr = null;
    private $api; 

    private function array_key_exists_regex_and_matches($needle, $needle_key, $haystack)
    {
        foreach ($haystack as $key => $value)
        {
            //if expected is not string so reges will not check i.e null value or perform pregmatch
            if ( (!is_string($value) && $needle === $value) || preg_match('/^'.$needle.'$/', $value))
            {
                if ($needle_key === $key)
                {
                    return true;
                }

            }
           
        }

        return false;
    }


    private function triggerApiResponse($ajaxRequest)
    {
        $this->api = new API_REQUEST($ajaxRequest);
        ob_start();
            //start buffer
            $this->api->invoke_api_method()->show_api_response();
            //
            $apiJsonResponse = ob_get_contents();
        ob_end_clean();

        $this->apiResponseArr = json_decode($apiJsonResponse, true);

        return $this;

    }

    private function expectedApiResponse($expectedApiJsonResponse)
    {
        $this->expectedApiResponseArr = json_decode($expectedApiJsonResponse, true);

        if (empty($this->expectedApiResponseArr))
        {
            $this->notPassedLog[] = 'Invalid json format of the injected argument: '.$expectedApiJsonResponse;
        }


        return $this;
    }

    //** find mismatch

    private function recordMismatch($reponseError)
    {
        if (empty($this->apiResponseArr))
        {
            $this->notPassedLog[] = 'Api did not responded for testing: '.$reponseError;
        }

        /*echo "\n\n**\n\n";
        var_dump($this->apiResponseArr);
        var_dump($this->expectedApiResponseArr);
        echo "\n\n**\n\n";*/
       

       
        foreach ($this->expectedApiResponseArr as $apiField => $apiValue)
        {
            if (!empty($apiField) && !array_key_exists($apiField, $this->apiResponseArr))
            {
                $this->notPassedLog[] = $reponseError.' (missing expected field array (i.e name) in response array) ';
            }
            else if ($this->apiResponseArr[$apiField] === '' && $this->apiResponseArr[$apiField] === null)
            {
                $this->notPassedLog[] = $reponseError.' (expected field array is empty while it is expected not to be)';

            }

            else if (!is_array($apiValue) && !is_array($this->apiResponseArr[$apiField]))
            {
                if ($this->apiResponseArr[$apiField] != $apiValue )
                {
                    $this->notPassedLog[] = $reponseError;
                }
               
            }
            else if  (is_array($apiValue) && is_array($this->apiResponseArr[$apiField]))
            {
   
                foreach ($apiValue as $arrKey => $arrVal)
                {
                    if (!in_array($arrVal, $this->apiResponseArr[$apiField]) && !is_array($arrVal))
                    {
                        $this->notPassedLog[] = $reponseError;
                    }
                    else if (is_array($arrVal))
                    {
                        $traverseSubArray = 0;
                        $numberOfSubArrays = count($this->apiResponseArr[$apiField]);
                        foreach ($this->apiResponseArr[$apiField] as $subKey => $subArray)
                        {
                            $traverseSubArray++;

                            if (!is_array($subArray))
                            {
                                $this->notPassedLog[] = $reponseError.' (should be array nested in outer array)';
                            }
                            else
                            {
                                foreach ($arrVal as $arrSubKey => $arrSubVal)
                                {
                                     
                                   
                                    if (!$this->array_key_exists_regex_and_matches($arrSubVal, $arrSubKey, $subArray) && $traverseSubArray === $numberOfSubArrays)
                                    {
                                        $this->notPassedLog[] = $reponseError;
                                    }
                                }

                            }
                        }
                    }
                }   
               
            }
        }
        //testPassed

        return $this;

    }

    public function testApiResponse($apiJsonRequest, $expectedApiJsonResponse, $reponseError)
    {

        return $this->triggerApiResponse($apiJsonRequest)->expectedApiResponse($expectedApiJsonResponse)->recordMismatch($reponseError);
    }

    public function showResult()
    {
        if (empty($this->notPassedLog))
        {
            echo 'API TEST PASSED SUCCESFULLY'."\n";
        }
        else
        {
            echo 'API TEST NOT PASSED: '.implode(' ,', $this->notPassedLog)."\n";
        }
    }
}
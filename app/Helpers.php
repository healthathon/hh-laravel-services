<?php

/**
 * This Model Class represents Blog Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App
 * @version  v.1.1
 */

namespace App;

use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;

/**
 * Class Helpers
 *
 * This helpers class contains an function which is use by majority of classes which holds an
 *  common functionality or behaviour.
 *
 * @package App
 */
class Helpers
{
    public $date;

    public function __construct()
    {
        $this->date = Carbon::now(\DateTimeZone::AMERICA);
    }

    /**
     * This function helps an application to send an json response to an application
     *
     * @param $statusCode : Represents status of Requests[200,404,500]
     * @param $statusMessage : Represents a short message of what happen
     * @param null $response : Its an response object, also called as data object which is by default null
     * @return \Illuminate\Http\JsonResponse: JsonResponse
     */
    public static function getResponse($statusCode, $statusMessage, $response = null)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'statusMessage' => $statusMessage,
            'response' => $response,
        ]);
    }

    public static function sendResponse(array $body)
    {
        return response()->json($body);
    }


    public static function CallAPI($method,$url,$data=[],$headers = []){


        $body = [];
        $body['form_params'] = $data;
        $body['headers'] = $headers;

        $client = new Client();
        $response = $client->request($method, $url,$body);

        return [
            'status'    =>  $response->getStatusCode(),
            'body'  =>  json_decode($response->getBody())
        ];
    }
}
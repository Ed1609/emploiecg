<?php

namespace App\Service;

use Twilio\Rest\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SmsService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function sendSms(string $number, string $name, string $text): bool // Correct method definition
    {

        $accountId = $_ENV['TWILIO_ACCOUNT_SID'];
        $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
        $fromNumber = $_ENV['TWILIO_PHONE_NUMBER'];

        $toNumber = $number;
        $message = 'Bonjour ' . $name . ', ' . $text . '';

        try {
            $client = new Client($accountId, $authToken);
            $client->messages->create(
                $toNumber,
                [
                    "from" => $fromNumber,
                    "body" => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
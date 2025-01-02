<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SmsService;

class SmsController extends AbstractController
{
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(string $recipientNumber, string $recipientName, string $message)
    {

        $success = $this->smsService->sendSms($recipientNumber, $recipientName, $message); // Correct method call

        return $this->$success;
    }
}
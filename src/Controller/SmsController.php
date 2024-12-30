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

    #[Route('/send-sms', name: 'send_sms')]
    public function sendSms(/*string $recipientNumber, string $recipientName, string $message*/): JsonResponse
    {
        $recipientNumber = '+242069226707';
        $recipientName = 'Ghost';
        $message = 'Bienvenu sur la plateforme de test';

        $success = $this->smsService->sendSms($recipientNumber, $recipientName, $message); // Correct method call

        if ($success) {
            return new JsonResponse(['message' => 'SMS sent successfully']);
        } else {
            return new JsonResponse(['error' => 'Failed to send SMS'], 500); // Internal Server Error
        }
    }
}
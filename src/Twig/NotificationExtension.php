<?php

namespace App\Twig;

use App\Service\NotificationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(private readonly NotificationService $notificationService) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_notifications', $this->notificationService->getNotifications(...)),
            new TwigFunction('app_notifications_count', $this->notificationService->countUnread(...)),
        ];
    }
}

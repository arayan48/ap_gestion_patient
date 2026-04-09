<?php

namespace App\Service;

use App\Repository\ReservationRepository;

class NotificationService
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
    ) {}

    /**
     * Returns a list of notification arrays, each with:
     *   type    : 'danger' | 'warning' | 'info'
     *   title   : string
     *   detail  : string
     *   unread  : bool
     */
    public function getNotifications(): array
    {
        $notifications = [];

        // ── 1. Séjours en retard (en_cours + dateFin dépassée) ────────────
        foreach ($this->reservationRepository->findOverdue() as $r) {
            $overdueSince = (new \DateTime())->diff($r->getDateFin());
            $delay = $overdueSince->days > 0
                ? $overdueSince->days . ' j de retard'
                : $overdueSince->h . ' h de retard';

            $notifications[] = [
                'type'   => 'danger',
                'title'  => 'Sortie en retard',
                'detail' => $r->getPatient()->getNom() . ' ' . $r->getPatient()->getPrenom()
                    . ' — Lit ' . $r->getLit()->getNumeroLit()
                    . ' (' . $delay . ')',
                'unread' => true,
                'url'    => '/reservations',
            ];
        }

        // ── 2. Sorties prévues aujourd'hui (toujours en_cours) ────────────
        foreach ($this->reservationRepository->findEndingToday() as $r) {
            $time = $r->getDateFin()->format('H:i');

            $notifications[] = [
                'type'   => 'warning',
                'title'  => 'Sortie prévue aujourd\'hui',
                'detail' => $r->getPatient()->getNom() . ' ' . $r->getPatient()->getPrenom()
                    . ' — Lit ' . $r->getLit()->getNumeroLit()
                    . ' à ' . $time,
                'unread' => true,
                'url'    => '/reservations',
            ];
        }

        // ── 3. Admissions du jour ─────────────────────────────────────────
        foreach ($this->reservationRepository->findAdmittedToday() as $r) {
            $time = $r->getDateDebut()->format('H:i');

            $notifications[] = [
                'type'   => 'info',
                'title'  => 'Admission aujourd\'hui',
                'detail' => $r->getPatient()->getNom() . ' ' . $r->getPatient()->getPrenom()
                    . ' — Lit ' . $r->getLit()->getNumeroLit()
                    . ' à ' . $time,
                'unread' => false,
                'url'    => '/reservations',
            ];
        }

        return $notifications;
    }

    public function countUnread(): int
    {
        return count(array_filter(
            $this->getNotifications(),
            fn($n) => $n['unread']
        ));
    }
}

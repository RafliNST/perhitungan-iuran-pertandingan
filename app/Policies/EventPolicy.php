<?php

namespace App\Policies;

use App\Enum\EventStatus;
use App\Models\Community;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    // Jika event dibuat di bawah komunitas, pastikan user adalah owner atau member komunitas tsb
    public function create(User $user, ?Community $community = null): bool
    {
        if (! $community) {
            return true;
        }

        return (int) $user->id === (int) $community->owner_id
            || $community->members()->where('user_id', $user->id)->exists();
    }

    // Hanya host yang boleh mengubah event (dan event belum dibatalkan)
    public function update(User $user, Event $event): bool
    {
        return (int) $user->id === (int) $event->host_id
            && $event->status !== EventStatus::CANCELLED;
    }

    // Delete hanya mengubah status -> hanya host yang boleh & belum cancelled
    public function delete(User $user, Event $event): bool
    {
        return (int) $user->id === (int) $event->host_id
            && $event->status !== EventStatus::CANCELLED;
    }

    // Aturan pendaftaran peserta (apply)
    public function apply(User $user, Event $event, ?int $targetUserId = null): bool
    {
        // 1. Tolak jika status event sudah Completed atau Cancelled
        if (! $event->isOpenForRegistration()) {
            return false;
        }

        $applicantId = $targetUserId ?? $user->id;

        // 2. Host TIDAK BOLEH mendaftar sebagai peserta di event-nya sendiri
        if ((int) $applicantId === (int) $event->host_id) {
            return false;
        }

        // 3. Jika host mendaftarkan user lain, pastikan pengirim request benar-benar host
        if ((int) $applicantId !== (int) $user->id && (int) $user->id !== (int) $event->host_id) {
            return false;
        }

        // 4. Jika event terikat komunitas, pastikan pendaftar adalah owner atau member komunitas tsb
        if ($event->community_id) {
            $community = $event->community;

            return (int) $applicantId === (int) $community->owner_id
                || $community->members()->where('user_id', $applicantId)->exists();
        }

        return true;
    }
}
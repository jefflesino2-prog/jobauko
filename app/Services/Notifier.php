<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Notice;
use App\Models\Opportunity;
use App\Models\User;

class Notifier
{
    public static function send(User $user, string $kind, string $text, bool $priority = false): void
    {
        Notice::create(['user_id' => $user->id, 'kind' => $kind, 'text' => $text, 'priority' => $priority]);
    }

    /** Every resident is told about a new opportunity; those whose interests match get a priority notice. */
    public static function newOpportunity(Opportunity $o): void
    {
        User::where('is_admin', false)->each(function (User $u) use ($o) {
            $hit = in_array($o->category, $u->interests ?? [], true);
            self::send(
                $u,
                'new',
                $hit ? "Matches your interests ({$u->identity}): {$o->title}." : "New opportunity: {$o->title}.",
                $hit
            );
        });
    }

    public static function status(Application $a): void
    {
        $a->loadMissing('user', 'opportunity');
        self::send($a->user, 'status', "Your application for {$a->opportunity->title} is now {$a->status}.");
    }
}

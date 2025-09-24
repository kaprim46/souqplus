<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Console\Command;

class BadeGiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badge:give';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give badges to users who have reached a certain number of points.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Badge give command executed.');

        /**
         * Give badges to users who have reached a certain number of points.
         * condition_type: (advertisement_count, membership_duration, role)
         * condition JSON: {"advertisement_count": 5}, {"membership_duration": 30}, {"role": "admin"|"business"|"customer"}
         */

        $badges = Badge::query()->get();

        foreach ($badges as $badge) {
            $condition = json_decode($badge->condition);

            switch ($badge->condition_type) {
                case 'advertisement_count':
                    $this->giveBadgeAd($condition->advertisement_count, $badge);
                    break;
                case 'membership_duration':
                    $this->giveBadgeMembership($condition->membership_duration, $badge);
                    break;
                case 'role':
                    $this->giveBadgeRile($condition->role, $badge);
                    break;
            }
        }
    }

    /**
     * Give badge to users who have reached a certain number of points.
     *
     * @param int $durationValue
     * @param Badge $badge
     */
    private function giveBadgeAd(int $durationValue, Badge $badge): void
    {
        // Give badge to users who have reached a certain number of points.
        $this->info("Give badge to users who have created $durationValue advertisements.");

        $users = User::query()
            ->whereHas('advertisements', function ($query) use ($durationValue) {
                $query->select('user_id')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) >= ?', [$durationValue]);
            })
            ->doesntHave('badges', 'and', function ($query) use ($badge) {
                $query->where('badge_id', $badge->id);
            })
            ->get();


        UserBadge::query()->insert($users->map(function ($user) use ($badge) {
            return [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ];
        })->toArray());

        $this->info("Badge given to users {$users->count()}");
    }

    /**
     * Give badge to users who have been a member for durationValue days.
     *
     * @param int $durationValue
     * @param Badge $badge
     */
    private function giveBadgeMembership(int $durationValue, Badge $badge): void
    {
        // Give badge to users who have been a member for durationValue days.
        $this->info("Give badge to users who have been a member for $durationValue days.");

        $users = User::query()
            ->where('created_at', '<=', now()->subDays($durationValue))
            ->doesntHave('badges', 'and', function ($query) use ($badge) {
                $query->where('badge_id', $badge->id);
            })
            ->get();

        UserBadge::query()->insert($users->map(function ($user) use ($badge) {
            return [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ];
        })->toArray());

        $this->info("Badge given to users {$users->count()}");
    }

    /**
     * Give badge to users who have the role of roleValue.
     *
     * @param string $roleValue
     * @param Badge $badge
     */
    private function giveBadgeRile(string $roleValue, Badge $badge): void
    {
        // Give badge to users who have the role of roleValue.
        $this->info("Give badge to users who have the role of $roleValue.");

        $users = User::query()
            ->where('role', $roleValue)
            ->doesntHave('badges', 'and', function ($query) use ($badge) {
                $query->where('badge_id', $badge->id);
            })
            ->get();

        UserBadge::query()->insert($users->map(function ($user) use ($badge) {
            return [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ];
        })->toArray());

        $this->info("Badge given to users {$users->count()}");
    }
}

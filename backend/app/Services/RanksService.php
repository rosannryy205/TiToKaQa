<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class RanksService
{
    public function updateUserRankByPoints(User $user)
    {
        $points = $user->rank_points;
        $rank = 'new';

        if ($points >= 3000) {
            $rank = 'diamond';
        } elseif ($points >= 2000) {
            $rank = 'gold';
        } elseif ($points >= 500) {
            $rank = 'silver';
        }

        if ($user->rank !== $rank) {
            $user->update(['rank' => $rank]);
            $this->giveRankVoucher($user, $rank);
        }
    }

    public function giveRankVoucher(User $user, string $rank)
    {
        $discount = Discount::where('source', 'for_users')
            ->where('user_level', $rank)
            ->where('status', 'active')
            ->first();

        if ($discount && $user->discounts()->where('discount_id', $discount->id)->doesntExist()) {
            $now = now();
            $user->discounts()->attach($discount->id, [
                'point_used' => 0,
                'exchanged_at' => $now,
                'expiry_at' => $now->addDays(7),
                'used_at' => null,
                'source' => 'rank_reward',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

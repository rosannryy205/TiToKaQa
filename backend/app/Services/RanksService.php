<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class RanksService
{
    public function updateUserRankByPoints(User $user)
{
    $points = $user->rank_points;

    if ($points >= 3000) {
        $rank = 'Kim cương';
    } elseif ($points >= 2000) {
        $rank = 'Vàng';
    } elseif ($points >= 500) {
        $rank = 'Bạc';
    } else {
        $rank = 'Chưa có hạng';
    }
    if ($user->rank !== $rank) {
        $user->update(['rank' => $rank]);
    }
}
}

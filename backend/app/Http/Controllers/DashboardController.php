<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Reservation_table;
use App\Models\Order_detail;
use App\Models\Food;
use Illuminate\Http\Request;

class DashboardController extends Controller
{


    public function revenueByMonth()
    {
        $payments = Payment::selectRaw('MONTH(payment_time) as month, SUM(amount_paid) as total')
            ->where('payment_status', 'Đã thanh toán')
            ->whereYear('payment_time', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];

        foreach (range(1, 12) as $thang) {
            $labels[] = 'THG ' . $thang;
            $values[] = $payments[$thang] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $values
        ]);
    }

    public function getDashboardStats()
    {
        $todayOrdersCount = Order::whereDate('order_time', today())
            ->whereIn('order_status', ['Hoàn thành'])
            ->count();

        $todayRevenue = Payment::where('payment_status', 'Đã thanh toán')
            ->whereDate('payment_time', today())
            ->sum('amount_paid');

        $todayReservations = Reservation_table::whereDate('reserved_from', today())
            ->count();

        $bestSellingDish = Order_detail::select('foods.name', Order_detail::raw('SUM(order_details.quantity) as total_quantity'))
            ->join('foods', 'foods.id', '=', 'order_details.food_id')
            ->groupBy('order_details.food_id', 'foods.name')
            ->orderByDesc('total_quantity')
            ->first();

        $dishName = $bestSellingDish->name ?? 'Chưa có món bán';


        return response()->json([
            'orders_today' => $todayOrdersCount,
            'revenue_today' => $todayRevenue,
            'reservations_today' => $todayReservations,
            'best_selling_dish' => $dishName,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

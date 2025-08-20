<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Reservation_table;
use App\Models\Order_detail;
use App\Models\User;
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

        $todayReservations = Reservation_table::join('orders', 'reservation_tables.order_id', '=', 'orders.id')
            ->whereDate('reservation_tables.reserved_from', today())
            ->where('orders.order_status', '!=', 'Đã hủy')
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

    // Thống kê người dùng //
    public function getTotalUser()
    {
        $totalUsers = User::count();
        return response()->json([
            'total' => $totalUsers
        ]);
    }

    public function statsUserByTime(Request $request)
    {
        $filter = $request->query('filter', 'month');

        $labels = [];
        $datas = [];

        if ($filter === 'today') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();

            $users = User::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereDate('created_at', [$start, $end])
                ->groupBy('hour')
                ->get()
                ->keyBy('hour');

            for ($h = $start->copy(); $h <= $end; $h->addHour()) {
                $labels[] = $h->format('H:i');
                $datas[] = $users[$h->hour]->count ?? 0;
            }
        } elseif ($filter === 'this_week') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();

            $users = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $labels[] = $d->format('d/m');
                $datas[] = $users[$d->toDateString()]->count ?? 0;
            }
        } elseif ($filter === 'month') {
            $users = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            foreach (range(1, 12) as $thang) {
                $labels[] = 'THG ' . $thang;
                $datas[] = $users[$thang]->count ?? 0;
            }
        } elseif ($filter === 'year') {
            $start = now()->subYears(6)->startOfYear();
            $end = now()->endOfYear();
            $users = User::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('year')
                ->get()
                ->keyBy('year');

            for ($h = $start->copy(); $h <= $end; $h->addYear()) {
                $labels[] = $h->format('Y');
                $datas[] = $users[$h->year]->count ?? 0;
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $datas
        ]);
    }

    // Thống kê đặt bàn //
    public function getTotalRes()
    {
        $totalRes = Order::whereNotNull('reservation_code')
            ->where('order_status', 'Hoàn thành')
            ->count();
        return response()->json([
            'total' => $totalRes
        ]);
    }

    public function statsResByTime(Request $request)
    {
        $filter = $request->query('filter', 'month');

        $labels = [];
        $datas = [];

        if ($filter === 'today') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();

            $res = Reservation_table::selectRaw('HOUR(reserved_from) as hour, COUNT(*) as count')
                ->join('orders', 'orders.id', '=', 'reservation_tables.order_id')
                ->where('orders.order_status', 'Hoàn thành')
                ->whereBetween('reserved_from', [$start, $end])
                ->groupBy('hour')
                ->get()
                ->keyBy('hour');

            for ($h = $start->copy(); $h <= $end; $h->addHour()) {
                $labels[] = $h->format('H:i');
                $datas[] = $res[$h->hour]->count ?? 0;
            }
        } elseif ($filter === 'this_week') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();

            $res = Reservation_table::selectRaw('DATE(reserved_from) as date, COUNT(*) as count')
                ->join('orders', 'orders.id', '=', 'reservation_tables.order_id')
                ->where('orders.order_status', 'Hoàn thành')
                ->whereBetween('reserved_from', [$start, $end])
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $labels[] = $d->format('d/m');
                $datas[] = $res[$d->toDateString()]->count ?? 0;
            }
        } elseif ($filter === 'month') {
            $res = Reservation_table::selectRaw('MONTH(reserved_from) as month, COUNT(*) as count')
                ->join('orders', 'orders.id', '=', 'reservation_tables.order_id')
                ->where('orders.order_status', 'Hoàn thành')
                ->whereYear('reserved_from', date('Y'))
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            foreach (range(1, 12) as $thang) {
                $labels[] = 'THG ' . $thang;
                $datas[] = $res[$thang]->count ?? 0;
            }
        } elseif ($filter === 'year') {
            $start = now()->subYears(6)->startOfYear();
            $end = now()->endOfYear();
            $res = Reservation_table::selectRaw('YEAR(reserved_from) as year, COUNT(*) as count')
                ->join('orders', 'orders.id', '=', 'reservation_tables.order_id')
                ->where('orders.order_status', 'Hoàn thành')
                ->whereBetween('reserved_from', [$start, $end])
                ->groupBy('year')
                ->get()
                ->keyBy('year');

            for ($h = $start->copy(); $h <= $end; $h->addYear()) {
                $labels[] = $h->format('Y');
                $datas[] = $res[$h->year]->count ?? 0;
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $datas
        ]);
    }

    // Thống kê đơn hàng //
    public function getTotalOrder()
    {
        $totalRes = Order::whereNull('reservation_code')
            ->where('order_status', 'Hoàn thành')
            ->count();
        return response()->json([
            'total' => $totalRes
        ]);
    }

    public function statsOrderByTime(Request $request)
    {
        $filter = $request->query('filter', 'month');

        $labels = [];
        $datas = [];

        if ($filter === 'today') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();

            $res = Order::selectRaw('HOUR(order_time) as hour, COUNT(*) as count')
                ->where('order_status', 'Hoàn thành')
                ->whereNull('reservation_code')
                ->whereBetween('order_time', [$start, $end])
                ->groupBy('hour')
                ->get()
                ->keyBy('hour');

            for ($h = $start->copy(); $h <= $end; $h->addHour()) {
                $labels[] = $h->format('H:i');
                $datas[] = $res[$h->hour]->count ?? 0;
            }
        } elseif ($filter === 'this_week') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();

            $res = Order::selectRaw('DATE(order_time) as date, COUNT(*) as count')
                ->where('order_status', 'Hoàn thành')
                ->whereNull('reservation_code')
                ->whereBetween('order_time', [$start, $end])
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $labels[] = $d->format('d/m');
                $datas[] = $res[$d->toDateString()]->count ?? 0;
            }
        } elseif ($filter === 'month') {
            $res = Order::selectRaw('MONTH(order_time) as month, COUNT(*) as count')
                ->where('order_status', 'Hoàn thành')
                ->whereNull('reservation_code')
                ->whereYear('order_time', date('Y'))
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            foreach (range(1, 12) as $thang) {
                $labels[] = 'THG ' . $thang;
                $datas[] = $res[$thang]->count ?? 0;
            }
        } elseif ($filter === 'year') {
            $start = now()->subYears(6)->startOfYear();
            $end = now()->endOfYear();
            $res = Order::selectRaw('YEAR(order_time) as year, COUNT(*) as count')
                ->where('order_status', 'Hoàn thành')
                ->whereNull('reservation_code')
                ->whereBetween('order_time', [$start, $end])
                ->groupBy('year')
                ->get()
                ->keyBy('year');

            for ($h = $start->copy(); $h <= $end; $h->addYear()) {
                $labels[] = $h->format('Y');
                $datas[] = $res[$h->year]->count ?? 0;
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $datas
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

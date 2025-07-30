<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\Request;

class TableController extends Controller
{
    //lấy tất cả bàn
    public function getAllTable()
    {
        $table = Table::orderBy('table_number', 'asc')->get();
        return $table;
    }

    //thêm bàn
    public function insertTable(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'table_number' => 'required|integer|min:1|unique:tables,table_number',
                'capacity' => 'required|numeric'
            ], [
                'table_number.required' => 'Số bàn không được để trống.',
                'table_number.numeric' => 'Số bàn phải là số.',
                'table_number.unique' => 'Số bàn đã tồn tại. Vui lòng chọn số khác.',
                'table_number.min' => 'Số bàn phải là số lớn hơn 0.',
                'capacity.required' => 'Sức chứa không được để trống.',
                'capacity.numeric' => 'Sức chứa phải là số.',
            ]);

            $table = new Table();
            $table->table_number = $validatedData['table_number'];
            $table->capacity = $validatedData['capacity'];
            $table->save();

            return response()->json([
                'status' => true,
                'message' => 'Thêm bàn thành công!',
                'table' => $table
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi không mong muốn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //lấy bàn theo id
    public function getTableById(Request $request)
    {
        $table = Table::find($request->id);
        return response()->json([
            'status' => true,
            'mess' => 'Lấy bàn thành công',
            'table' => $table
        ], 200);
    }

    //sửa bàn
    public function updateTable(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|min:1|unique:tables,table_number,' . $request->id,
            'capacity' => 'required|integer|min:1',
        ], [
            'id.exists' => 'Bàn không tồn tại.',
            'table_number.required' => 'Số bàn không được bỏ trống.',
            'table_number.integer' => 'Số bàn phải là số nguyên.',
            'table_number.min' => 'Số bàn phải lớn hơn 0.',
            'table_number.unique' => 'Số bàn đã tồn tại.',
            'capacity.required' => 'Sức chứa không được bỏ trống.',
            'capacity.integer' => 'Sức chứa phải là số nguyên.',
            'capacity.min' => 'Sức chứa phải lớn hơn 0.',
        ]);

        $table = Table::find($request->id);
        $table->table_number = $request->table_number;
        $table->capacity = $request->capacity;
        $table->save();

        return response()->json([
            'status' => true,
            'mess' => 'Sửa bàn thành công',
            'table' => $table
        ]);
    }


    //xoá bàn
    public function deleteTable(Request $request)
    {
        $table = Table::find($request->id);
        $table->delete();
        return response()->json(['message' => 'Xoá bàn thành công']);
    }

    //restore
    // public function restoreTable(Request $request)
    // {
    //     $table = Table::withTrashed();
    //     $table->find($request->id);
    //     $table->restore();
    //     return response()->json(['message' => 'Khôi phục bàn thành công']);
    // }


    //lấy danh sách bàn
    public function getTables(Request $request)
    {
        // nếu không có date thì mặc định là ngày hiện tại
        $date = $request->input('date', Carbon::today()->toDateString());
        $filterStatus = $request->input('status');

        $tables = Table::with([
            'reservations' => function ($query) use ($date) {
                $query->whereDate('reserved_from', $date)
                    ->with('order:id,order_status');
            }
        ])
            ->orderBy('table_number')
            ->get();

        $inUseStatuses = ['Khách đã đến'];
        $reservedStatuses = ['Đã xác nhận', 'Đang xử lý'];
        $processedTables = [];

        foreach ($tables as $table) {
            $hasReservationHistory = $table->reservations()->exists();
            $table->current_day_status = 'Bàn trống';
            $table->has_booking_history = false;

            $isInUse = false;
            $isReserved = false;

            foreach ($table->reservations as $reservation) {
                $table->has_booking_history = true;

                $order = $reservation->order;
                if (!$order) continue;

                if (in_array($order->order_status, $inUseStatuses)) {
                    $isInUse = true;
                    break;
                }

                if (in_array($order->order_status, $reservedStatuses)) {
                    $isReserved = true;
                }
            }

            if ($isInUse) {
                $table->current_day_status = 'Đang phục vụ';
            } elseif ($isReserved) {
                $table->current_day_status = 'Đã đặt trước';
            }


            if ($filterStatus && $table->current_day_status !== $filterStatus) {
                continue;
            }

            $processedTables[] = $table;
        }

        return response()->json([
            'tables' => $processedTables,
            'hasReservationHistory' => $tables->contains(function ($table) {
                return $table->reservations()->exists();
            }),
            'date' => $date,
        ], 200);
    }

    //lấy tất cả order theo id bàn
    public function getAllOrdersByIdTable(Request $request)
    {
        $table = Table::with([
            'orders' => function ($query) {
                $query->whereDate('order_time', Carbon::today());
            },
            'orders.tables'
        ])->find($request->id);
        $orders = $table->orders;
        $allReservations = [];
        foreach ($orders as $order) {
            if ($order->tables->isNotEmpty()) {
                $pivot = $order->tables->first()->pivot;
                $allReservations[] = [
                    'order_id' => $order->id,
                    'reserved_from' => $pivot->reserved_from,
                    'order_status' => $order->order_status,
                ];
            }
        }
        return response()->json([
            'message' => 'Đơn hàng của ngày hôm nay cho bàn ID ' . $request->id . ' đã được lấy thành công.',
            'data' => [
                'reservations' => $allReservations,
            ]
        ], 200);
    }
}

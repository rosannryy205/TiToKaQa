<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
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
        try {
            // VNPAY credentials
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = $request->input('return_url', 'http://localhost:5173/payment-result');
            $vnp_TmnCode = "NJJ0R8FS"; // Merchant code at VNPAY
            $vnp_HashSecret = "BYKJBHPPZKQMKBIBGGXIYKWYFAYSJXCW"; // Secret key

            // Transaction information
            $vnp_TxnRef = $request->input('order_id'); // Transaction reference (unique per order)
            $vnp_OrderInfo = 'Thanh toán đơn hàng test'; // Order information
            $vnp_OrderType = 'other';
            $vnp_Amount = $request->input('amount') * 100; // Amount in VND (VNPAY expects amount in cents)
            $vnp_Locale = 'vn'; // Locale

            $vnp_IpAddr = $request->ip(); // Use Laravel's request to get IP

            // Prepare input data
            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            ];

            // Optional fields
            $vnp_BankCode = $request->input('bank_code');
            if (!empty($vnp_BankCode)) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            } else {
                // Bỏ qua mã ngân hàng và để VNPAY tự động chọn
                unset($inputData['vnp_BankCode']);
            }


            // Sort parameters by key
            ksort($inputData);

            // Build the query string and hashdata for signature
            $queryString = "";
            $hashdata = "";
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $queryString .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            // Remove trailing '&' from the query string
            $queryString = rtrim($queryString, '&');

            // Now calculate the secure hash using the secret key
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

            // Append the secure hash to the query string
            $vnp_Url .= "?" . $queryString . "&vnp_SecureHash=" . $vnpSecureHash;

            $paymentTime = Carbon::now('Asia/Ho_Chi_Minh');
            Payment::create([
                'order_id' => $vnp_TxnRef,
                'amount_paid' => $request->input('amount'),
                'payment_method' => 'Thanh toán VNPAY',
                'payment_time'=> $paymentTime ,
                'payment_status' => 'Đã thanh toán',
                'payment_type' => $request->input('payment_type', 'Thanh toán toàn bộ'),
            ]);

            // Return the payment URL or redirect
            return response()->json(['payment_url' => $vnp_Url]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi tạo link thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function vnpayReturn(Request $request)
    {
        Log::info('🔥 DỮ LIỆU NHẬN VỀ', $request->all()); // dòng này có rồi
        try {
            $validated = $request->validate([
                'amount_paid' => 'required|numeric',
                'payment_method' => 'required|in:Thanh toán COD',
                'payment_status' => 'required|in:Chưa thanh toán',
                'payment_type' => 'required|in:Thanh toán toàn bộ',
                'order_id' => 'required|exists:orders,id',
            ]);
            $validated['payment_time'] = Carbon::now('Asia/Ho_Chi_Minh');
            Payment::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'Đã lưu thông tin thanh toán'
            ]);
        } catch (\Throwable $e) {
            Log::error('🔥 Lỗi khi xử lý vnpayReturn: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xử lý dữ liệu thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
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

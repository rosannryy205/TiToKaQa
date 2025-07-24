<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Hiển thị danh sách các tài nguyên.
     */
    public function index()
    {
        // Triển khai theo nhu cầu.
    }

    /**
     * Hiển thị biểu mẫu để tạo tài nguyên mới.
     */
    public function create()
    {
        // Triển khai theo nhu cầu.
    }

    /**
     * Lưu một tài nguyên mới được tạo vào bộ nhớ.
     * Phương thức này xử lý việc khởi tạo một thanh toán VNPAY.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric|min:1000',
                'bank_code' => 'nullable|string',
                'return_url' => 'nullable|url',
            ]);

            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
            }

            $vnp_TxnRef = $order->id . '_' . Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis') . '_' . uniqid();

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => config('services.vnpay.tmn_code'),
                "vnp_Amount" => $validated['amount'] * 100,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $request->ip(),
                "vnp_Locale" => 'vn',
                "vnp_OrderInfo" => 'Thanh toán cho đơn hàng #' . $order->id,
                "vnp_OrderType" => 'billpayment',
                "vnp_ReturnUrl" => $validated['return_url'] ?? config('app.frontend_url') . '/payment-result',
                "vnp_TxnRef" => $vnp_TxnRef,
            ];

            if (!empty($validated['bank_code'])) {
                $inputData['vnp_BankCode'] = $validated['bank_code'];
            }

            ksort($inputData);

            $queryString = http_build_query($inputData);
            $hashdata = implode('&', array_map(function ($key, $value) {
                return urlencode($key) . '=' . urlencode($value);
            }, array_keys($inputData), $inputData));

            $vnpSecureHash = hash_hmac('sha512', $hashdata, config('services.vnpay.hash_secret'));
            $vnp_Url = config('services.vnpay.url') . "?" . $queryString . "&vnp_SecureHash=" . $vnpSecureHash;

            Payment::create([
                'order_id' => $order->id,
                'vnpay_txn_ref' => $vnp_TxnRef,
                'amount_paid' => $validated['amount'],
                'payment_method' => 'VNPAY',
                'payment_time' => null,
                'payment_status' => 'Đang chờ xử lý',
            ]);

            return response()->json(['payment_url' => $vnp_Url]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi không mong muốn khi tạo liên kết thanh toán.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function vnpayReturn(Request $request)
    {

        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->except('vnp_SecureHash', 'vnp_SecureHashType');
        ksort($inputData);

        $hashdata = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $responseCode = "99";
        $message = "Lỗi không xác định";

        DB::beginTransaction();
        try {
            $payment = Payment::where('vnpay_txn_ref', $request->input('vnp_TxnRef'))->first();

            if (!$payment) {
                $responseCode = "01";
                $message = "Không tìm thấy bản ghi thanh toán";
            } elseif ($secureHash !== $request->input('vnp_SecureHash')) {
                $responseCode = "97";
                $message = "Chữ ký không hợp lệ";
            } elseif ($payment->amount_paid != ($request->input('vnp_Amount') / 100)) {
                $responseCode = "04";
                $message = "Số tiền không hợp lệ";
            } elseif ($payment->payment_status == 'Đã thanh toán') {
                $responseCode = "00";
                $message = "Thanh toán đã được xử lý thành công";
            } else {
                if ($request->input('vnp_ResponseCode') == '00' && $request->input('vnp_TransactionStatus') == '00') {
                    $payment->update([
                        'payment_status' => 'Đã thanh toán',
                        'payment_time' => Carbon::createFromFormat('YmdHis', $request->input('vnp_PayDate'), 'Asia/Ho_Chi_Minh'),
                        'transaction_id' => $request->input('vnp_TransactionNo'),
                        'bank_code' => $request->input('vnp_BankCode'),
                        'card_type' => $request->input('vnp_CardType'),
                    ]);

                    $order = Order::find($payment->order_id);
                    if ($order && $order->status == 'Đang chờ xử lý') {
                        $order->update(['status' => 'Đã thanh toán']);
                    }
                    $responseCode = "00";
                    $message = "Thanh toán thành công";
                } else {
                    $payment->update([
                        'payment_status' => 'Thất bại',
                        'payment_time' => Carbon::createFromFormat('YmdHis', $request->input('vnp_PayDate'), 'Asia/Ho_Chi_Minh'),
                        'transaction_id' => $request->input('vnp_TransactionNo'),
                        'bank_code' => $request->input('vnp_BankCode'),
                        'card_type' => $request->input('vnp_CardType'),
                    ]);

                    $order = Order::find($payment->order_id);
                    if ($order && $order->status == 'Đang chờ xử lý') {
                        $order->update(['status' => 'Thanh toán thất bại']);
                    }
                    $responseCode = "02";
                    $message = "Thanh toán thất bại hoặc bị hủy: Mã phản hồi VNPAY: " . $request->input('vnp_ResponseCode') . ", Trạng thái giao dịch: " . $request->input('vnp_TransactionStatus');
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $responseCode = "99";
            $message = "Đã xảy ra lỗi hệ thống trong quá trình xử lý thanh toán";
        }

        return response()->json([
            'RspCode' => $responseCode,
            'Message' => $message,
            'success' => true,
            'order_id' => $payment->order_id ?? null,]);
    }

    public function handleCodPayment(Request $request)
    {
        Log::info('🔥 DỮ LIỆU THANH TOÁN COD ĐÃ NHẬN', $request->all());
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount_paid' => 'required|numeric',
            ]);

            DB::beginTransaction();

            Payment::create([
                'order_id' => $validated['order_id'],
                'amount_paid' => $validated['amount_paid'],
                'payment_method' => 'COD',
                'payment_status' => 'Đang chờ xử lý',
                'payment_time' => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);

            $order = Order::find($validated['order_id']);
            if ($order && $order->status === 'Đang chờ xử lý') {
                $order->update(['status' => 'Đã thanh toán']);
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Đã lưu thông tin thanh toán COD']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Lỗi khi xử lý dữ liệu thanh toán COD', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Hiển thị tài nguyên được chỉ định.
     */
    public function show(string $id)
    {
        // Triển khai theo nhu cầu.
    }

    /**
     * Hiển thị biểu mẫu để chỉnh sửa tài nguyên được chỉ định.
     */
    public function edit(string $id)
    {
        // Triển khai theo nhu cầu.
    }

    /**
     * Cập nhật tài nguyên được chỉ định trong bộ nhớ.
     */
    public function update(Request $request, string $id)
    {
        // Triển khai theo nhu cầu.
    }

    /**
     * Xóa tài nguyên được chỉ định khỏi bộ nhớ.
     */
    public function destroy(string $id)
    {
        // Triển khai theo nhu cầu.
    }
}

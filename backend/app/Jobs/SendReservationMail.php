<?php

namespace App\Jobs;

use App\Mail\ReservationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class SendReservationMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::with('tables')->find($this->orderId);
        if (!$order) {
            Log::error("SendReservationMailJob: Không tìm thấy đơn hàng ID {$this->orderId}");
            return;
        }

        $tableInfos = $order->tables->map(function ($table) {
            return [
                'table_number'  => $table->table_number ?? 'Không rõ',
                'reserved_from' => $table->pivot->reserved_from,
                'reserved_to'   => $table->pivot->reserved_to,
            ];
        })->toArray();

        $qrImage = QrCode::format('png')->size(250)->generate('http://localhost:5173/history-order-detail/' . $order->id);

        $filename = 'qr_' . $order->id . '.png';
        $tempPath = storage_path('app/public/' . $filename);
        file_put_contents($tempPath, $qrImage);

        $uploadedFileUrl = Cloudinary::upload($tempPath, [
            'folder' => 'qr_codes'
        ])->getSecurePath();

        unlink($tempPath);

        $mailData = [
            'order_id' => $order->id,
            'reservation_code' => $order->reservation_code,
            'guest_name' => $order->guest_name,
            'guest_email' => $order->guest_email,
            'guest_phone' => $order->guest_phone,
            'guest_count' => $order->guest_count,
            'total_price' => $order->total_price ?? null,
            'note' => $order->note ?? null,
            'order_details' => null,
            'tables' => $tableInfos,
            'subtotal' => null,
            'order_status' =>  'Đã xác nhận',
            'qr_url' => $uploadedFileUrl
        ];

        if (empty($mailData['guest_email'])) {
        Log::error("SendReservationMailJob: guest_email trống");
        return;
    }
        // Gửi mail
        Mail::to($order->guest_email)->send(new ReservationMail($mailData));

        Log::info("SendReservationMailJob: Đã gửi mail xác nhận đơn hàng ID {$order->id}");
    }
}

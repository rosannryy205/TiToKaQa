<?php

namespace App\Jobs;

use App\Mail\ReservationMail;
use App\Models\Combo;
use App\Models\Food;
use App\Models\Food_topping;
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
    protected $orderDetails;

    public function __construct($orderId, $orderDetails)
    {
        $this->orderId = $orderId;
        $this->orderDetails = $orderDetails;
    }

    public function handle()
    {
        $order = Order::with('tables')->find($this->orderId);
        if (!$order) {
            Log::error("SendReservationMailJob: Không tìm thấy đơn hàng ID {$this->orderId}");
            return;
        }

        $orderDetailsWithNames = [];
        $subtotal = 0;

        if (!empty($this->orderDetails)) {
            foreach ($this->orderDetails as $item) {
                $name = null;
                if ($item['type'] === 'food' && !empty($item['food_id'])) {
                    $food = Food::find($item['food_id']);
                    $name = $food?->name ?? 'Món ăn không tồn tại';
                    $image = $food?->image;
                }
                if ($item['type'] === 'combo' && !empty($item['combo_id'])) {
                    $combo = Combo::find($item['combo_id']);
                    $name = $combo?->name ?? 'Món ăn không tồn tại';
                    $image = $combo?->image;
                }

                $toppingsWithNames = [];
                if (!empty($item['toppings'])) {
                    foreach ($item['toppings'] as $topping) {
                        $foodToppingModel = Food_topping::find($topping['food_toppings_id']);
                        $toppingModel = $foodToppingModel?->toppings;

                        $toppingsWithNames[] = [
                            'name' => $toppingModel?->name ?? 'Topping không tồn tại',
                            'price' => $topping['price']
                        ];
                    }
                }

                $orderDetailsWithNames[] = [
                    'name' => $name,
                    'image' => $image,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'type' => $item['type'],
                    'toppings' => $toppingsWithNames,
                ];
            }
        }
        $subtotal = 0;

        foreach ($orderDetailsWithNames as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $itemSubtotal += $topping['price'] * $item['quantity'];
                }
            }

            $subtotal += $itemSubtotal;
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
            'guest_count' => $order->guest_phone,
            'total_price' => $order->total_price ?? null,
            'note' => $order->note ?? null,
            'order_details' => $orderDetailsWithNames,
            'tables' => $tableInfos,
            'subtotal' => $subtotal,
            'order_status' =>  $order->order_status,
            'qr_url' => $uploadedFileUrl
        ];


        Mail::to($mailData['guest_email'])->send(new ReservationMail($mailData));
    }
}

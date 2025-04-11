<div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; border: 1px solid #ddd; background-color: #fff; padding: 24px; color: #333;">
    <h2 style="color: #e53935; text-align: center; margin-bottom: 20px;">🍜 XÁC NHẬN ĐƠN HÀNG & ĐẶT BÀN</h2>

    <p style="font-size: 15px;">Xin chào <strong>{{ $mailData['guest_name'] }}</strong>,</p>
    <p style="font-size: 15px;">
        Cảm ơn bạn đã đặt món và đặt bàn tại <strong>TikTokaQa</strong>! Dưới đây là chi tiết đơn hàng và thông tin đặt bàn của bạn:
    </p>
    <h3 style="color: #e53935; margin-top: 30px; font-size: 18px;">🧾 Chi tiết đơn hàng</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px;">
        <thead>
            <tr style="background-color: #e53935; color: white;">
                <th style="padding: 10px; text-align: left;">Món ăn</th>
                <th style="padding: 10px; text-align: center;">Cấp độ cay</th>
                <th style="padding: 10px; text-align: center;">SL</th>
                <th style="padding: 10px; text-align: right;">Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['order_details'] as $item)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;">
                    {{ $item['name'] }}
                    @if (!empty($item['toppings']))
                        <br>
                        <small style="color: #888;">Topping: {{ collect($item['toppings'])->pluck('name')->implode(', ') }}</small>
                    @endif
                </td>
                <td style="padding: 10px; text-align: center;">{{ $item['spicy_level'] ?? 'Không' }}</td>
                <td style="padding: 10px; text-align: center;">{{ $item['quantity'] }}</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($item['price']) }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: right; font-size: 16px; margin-top: 10px;">
        <strong>Tổng cộng:</strong>
        <span style="color: #e53935;">{{ number_format($mailData['total_price']) }} VND</span>
    </div>

    <h3 style="color: #e53935; margin-top: 30px; font-size: 18px;">📌 Thông tin đặt bàn</h3>
    <table style="width: 100%; font-size: 14px; margin-bottom: 10px;">
    <tr>
        <td style="padding: 6px 0;"><strong>Tên khách:</strong></td>
        <td style="text-align: right;">{{ $mailData['guest_name'] }}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0;"><strong>Số điện thoại:</strong></td>
        <td style="text-align: right;">{{ $mailData['guest_phone'] }}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0;"><strong>Ngày đặt bàn:</strong></td>
        <td style="text-align: right;">{{ $mailData['reservations_time'] }}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0;"><strong>Giờ đặt:</strong></td>
        <td style="text-align: right;">{{ $mailData['reservations_time'] }}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0;"><strong>Số lượng người:</strong></td>
        <td style="text-align: right;">{{ $mailData['guest_count'] }}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0;"><strong>Ghi chú thêm:</strong></td>
        <td style="text-align: right;">{{ $mailData['note'] ?? 'Không có' }}</td>
    </tr>
</table>

    <div style="margin-top: 40px; text-align: center;">
        <p style="font-size: 15px;">❤️ Cảm ơn bạn đã tin tưởng <strong>TikTokaQa</strong>. Chúc bạn có trải nghiệm tuyệt vời!</p>
        <img src="{{ asset('assets/images/logonew.png') }}" alt="TikTokaQa Logo" style="height: 50px; margin-top: 10px;" />

</div>


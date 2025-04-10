<div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; background-color: #fff8f0;">
    <h2 style="color: #e53935;">🍜 XÁC NHẬN ĐƠN HÀNG & ĐẶT BÀN</h2>

    <p>Xin chào <strong>{{ $mailData['guest_name'] }}</strong>,</p>
    <p>Cảm ơn bạn đã đặt món và đặt bàn tại <strong>TikTokaQa</strong>! Dưới đây là chi tiết đơn hàng và thông tin đặt bàn của bạn:</p>

    <h3 style="color: #e53935;">🧾 Đơn hàng</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background-color: #e53935; color: white;">
                <th style="padding: 8px; text-align: left;">Món ăn</th>
                <th style="padding: 8px; text-align: center;">Cấp độ cay</th>
                <th style="padding: 8px; text-align: center;">Số lượng</th>
                <th style="padding: 8px; text-align: right;">Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['order_details'] as $item)
            <tr>
                <td style="padding: 8px;">
                    {{ $item['name'] }}
                    @if (!empty($item['toppings']))
                        <br>
                        <small style="color: #555;">Topping:
                            {{ collect($item['toppings'])->pluck('name')->implode(', ') }}
                        </small>
                    @endif
                </td>
                <td style="padding: 8px; text-align: center;">Cấp 3</td> <!-- Nếu có cấp độ cay thì thay bằng dynamic -->
                <td style="padding: 8px; text-align: center;">{{ $item['quantity'] }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($item['price']) }}đ</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr style="font-weight: bold;">
                <td colspan="3" style="padding: 8px; text-align: right;">Tổng cộng:</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($mailData['total_price']) }}VND</td>
            </tr>
        </tfoot>
    </table>

    <h3 style="margin-top: 20px; color: #e53935;">📌 Thông tin đặt bàn</h3>
    <p><strong>Tên khách:</strong> {{ $mailData['guest_name'] }}</p>
    <p><strong>Số điện thoại:</strong> {{ $mailData['guest_phone'] }}</p>
    <p><strong>Ngày đặt bàn:</strong> {{ $mailData['reservations_time'] }}</p>
    <p><strong>Giờ đặt:</strong> {{ $mailData['reservations_time'] }}</p>
    <p><strong>Số lượng người:</strong> {{ $mailData['guest_count'] }}</p>
    <p><strong>Ghi chú thêm:</strong> {{ $mailData['note'] ?? 'Không có' }}</p>

    {{-- <h3 style="margin-top: 20px; color: #e53935;">🚚 Thông tin giao hàng</h3>
    <p><strong>Địa chỉ:</strong> {{dia_chi}}</p>
    <p><strong>Thanh toán:</strong> {{phuong_thuc_thanh_toan}}</p>

    <p style="margin-top: 20px;">🕒 Thời gian giao dự kiến: <strong>30 - 45 phút</strong></p>
    <p>❤️ Cảm ơn bạn đã tin tưởng và ủng hộ quán. Chúc bạn một bữa ăn ngon miệng và vui vẻ!</p> --}}

</div>

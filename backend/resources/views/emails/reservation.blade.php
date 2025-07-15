<div
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #ffffff; border: 1px solid #e8e8e8; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <div style="background-color: #c92c3c; padding: 10px; text-align: center;">
        <img src="https://res.cloudinary.com/daqhc6id1/image/upload/v1750930229/image.png" alt="TikTokaQa Logo"
            style="height: 60px; display: inline-block; vertical-align: middle;" />
    </div>
    <h2
        style="font-size: 20px; text-align: center; margin: 20px 0; text-transform: uppercase; color: #c92c3c; letter-spacing: 1px;">
        XÁC NHẬN ĐƠN HÀNG
    </h2>

    <div style="padding: 0 30px 30px 30px;">
        <div style="font-size: 14px; color: #777777; margin-bottom: 25px;">
            Chào <strong style="color: #333333;">{{ $mailData['guest_name'] }}</strong>,<br>
            <div>
                Cảm ơn bạn đã đặt món và đặt bàn tại <span style="color: #c92c3c">TikTokaQa</span>! Đơn hàng và thông tin
                đặt bàn của bạn đã được xác nhận. Chúng tôi sẽ gửi một email khác khi đơn của bạn đã được chuẩn bị.
            </div>
        </div>

        <div style="border-top: 1px solid #e8e8e8; padding-top: 25px;">
            <h3
                style="font-size: 16px; color: #333333; margin-top: 0; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                MÓN ĐÃ ĐẶT
            </h3>
            @foreach ($mailData['order_details'] as $item)
                <table cellspacing="0" cellpadding="0" border="0" width="100%"
                    style="margin-bottom: 20px; border-bottom: 1px dashed #eeeeee; padding-bottom: 15px;">
                    <tr>
                        <td style="vertical-align: top; padding-right: 15px; width: 60px;">
                            <img src="https://res.cloudinary.com/daqhc6id1/image/upload/v1750924403/{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                                style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #eeeeee; background-color: #ffffff; display: block;">
                        </td>
                        <td style="vertical-align: top; width: 70%;">
                            <p style="margin: 0; font-size: 15px; color: #333333; font-weight: bold;">
                                {{ $item['name'] }}
                            </p>
                            @if (!empty($item['toppings']))
                                <ul
                                    style="margin: 5px 0 0 15px; padding: 0; list-style-type: disc; font-size: 13px; color: #777777;">
                                    @foreach ($item['toppings'] as $index => $toping)
                                        @if ($index < 3)
                                            @if ($toping['price'] > 0)
                                                <li>{{ $toping['name'] }} - {{ number_format($toping['price']) }} VNĐ
                                                </li>
                                            @else
                                                <li>{{ $toping['name'] }}</li>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if (count($item['toppings']) > 3)
                                        <li>... và {{ count($item['toppings']) - 3 }} topping khác</li>
                                    @endif
                                </ul>
                            @endif
                        </td>

                        <td style="vertical-align: top; text-align: right; width: 30%;">
                            <p style="margin: 0; font-size: 15px; color: #333333;">x{{ $item['quantity'] }}</p>
                            <p style="margin: 5px 0 0 0; font-size: 15px; color: #333333; font-weight: bold;">
                                {{ number_format($item['price']) }} VND
                            </p>
                        </td>
                    </tr>
                </table>
            @endforeach
        </div>

        <div style="border-top: 1px solid #e8e8e8; padding-top: 20px; margin-top: 25px;">
            <table style="width: 100%; font-size: 14px; color: #555555;">
                <tr>
                    <td style="padding-bottom: 5px;">Tạm tính:</td>
                    <td style="text-align: right; padding-bottom: 5px;">
                        {{ number_format($mailData['subtotal']) }} VND
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">Phí giữ bàn:</td>
                    <td style="text-align: right; padding-bottom: 5px;">100,000 VND</td>
                </tr>
                <tr style="font-size: 16px; color: #c92c3c; font-weight: bold; border-top: 1px solid #e8e8e8;">
                    <td style="padding-top: 10px;">Tổng tiền:</td>
                    <td style="text-align: right; padding-top: 10px;">
                        {{ number_format($mailData['total_price']) }} VND
                    </td>
                </tr>
            </table>
        </div>

        <div style="border-top: 1px solid #e8e8e8; padding-top: 20px; margin-top: 25px; margin-bottom:10px;">
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <h4
                        style="font-size: 16px; color: #333333; margin-top: 0; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        THÔNG TIN ĐẶT BÀN
                    </h4>
                    <td style="vertical-align: top; padding-right: 20px;">
                        <p style="margin: 0;"><strong>Tên khách:</strong> {{ $mailData['guest_name'] }}</p>
                        <p style="margin: 5px 0 0 0;"><strong>Số điện thoại:</strong> {{ $mailData['guest_phone'] }}
                        </p>
                        <p style="margin: 5px 0 0 0;"><strong>Số lượng người:</strong> {{ $mailData['guest_count'] }}
                        </p>
                        <p style="margin: 5px 0 0 0;"><strong>Ghi chú:</strong> {{ $mailData['note'] ?? 'Không có' }}
                        </p>
                    </td>
                    <td style="vertical-align: top; padding-left: 20px; color: #333333">
                        @if (!empty($mailData['tables']))
                            @php
                                $firstTable = $mailData['tables'][0];
                                $reservedDate = \Carbon\Carbon::parse($firstTable['reserved_from'])->format('d/m/Y');
                                $reservedTime = \Carbon\Carbon::parse($firstTable['reserved_from'])->format('H:i');
                                $tableNumbers = collect($mailData['tables'])->pluck('table_number')->implode(', ');
                            @endphp

                            <p style="margin: 0; color: #333333"><strong>Các bàn đã đặt:</strong> Bàn số
                                {{ $tableNumbers }}</p>
                            <p style="margin: 5px 0 0 0; color: #333333"><strong>Ngày đặt:</strong> {{ $reservedDate }}
                            </p>
                            <p style="margin: 5px 0 0 0; color: #333333"><strong>Giờ đặt:</strong> {{ $reservedTime }}
                            </p>
                            <p style="margin: 5px 0 0 0; color: #333333"><strong>Trạng thái đơn:</strong>
                                {{ $mailData['order_status'] }}</p>
                        @else
                            <p>Không có thông tin đặt bàn.</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div style="border-top: 1px solid #e8e8e8; padding-top: 25px; text-align: center;">
            <div style="font-size: 14px; color: #777777; margin-bottom: 15px;">
                Khi đến nhà hàng, vui lòng đưa mã QR cho nhân viên để xác nhận đơn hàng của bạn
            </div>
            @if (!empty($mailData['qr_url']))
                <img src="{{ $mailData['qr_url'] }}" alt="QR Code" style="width: 250px;" />
            @endif
            <br>

            <div style="font-size: 12px; color: #777777; margin-top: 15px;">
                Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi tại
                <a href="mailto:support@tiktokaqa.com"
                    style="color: #c92c3c; text-decoration: none;">support@tiktokaqa.com</a> hoặc gọi <a
                    href="tel:+84123456789" style="color: #c92c3c; text-decoration: none;">(+84) 123 456 789</a>
            </div>
        </div>
    </div>

    <div
        style="background-color: #f7f7f7; padding: 20px 0; text-align: center; font-size: 12px; color: #777777; border-top: 1px solid #e8e8e8;">
        <p style="margin: 0;">&copy; 2025 TikTokaQa</p>
        <p style="margin: 5px 0 0 0;">123 Đường ABC, Quận XYZ, Thành phố HCM, Việt Nam</p>
        <img src="https://res.cloudinary.com/daqhc6id1/image/upload/v1750927277/logonew.png" alt="TikTokaQa Logo"
            style="height: 30px; margin-top: 15px; opacity: 0.7;" />
    </div>
</div>

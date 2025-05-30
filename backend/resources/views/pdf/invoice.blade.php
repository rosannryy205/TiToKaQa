<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>HÓA ĐƠN</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 0;
            text-align: center;
        }

        .header,
        .footer {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .info {
            text-align: left;
            margin: 5px 0;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto; /* Cho phép bảng tự động điều chỉnh kích thước cột */
            text-align: left;

        }

        th,
        td {
            padding: 4px;
            border-bottom: 1px solid #ddd;
        }

        .name {
            width: 50%;
            text-align: left;
        }

        .qty {
            width: 20%;
            text-align: center;
        }

        .price {
            width: 30%;
            text-align: right;
        }

        .topping {
            width: 50%;
            text-align: left;
        }

        .topping-price {
            width: 30%;
            text-align: right;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
        }

        @page {
    size: 80mm 150mm;
    margin: 5mm;
}

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            NHÀ HÀNG TIKTOKAQA<br>
            HÓA ĐƠN THANH TOÁN
        </div>

        <div class="info">
            Mã đơn: {{ $order_id }}<br>
            Khách: {{ $guest_name }} - {{ $guest_phone }}<br>
            Thời gian: {{ $order_time }}
        </div>

        <div class="line"></div>

        <table>
            <thead>
                <tr>
                    <th class="name">Món ăn</th>
                    <th class="qty" style="text-align: center;">SL</th>
                    <th class="price">Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order_details as $detail)
                    <tr>
                        <td>{{ $detail['name'] }}</td>
                        <td class="qty" style="text-align: center;">x{{ $detail['quantity'] }}</td>
                        <td class="price">{{ number_format($detail['price'], 0) }}₫</td>
                    </tr>
                    @foreach ($detail['toppings'] as $topping)
                        <tr>
                            <td class="topping">+ {{ $topping['name'] }}</td>
                            <td class="qty" style="text-align: center;">&nbsp;</td>
                            <td class="topping-price">+{{ number_format($topping['price'], 0) }}₫</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <div class="total">
            TỔNG TIỀN: {{ number_format($total_price, 0) }}₫
        </div>

        @if ($note)
            <div class="info">Ghi chú: {{ $note }}</div>
        @endif

        @if (!empty($tables))
            <div class="info">Bàn: {{ implode(', ', $tables) }}</div>
        @endif

        <div class="footer">
            --- CẢM ƠN QUÝ KHÁCH ---
        </div>

    </div>
</body>

</html>

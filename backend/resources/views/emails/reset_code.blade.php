<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<style>
    .container-mailwelcome {
            font-family: Arial, sans-serif;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
        }
        .highlight {
            color: #0a75ad;
            font-weight: bold;
        }
</style>

<body>
    <div class="container-mailwelcome">
        {{-- <img class="img-welcome" src="{{ $message->embed(public_path('images/logonew.png')) }}" alt="TIKIKAQA"> --}}
        <h2>Mã xác minh</h2>
        <p>Mã xác minh của bạn là</p>
        <h1 style="color: #3157DE;">{{ $code }}</h1>
        <p>Mã này sẽ hết hạn sau 5 phút</p>
        <p class="highlight">Chúc bạn một ngày vui vẻ và trải nghiệm tốt!</p>
    </div>
</body>
</html>


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
        {{-- <div style="font-size: 14px; color: #777777; margin-bottom: 25px;">
            <div>
                Cảm ơn bạn đã đặt món và đặt bàn tại <span style="color: #c92c3c">TikTokaQa</span>! Đơn hàng và thông tin
                đặt bàn của bạn đã được xác nhận. Chúng tôi sẽ gửi một email khác khi đơn của bạn đã được chuẩn bị.
            </div>
        </div> --}}




        <div style="border-top: 1px solid #e8e8e8; padding-top: 20px; margin-top: 25px; margin-bottom:10px;">
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <h4
                        style="font-size: 16px; color: #333333; margin-top: 0; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        MÃ XÁC MINH
                    </h4>
                    <td style="vertical-align: top; padding-right: 20px;">
                        <p style="margin: 0;"><strong>Mã xác minh của bạn là</strong> </p>
                        <h1 class="text-center" style="color:#c92c3c">{{ $code }}</h1>
                        </p>

                    </td>
                </tr>
            </table>
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

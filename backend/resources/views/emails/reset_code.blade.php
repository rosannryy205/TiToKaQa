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
        <h2>Mã khôi phục mật khẩu</h2>
        <p>Mã khôi phục của bạn là</p>
        <h1 style="color: #3157DE;">{{ $code }}</h1>
        <p>Mã này sẽ hết hạn sau 5 phút</p>
        <p class="highlight">Chúc bạn một ngày vui vẻ và trải nghiệm tốt!</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
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
        .img-welcome{
            width :100px ;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container-mailwelcome">
        {{-- <img class="img-welcome" src="{{ $message->embed(public_path('images/logonew.png')) }}" alt="TIKIKAQA"> --}}
        <h2>Xin chào {{ $user->username }}!</h2>
        <p>Chào mừng bạn đã đăng ký tài khoản tại TIKIKAQA của chúng tôi.</p>
        <p class="highlight">Chúc bạn một ngày vui vẻ và trải nghiệm tốt!</p>
    </div>
</body>
</html>

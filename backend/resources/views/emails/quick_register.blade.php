    <div
        style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #ffffff; border: 1px solid #e8e8e8; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="background-color: #c92c3c; padding: 10px; text-align: center;">
            <img src="https://res.cloudinary.com/daqhc6id1/image/upload/v1750930229/image.png" alt="TikTokaQa Logo"
                style="height: 60px; display: inline-block; vertical-align: middle;" />
        </div>

        <h2
            style="font-size: 20px; text-align: center; margin: 20px 0; text-transform: uppercase; color: #c92c3c; letter-spacing: 1px;">
            ĐĂNG KÝ TÀI KHOẢN THÀNH CÔNG
        </h2>

        <div style="padding: 0 30px 30px 30px;">
            <div style="font-size: 14px; color: #777777; margin-bottom: 25px;">
                Xin chào <strong style="color: #333333;">{{ $name }}</strong>,<br>
                <div style="margin-top: 10px;">
                    Tài khoản của bạn tại <span style="color: #c92c3c;">TikTokaQa</span> đã được tạo thành công 🎉<br><br>
                    Thông tin đăng nhập của bạn:
                    <ul style="margin: 10px 0; padding-left: 20px; color: #333;">
                        <li><strong>Email:</strong> {{ $email }}</li>
                        <li><strong>Tên đăng nhập:</strong> {{ $name }}</li>
                        <li><strong>Mật khẩu:</strong> {{ $password }}</li>
                    </ul>
                    Vui lòng đăng nhập và thay đổi mật khẩu để bảo mật tốt hơn.
                </div>
            </div>

            <div style="border-top: 1px solid #e8e8e8; padding-top: 20px; text-align: center;">
                <a href="{{ url('https://titokaqarestaurant.online/login') }}"
                    style="background-color: #c92c3c; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-size: 14px; display: inline-block;">
                    Đăng nhập ngay
                </a>
            </div>

            <div style="border-top: 1px solid #e8e8e8; padding-top: 25px; text-align: center;">
                <div style="font-size: 12px; color: #777777; margin-top: 15px;">
                    Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi tại
                    <a href="mailto:support@tiktokaqa.com"
                        style="color: #c92c3c; text-decoration: none;">support@tiktokaqa.com</a> hoặc gọi
                    <a href="tel:+84123456789" style="color: #c92c3c; text-decoration: none;">(+84) 123 456 789</a>
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

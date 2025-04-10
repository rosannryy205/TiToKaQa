<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt bàn thành công')
                    ->view('emails.reservation') // bạn có thể tạo file blade tương ứng
                    ->with('mailData', $this->mailData);
    }
}

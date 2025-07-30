# TiToKaQa

composer install - backend

php artisan migrate --seed - backend

npm install vite --save-dev - vuejs

ai lỗi session table thì chạy
php artisan session:table
php artisan migrate
rồi
php artisan config:cache
php artisan cache:clear

npm install numeral

cấu hình lại mail trong file .env nha mn
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=wd19305.ps39144.toanlt@gmail.com
MAIL_PASSWORD=httnroupadxdwkvp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=wd19305.ps39144.toanlt@gmail.com
MAIL_FROM_NAME="TITOKAQA"

npm install jquery

composer require laravel/socialite

cai chung chi SSL Certs de tranh loi google
https://curl.se/docs/caextract.html
cai xong vao php.ini tim curl.cainfo
bo dau ; o truoc va them path chua chung chi vua tai vd "C:\path"

npm install vue-toastification@2.0.0-rc.5

composer require barryvdh/laravel-dompdf - cài cái này để in hoá đơn

npm install haversine-distance - cài cái này để tính tọa độ địa lí

npm install leaflet

npm install vue-leaflet

npm install sweetalert2

npm install @fullcalendar/vue3 @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction @fullcalendar/resource-timegrid

npm install vuedraggable@next

npm i --save @fortawesome/vue-fontawesome@latest-3 npm install --save @fortawesome/free-solid-svg-icons

composer require spatie/laravel-permission

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

để vào đc admin vào database tìm bảng model has role đổi role_id thành 4 rồi đăng nhập lại

php atisan storage:link

composer require pusher/pusher-php-server

php artisan make:event MessageSent

npm install --save pusher-js

npm install uuid

composer require simplesoftwareio/simple-qrcode

composer require cloudinary-labs/cloudinary-laravel

npm install html5-qrcode

ai đặt bàn mà không có qr code thì lên ytb coi họ chỉ cách tải imagick

CLOUDINARY_URL=cloudinary://341442476722344:n3tylOnfBQDsRrO5GnixiaSfMWk@daqhc6id1

QUEUE_CONNECTION=database

composer require google/cloud-dialogflow

DIALOGFLOW_PROJECT_ID=dialogflow-466119

npm install laravel-echo

npm install chart.js npm install vue-chartjs@5 chart.js@^4 npm install tween.js npm install perfect-scrollbar npm install -D sass

npm install flatpickr

mở tab chạy php artisan queue:work -> chạy jobs (demo chatbot)

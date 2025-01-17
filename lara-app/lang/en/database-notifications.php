<?php

use App\Enums\NotificationKeyNameEnum;

return [
    NotificationKeyNameEnum::SIGNUP_SUCCESSFUL->value => [
        'title' => 'کاربر گرامی',
        'message' => 'از ثبت نام شما سپاسگزاریم! بسیار خوشحالیم که به خانواده :app_name پیوسته اید.',
    ],

    NotificationKeyNameEnum::BID_REGISTERED_TO_REQUESTER->value => [
        'title' => 'ثبت پیشنهاد جدید روی درخواست شماره :request_number',
        'message' => 'پیشنهادی به قیمت :bid_price تومان روی درخواست شما ثبت گردید. مشاهده جزئیات درخواست'
    ],

    NotificationKeyNameEnum::BID_REGISTERED_TO_OTHER_BIDDERS->value => [
        'title' => 'کاهش ارزش پیشنهاد شما روی درخواست شماره :request_number',
        'message' => 'کاربر دیگری پیشنهاد بهتری به قیمت :bid_price تومان ثبت کرد. مشاهده جزئیات درخواست'
    ],

    NotificationKeyNameEnum::BID_ACCEPTED_AUTOMATIC_TO_OTHER_BIDDERS->value => [
        'title' => 'رد پیشنهاد شما روی درخواست شماره :request_number',
        'message' => 'برای مشاهده درخواست های فعال و ارسال پیشنهادات جدید، روی این اعلان کلیک کنید.'
    ],

    NotificationKeyNameEnum::BID_ACCEPTED_AUTOMATIC_TO_BIDDER->value => [
        'title' => 'تائید خودکار پیشنهاد شما روی درخواست شماره :request_number',
        'message' => 'پیشنهاد به صورت خودکار پذیرفته شد. برای ادامه مراحل معامله روی این اعلان کلیک کنید.'
    ],

    NotificationKeyNameEnum::BID_ACCEPTED_AUTOMATIC_TO_REQUESTER->value => [
        'title' => 'تائید خودکار پیشنهاد روی درخواست شماره :request_number',
        'message' => 'پیشنهاد به صورت خودکار پذیرفته شد. برای ادامه مراحل معامله روی این اعلان کلیک کنید.'
    ],

    NotificationKeyNameEnum::BID_ACCEPTED_BY_REQUESTER_TO_BIDDER->value => [
        'title' => 'تائید پیشنهاد شما روی درخواست شماره :request_number',
        'message' => 'پیشنهاد شما توسط درخواست کننده پذیرفته شد. برای ادامه مراحل معامله روی این اعلان کلیک کنید.'
    ],

    NotificationKeyNameEnum::BID_ACCEPTED_BY_REQUESTER_TO_OTHER_BIDDERS->value => [
        'title' => 'رد پیشنهاد شما روی درخواست شماره :request_number',
        'message' => 'برای مشاهده درخواست های فعال و ارسال پیشنهادات جدید، روی این اعلان کلیک کنید.'
    ],

    NotificationKeyNameEnum::PAY_TOMAN_TO_SYSTEM_TO_BUYER->value => [
        'title' => 'واریز موفقیت‌آمیز معامله شماره :trade_number',
        'message' => 'مبلغ :invoice_amount تومان از جانب شما با موفقیت واریز شد.'
    ],

    NotificationKeyNameEnum::PAY_TOMAN_TO_SYSTEM_TO_SELLER->value => [
        'title' => 'صورتحساب معامله شماره :trade_number',
        'message' => 'صورتحساب واریز ارزی شما آماده پرداخت است. لطفاً جهت تکمیل فرآیند روی این اعلان کلیک کنید.'
    ]
];

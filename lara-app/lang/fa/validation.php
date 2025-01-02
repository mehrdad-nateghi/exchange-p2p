<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute باید پذیرفته شود.',
    'accepted_if' => 'هنگامی که :other برابر با :value است :attribute باید پذیرفته شود.',
    'active_url' => ':attribute یک URL معتبر نیست.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد یا برابر با :date باشد.',
    'alpha' => ':attribute فقط باید حروف باشد.',
    'alpha_dash' => ':attribute فقط باید حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط باید حروف و اعداد باشد.',
    'array' => ':attribute باید آرایه باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل یا برابر با :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min و :max آیتم باشد.',
        'file' => ':attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'string' => ':attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => ':attribute فقط می‌تواند درست یا غلط باشد.',
    'confirmed' => 'تأیید :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور اشتباه است.',
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'date_equals' => ':attribute باید تاریخی برابر با :date باشد.',
    'date_format' => ':attribute با فرمت :format مطابقت ندارد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => 'وقتی :other برابر با :value است، :attribute باید رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min و :max رقم باشد.',
    'dimensions' => ':attribute ابعاد تصویر نامعتبر دارد.',
    'distinct' => ':attribute دارای مقدار تکراری است.',
    'doesnt_end_with' => ':attribute نباید با این مقادیر تمام شود: :values.',
    'doesnt_start_with' => ':attribute نباید با این مقادیر شروع شود: :values.',
    'email' => ':attribute باید یک ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از این مقادیر تمام شود: :values',
    'enum' => ':attribute انتخاب شده نامعتبر است.',
    'exists' => ':attribute انتخاب شده نامعتبر است.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute باید مقدار داشته باشد.',
    'gt' => [
        'array' => ':attribute باید بیشتر از :value آیتم داشته باشد.',
        'file' => ':attribute باید بزرگتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر از :value باشد.',
        'string' => ':attribute باید بیشتر از :value کاراکتر باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید حداقل :value آیتم داشته باشد.',
        'file' => ':attribute باید بزرگتر یا مساوی :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر یا مساوی :value باشد.',
        'string' => ':attribute باید بیشتر یا مساوی :value کاراکتر باشد.',
    ],
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب شده نامعتبر است.',
    'in_array' => 'فیلد :attribute در :other وجود ندارد.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'ip' => ':attribute باید IP معتبر باشد.',
    'ipv4' => ':attribute باید IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید IPv6 معتبر باشد.',
    'json' => ':attribute باید متن JSON معتبر باشد.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => ':attribute باید کوچکتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچکتر از :value باشد.',
        'string' => ':attribute باید کمتر از :value کاراکتر باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیشتر از :value آیتم داشته باشد.',
        'file' => ':attribute باید کوچکتر یا مساوی :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچکتر یا مساوی :value باشد.',
        'string' => ':attribute باید کمتر یا مساوی :value کاراکتر باشد.',
    ],
    'mac_address' => ':attribute باید یک مک آدرس معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیشتر از :max آیتم داشته باشد.',
        'file' => ':attribute نباید بزرگتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگتر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
    ],
    'max_digits' => ':attribute نباید بیشتر از :max رقم داشته باشد.',
    'mimes' => ':attribute باید فایلی از نوع: :values باشد.',
    'mimetypes' => ':attribute باید فایلی از نوع: :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min آیتم داشته باشد.',
        'file' => ':attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب شده نامعتبر است.',
    'not_regex' => 'فرمت :attribute نامعتبر است.',
    'numeric' => ':attribute باید عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل شامل یک حرف باشد.',
        'mixed' => ':attribute باید شامل حداقل یک حرف بزرگ و یک حرف کوچک باشد.',
        'numbers' => ':attribute باید شامل حداقل یک عدد باشد.',
        'symbols' => ':attribute باید شامل حداقل یک نماد باشد.',
        'uncompromised' => ':attribute داده شده در نشت داده ظاهر شده است. لطفاً یک :attribute متفاوت انتخاب کنید.',
    ],
    'present' => 'فیلد :attribute باید موجود باشد.',
    'prohibited' => 'فیلد :attribute ممنوع است.',
    'prohibited_if' => 'وقتی :other برابر :value است، فیلد :attribute ممنوع است.',
    'prohibited_unless' => 'فیلد :attribute ممنوع است مگر اینکه :other در :values باشد.',
    'prohibits' => 'فیلد :attribute اجازه حضور :other را نمی‌دهد.',
    'regex' => 'فرمت :attribute نامعتبر است.',
    'required' => 'فیلد :attribute الزامی است.',
    'required_array_keys' => 'فیلد :attribute باید شامل ورودی‌هایی برای: :values باشد.',
    'required_if' => 'وقتی :other برابر با :value است، فیلد :attribute الزامی است.',
    'required_if_accepted' => 'هنگامی که :other پذیرفته شده است، فیلد :attribute الزامی است.',
    'required_unless' => 'فیلد :attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => 'وقتی :values موجود است، فیلد :attribute الزامی است.',
    'required_with_all' => 'وقتی :values موجود است، فیلد :attribute الزامی است.',
    'required_without' => 'وقتی :values موجود نیست، فیلد :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ یک از :values موجود نیست، فیلد :attribute الزامی است.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'array' => ':attribute باید شامل :size آیتم باشد.',
        'file' => ':attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید :size باشد.',
        'string' => ':attribute باید :size کاراکتر باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از این مقادیر شروع شود: :values',
    'string' => ':attribute باید رشته باشد.',
    'timezone' => ':attribute باید منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً گرفته شده است.',
    'uploaded' => 'آپلود :attribute شکست خورد.',
    'url' => ':attribute باید URL معتبر باشد.',
    'uuid' => ':attribute باید UUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

    // Custom validation messages
    'verification_code_not_expired' => 'در حال حاضر امکان درخواست کد تایید جدید وجود ندارد. لطفا تا منقضی شدن کد قبلی صبر کنید.',
    'invalid_verification_code' => 'کد تایید وارد شده معتبر نمی‌باشد.',
    'invalid_request' => 'درخواست انتخاب شده نامعتبر است.',
    'bid_price_must_between' => 'قیمت پیشنهادی باید بین :min و :max باشد.',
    'receipt_already_accepted_by_buyer' => 'این رسید قبلاً توسط خریدار تایید شده است و قابل ویرایش نمی‌باشد.',
    'receipt_rejection_time' => 'شما فقط پس از گذشت ۷۰٪ از زمان انقضا می‌توانید رسید را رد کنید.',
    'receipt_already_exists' => 'برای این مرحله قبلاً یک رسید بارگذاری شده است. شما نمی‌توانید رسید دیگری بارگذاری کنید.',
    'request_must_have_rial_account' => 'درخواست باید دارای حساب بانکی ریالی باشد تا بتوانید رسید را بارگذاری کنید.',
    'request_has_rial_payment_method' => 'یک روش پرداخت بانکی ریالی برای این درخواست قبلاً ثبت شده است. شما نمی‌توانید روش دیگری اضافه کنید.',
    'payment_method_in_use' => 'این روش پرداخت در حال استفاده در یک درخواست فعال است و قابل ویرایش یا حذف نمی‌باشد.',









];

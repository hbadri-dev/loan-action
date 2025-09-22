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

    'accepted' => 'فیلد :attribute باید پذیرفته شود.',
    'accepted_if' => 'فیلد :attribute باید پذیرفته شود زمانی که :other برابر :value است.',
    'active_url' => 'فیلد :attribute یک URL معتبر نیست.',
    'after' => 'فیلد :attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => 'فیلد :attribute باید تاریخی بعد از یا برابر :date باشد.',
    'alpha' => 'فیلد :attribute فقط می‌تواند شامل حروف باشد.',
    'alpha_dash' => 'فیلد :attribute فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => 'فیلد :attribute فقط می‌تواند شامل حروف و اعداد باشد.',
    'array' => 'فیلد :attribute باید یک آرایه باشد.',
    'ascii' => 'فیلد :attribute باید فقط شامل کاراکترهای تک‌بایتی و عددی باشد.',
    'before' => 'فیلد :attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => 'فیلد :attribute باید تاریخی قبل از یا برابر :date باشد.',
    'between' => [
        'array' => 'فیلد :attribute باید بین :min و :max آیتم داشته باشد.',
        'file' => 'فیلد :attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید بین :min و :max باشد.',
        'string' => 'فیلد :attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => 'فیلد :attribute باید true یا false باشد.',
    'can' => 'فیلد :attribute حاوی یک مقدار غیرمجاز است.',
    'confirmed' => 'تأیید فیلد :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => 'فیلد :attribute یک تاریخ معتبر نیست.',
    'date_equals' => 'فیلد :attribute باید تاریخی برابر :date باشد.',
    'date_format' => 'فیلد :attribute با فرمت :format مطابقت ندارد.',
    'decimal' => 'فیلد :attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => 'فیلد :attribute باید رد شود.',
    'declined_if' => 'فیلد :attribute باید رد شود زمانی که :other برابر :value است.',
    'different' => 'فیلد :attribute و :other باید متفاوت باشند.',
    'digits' => 'فیلد :attribute باید :digits رقم باشد.',
    'digits_between' => 'فیلد :attribute باید بین :min و :max رقم باشد.',
    'dimensions' => 'فیلد :attribute ابعاد تصویر نامعتبر دارد.',
    'distinct' => 'فیلد :attribute مقدار تکراری دارد.',
    'doesnt_end_with' => 'فیلد :attribute نباید با یکی از موارد زیر تمام شود: :values.',
    'doesnt_start_with' => 'فیلد :attribute نباید با یکی از موارد زیر شروع شود: :values.',
    'email' => 'فیلد :attribute باید یک آدرس ایمیل معتبر باشد.',
    'ends_with' => 'فیلد :attribute باید با یکی از موارد زیر تمام شود: :values.',
    'enum' => ':attribute انتخاب شده نامعتبر است.',
    'exists' => ':attribute انتخاب شده نامعتبر است.',
    'extensions' => 'فیلد :attribute باید یکی از پسوندهای زیر را داشته باشد: :values.',
    'file' => 'فیلد :attribute باید یک فایل باشد.',
    'filled' => 'فیلد :attribute باید مقدار داشته باشد.',
    'gt' => [
        'array' => 'فیلد :attribute باید بیشتر از :value آیتم داشته باشد.',
        'file' => 'فیلد :attribute باید بزرگ‌تر از :value کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید بزرگ‌تر از :value باشد.',
        'string' => 'فیلد :attribute باید بیشتر از :value کاراکتر داشته باشد.',
    ],
    'gte' => [
        'array' => 'فیلد :attribute باید :value آیتم یا بیشتر داشته باشد.',
        'file' => 'فیلد :attribute باید بزرگ‌تر یا مساوی :value کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید بزرگ‌تر یا مساوی :value باشد.',
        'string' => 'فیلد :attribute باید :value کاراکتر یا بیشتر داشته باشد.',
    ],
    'hex_color' => 'فیلد :attribute باید یک رنگ هگز معتبر باشد.',
    'image' => 'فیلد :attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب شده نامعتبر است.',
    'in_array' => 'فیلد :attribute در :other وجود ندارد.',
    'integer' => 'فیلد :attribute باید یک عدد صحیح باشد.',
    'ip' => 'فیلد :attribute باید یک آدرس IP معتبر باشد.',
    'ipv4' => 'فیلد :attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => 'فیلد :attribute باید یک آدرس IPv6 معتبر باشد.',
    'json' => 'فیلد :attribute باید یک رشته JSON معتبر باشد.',
    'lowercase' => 'فیلد :attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => 'فیلد :attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => 'فیلد :attribute باید کوچک‌تر از :value کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید کوچک‌تر از :value باشد.',
        'string' => 'فیلد :attribute باید کمتر از :value کاراکتر داشته باشد.',
    ],
    'lte' => [
        'array' => 'فیلد :attribute نباید بیشتر از :value آیتم داشته باشد.',
        'file' => 'فیلد :attribute باید کوچک‌تر یا مساوی :value کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید کوچک‌تر یا مساوی :value باشد.',
        'string' => 'فیلد :attribute باید :value کاراکتر یا کمتر داشته باشد.',
    ],
    'mac_address' => 'فیلد :attribute باید یک آدرس MAC معتبر باشد.',
    'max' => [
        'array' => 'فیلد :attribute نباید بیشتر از :max آیتم داشته باشد.',
        'file' => 'فیلد :attribute نباید بزرگ‌تر از :max کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute نباید بزرگ‌تر از :max باشد.',
        'string' => 'فیلد :attribute نباید بیشتر از :max کاراکتر داشته باشد.',
    ],
    'max_digits' => 'فیلد :attribute نباید بیشتر از :max رقم داشته باشد.',
    'mimes' => 'فیلد :attribute باید یک فایل از نوع: :values باشد.',
    'mimetypes' => 'فیلد :attribute باید یک فایل از نوع: :values باشد.',
    'min' => [
        'array' => 'فیلد :attribute باید حداقل :min آیتم داشته باشد.',
        'file' => 'فیلد :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید حداقل :min باشد.',
        'string' => 'فیلد :attribute باید حداقل :min کاراکتر داشته باشد.',
    ],
    'min_digits' => 'فیلد :attribute باید حداقل :min رقم داشته باشد.',
    'missing' => 'فیلد :attribute باید مفقود باشد.',
    'missing_if' => 'فیلد :attribute باید مفقود باشد زمانی که :other برابر :value است.',
    'missing_unless' => 'فیلد :attribute باید مفقود باشد مگر اینکه :other برابر :value باشد.',
    'missing_with' => 'فیلد :attribute باید مفقود باشد زمانی که :values موجود است.',
    'missing_with_all' => 'فیلد :attribute باید مفقود باشد زمانی که :values موجود است.',
    'multiple_of' => 'فیلد :attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب شده نامعتبر است.',
    'not_regex' => 'فرمت فیلد :attribute نامعتبر است.',
    'numeric' => 'فیلد :attribute باید یک عدد باشد.',
    'password' => [
        'letters' => 'فیلد :attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => 'فیلد :attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => 'فیلد :attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => 'فیلد :attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute داده شده در نشت داده‌ها ظاهر شده است. لطفاً یک :attribute متفاوت انتخاب کنید.',
    ],
    'present' => 'فیلد :attribute باید موجود باشد.',
    'present_if' => 'فیلد :attribute باید موجود باشد زمانی که :other برابر :value است.',
    'present_unless' => 'فیلد :attribute باید موجود باشد مگر اینکه :other برابر :value باشد.',
    'present_with' => 'فیلد :attribute باید موجود باشد زمانی که :values موجود است.',
    'present_with_all' => 'فیلد :attribute باید موجود باشد زمانی که :values موجود است.',
    'prohibited' => 'فیلد :attribute ممنوع است.',
    'prohibited_if' => 'فیلد :attribute ممنوع است زمانی که :other برابر :value است.',
    'prohibited_unless' => 'فیلد :attribute ممنوع است مگر اینکه :other در :values موجود باشد.',
    'prohibits' => 'فیلد :attribute مانع از وجود :other می‌شود.',
    'regex' => 'فرمت فیلد :attribute نامعتبر است.',
    'required' => 'فیلد :attribute الزامی است.',
    'required_array_keys' => 'فیلد :attribute باید ورودی‌هایی برای: :values داشته باشد.',
    'required_if' => 'فیلد :attribute الزامی است زمانی که :other برابر :value است.',
    'required_if_accepted' => 'فیلد :attribute الزامی است زمانی که :other پذیرفته شده است.',
    'required_unless' => 'فیلد :attribute الزامی است مگر اینکه :other در :values موجود باشد.',
    'required_with' => 'فیلد :attribute الزامی است زمانی که :values موجود است.',
    'required_with_all' => 'فیلد :attribute الزامی است زمانی که :values موجود است.',
    'required_without' => 'فیلد :attribute الزامی است زمانی که :values موجود نیست.',
    'required_without_all' => 'فیلد :attribute الزامی است زمانی که هیچ یک از :values موجود نیست.',
    'same' => 'فیلد :attribute و :other باید مطابقت داشته باشند.',
    'size' => [
        'array' => 'فیلد :attribute باید شامل :size آیتم باشد.',
        'file' => 'فیلد :attribute باید :size کیلوبایت باشد.',
        'numeric' => 'فیلد :attribute باید :size باشد.',
        'string' => 'فیلد :attribute باید :size کاراکتر باشد.',
    ],
    'starts_with' => 'فیلد :attribute باید با یکی از موارد زیر شروع شود: :values.',
    'string' => 'فیلد :attribute باید یک رشته باشد.',
    'timezone' => 'فیلد :attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً گرفته شده است.',
    'uploaded' => 'آپلود :attribute ناموفق بود.',
    'uppercase' => 'فیلد :attribute باید با حروف بزرگ باشد.',
    'url' => 'فیلد :attribute باید یک URL معتبر باشد.',
    'ulid' => 'فیلد :attribute باید یک ULID معتبر باشد.',
    'uuid' => 'فیلد :attribute باید یک UUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'پیام سفارشی',
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

    'attributes' => [
        'name' => 'نام',
        'email' => 'ایمیل',
        'phone' => 'شماره موبایل',
        'password' => 'رمز عبور',
        'password_confirmation' => 'تکرار رمز عبور',
        'title' => 'عنوان',
        'description' => 'توضیحات',
        'amount' => 'مبلغ',
        'price' => 'قیمت',
        'min_purchase_price' => 'حداقل قیمت خرید',
        'principal_amount' => 'مبلغ اصل وام',
        'interest_rate_percent' => 'نرخ بهره (درصد)',
        'term_months' => 'مدت وام (ماه)',
        'loan_type' => 'نوع وام',
        'status' => 'وضعیت',
        'auction_id' => 'شناسه مزایده',
        'user_id' => 'شناسه کاربر',
        'bid_amount' => 'مبلغ پیشنهاد',
        'receipt_image' => 'تصویر رسید',
        'transfer_receipt' => 'رسید انتقال',
        'national_id' => 'کد ملی',
        'otp_code' => 'کد تأیید',
        'purpose' => 'هدف',
        'reject_reason' => 'دلیل رد',
        'file' => 'فایل',
        'image' => 'تصویر',
        'date' => 'تاریخ',
        'time' => 'زمان',
        'created_at' => 'تاریخ ایجاد',
        'updated_at' => 'تاریخ بروزرسانی',
        'confirmed_at' => 'تاریخ تأیید',
        'completed_at' => 'تاریخ تکمیل',
        'locked_at' => 'تاریخ قفل شدن',
        'expires_at' => 'تاریخ انقضا',
        'used_at' => 'تاریخ استفاده',
        'reviewed_at' => 'تاریخ بررسی',
        'buyer_confirmed_at' => 'تاریخ تأیید خریدار',
        'admin_confirmed_at' => 'تاریخ تأیید ادمین',
    ],
];


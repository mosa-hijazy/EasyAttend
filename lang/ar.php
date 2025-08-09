<?php
return [
    // Nav items
    'users'               => 'المستخدمين',
    'add_user'            => 'إضافة مستخدم',
    'attendance'          => 'الحضور',
    'leaves'              => 'الإجازات',
    'my_attendance'       => 'حضوري',
    'dashboard'           => 'لوحة التحكم',
    'request_leave'       => 'طلب إجازة',
    'my_leaves'           => 'إجازاتي',
    'chat'                => 'الدردشة',
    'pay_stub'            => 'كشف الراتب',
    'logout'              => 'تسجيل الخروج',

    // Roles
    'role_admin'          => 'مدير',
    'role_employee'       => 'موظف',

    // Attendance page
    'today_attendance'    => 'حضور اليوم',
    'check_in'            => 'تسجيل الدخول',
    'check_out'           => 'تسجيل الخروج',
    'confirm_checkout'    => 'تأكيد الخروج',
    'todays_shifts'       => 'ورديات اليوم',
    'date'                => 'التاريخ',
    'check_in_time'       => 'وقت الدخول',
    'check_out_time'      => 'وقت الخروج',
    'total_hours'         => 'إجمالي الساعات',
    'extra_hours'         => 'ساعات إضافية',
    'no_records'          => 'لا توجد سجلات حضور.',
    'total_for'           => 'الإجمالي لـ',

    // Messages
    'early_checkin_block' => '⛔ لا يمكنك تسجيل الدخول قبل الساعة 08:00.',
    'checkin_success'     => '✅ تم تسجيل الدخول بنجاح.',
    'checkin_fail'        => '❌ فشل في تسجيل الدخول.',
    'email_fail'          => '⚠️ فشل إرسال بريد التأخير.',
    'less_than_hour'      => '⚠️ لقد سجلت الدخول منذ أقل من ساعة. اضغط "تأكيد الخروج" للمتابعة.',
    'checkout_success'    => '✅ تم تسجيل الخروج بنجاح.',
    'checkout_fail'       => '❌ فشل في تسجيل الخروج.',

    // Error
    'error_no_username'   => 'يجب على المستخدم أن يحتوي على اسم مستخدم لعرض الحضور.',

    // Dashboard
    'dashboard_title'     => 'لوحة الحضور',
    'dashboard_overview'  => 'نظرة عامة على اللوحة',
    'present_days'        => 'أيام الحضور',
    'absent_days'         => 'أيام التغيب',
    'attendance_summary'  => 'ملخص الحضور',

    // User info section
    'user_info'           => 'معلومات المستخدم',
    'name'                => 'الاسم',
    'email'               => 'البريد الإلكتروني',
    'role'                => 'الدور',
    'username'            => 'اسم المستخدم',

    // Chart
    'attendance_chart'    => 'مخطط الحضور',
    'summary_for'         => 'ملخص عن',

    // Search form
    'search_attendance'   => 'بحث الحضور',
    'show_attendance'     => 'عرض الحضور',

    // Table labels
    'attendance_for'      => 'الحضور لـ',
    'no_attendance_records'=> 'لا توجد سجلات حضور.',
    'status'              => 'الحالة',
    'details'             => 'التفاصيل',
    'present'             => 'حاضر',
    'absent'              => 'غائب',

    // in/out/total in details
    'in'                  => 'دخول',
    'out'                 => 'خروج',
    'total'               => 'الإجمالي',

    // date/month/year inputs
    'year'                => 'السنة',
    'month'               => 'الشهر',
    'start_day'           => 'يوم البدء',

    // Leave request page (new)
    'request_leave_title' => 'إرسال طلب إجازة',
    'from_date'           => 'من تاريخ',
    'to_date'             => 'إلى تاريخ',
    'leave_type'          => 'نوع الإجازة',
    'select_placeholder'  => '-- اختر --',
    'sick_leave'          => 'إجازة مرضية',
    'vacation'            => 'إجازة',
    'emergency'           => 'طارئ',
    'other'               => 'أخرى',
    'reason'              => 'السبب',
    'reason_placeholder'  => 'أضف التفاصيل…',
    'submit_request'      => 'إرسال الطلب',
    'leave_submit_success'=> '✅ تم إرسال طلب الإجازة بنجاح.',
    'all_fields_required' => '❌ جميع الحقول مطلوبة.',




        // My Leaves page
    'my_leave_requests' => 'طلبات الإجازة الخاصة بي',
    'new_request'       => 'طلب جديد',
    'filter_by_status'  => 'تصفية حسب الحالة',
    'all'               => 'الكل',
    'pending'           => 'قيد الانتظار',
    'approved'          => 'موافق عليه',
    'rejected'          => 'مرفوض',
    'apply'             => 'تطبيق',
    'leave_history'     => 'سجل الإجازات',
    'from_to'           => 'من → إلى',
    'type'               => 'النوع',
    'reason'             => 'السبب',
    'status'             => 'الحالة',
    'submitted_at'       => 'تاريخ الإرسال',
    'no_leave_requests'  => 'لا توجد طلبات إجازة.',

        // Chat page
    'chat'              => 'الدردشة',
    'search_contacts'   => 'ابحث عن جهات الاتصال…',
    'type_your_message' => 'اكتب رسالتك…',
    'send'              => 'إرسال',
    'select_contact'    => 'اختر جهة اتصال للبدء في الدردشة',
    'unknown'           => 'مجهول',

     // Salary Slip
    'salary_slip_title'     => 'كشف الراتب',
    'salary_slip_heading'   => 'كشف الراتب — {month}/{year}',
    'employee_name'         => 'اسم الموظف',
    'employee_id'           => 'معرّف الموظف',
    'hourly_rate'           => 'أجر الساعة',
    'total_hours'           => 'إجمالي الساعات',
    'extra_hours'           => 'ساعات إضافية',
    'base_salary'           => 'الراتب الأساسي',
    'extra_salary'          => 'راتب إضافي',
    'total_salary'          => 'إجمالي الراتب',
    'warning_below_minimum' => '⚠️ لم تكمل {min} الساعات المطلوبة هذا الشهر.',
    'congrats_completed'    => '✅ أحسنت! أكملت ساعات العمل المطلوبة هذا الشهر.',
    'back_to_dashboard'     => 'العودة للوحة التحكم',

];
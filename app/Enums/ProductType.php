<?php

namespace App\Enums;

enum ProductType: string
{
    case WrittenSampleQuestion = 'written_sample_question';
    case ExamRegistration = 'exam_registration';
    case SoftwareService = 'software_service';
    case CourseTuition = 'course_tuition';

    case SimulatedExam = 'simulated_exam';

    public function label(): string
    {
        return match ($this) {
            self::WrittenSampleQuestion => 'نمونه سؤالات کتبی',
            self::ExamRegistration => 'ثبت‌نام آزمون',
            self::SoftwareService => 'خدمات نرم‌افزاری',
            self::CourseTuition => 'شهریه دوره',
            self::SimulatedExam => 'آزمون شبیه‌سازی',
        };
    }
}

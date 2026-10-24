<?php

namespace App\Enums;

enum ProductType: string
{
    case WrittenSampleQuestion = 'written_sample_question';
    case ExamRegistration = 'exam_registration';
    case SoftwareService = 'software_service';
    case CourseTuition = 'course_tuition';
    case SimulatedExam = 'simulated_exam';
}

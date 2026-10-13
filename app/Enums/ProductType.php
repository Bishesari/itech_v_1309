<?php

namespace App\Enums;

enum ProductType: string
{
    case StandardQuestionBank = 'standard_question_bank';
    case ExamRegistration = 'exam_registration';
    case SoftwareService = 'software_service';
    case CourseTuition = 'course_tuition';
}

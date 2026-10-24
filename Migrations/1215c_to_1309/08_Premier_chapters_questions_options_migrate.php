<?php

$host = '127.0.0.1';
$username = 'root';
$password = 'root';

$source = new PDO(
    "mysql:host=$host;dbname=v1215c;charset=utf8mb4",
    $username,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$destination = new PDO(
    "mysql:host=$host;dbname=cgynaeet_1309;charset=utf8mb4",
    $username,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// 1. انتقال فصل‌ها و نگاشت شناسه‌ها
$chapters = $source->query(
    'SELECT * FROM chapters WHERE standard_id = 4 ORDER BY number'
)->fetchAll(PDO::FETCH_ASSOC);

$chapterMap = [];

$insertChapter = $destination->prepare('INSERT INTO chapters (standard_id, number, title, created_at, updated_at) VALUES (8, ?, ?, ?, ?)');

foreach ($chapters as $chapter) {
    $insertChapter->execute([
        $chapter['number'],
        $chapter['title'],
        $chapter['created_at'],
        $chapter['updated_at'],
    ]);
    $chapterMap[$chapter['id']] = $destination->lastInsertId();
}

// 2. انتقال سؤال‌ها و نگاشت شناسه‌ها
$questions = $source->query(
    'SELECT q.*
     FROM questions q
     JOIN chapters c ON c.id = q.chapter_id
     WHERE c.standard_id = 4
     ORDER BY q.id'
)->fetchAll(PDO::FETCH_ASSOC);

$questionMap = [];

$insertQuestion = $destination->prepare(
    'INSERT INTO questions
        (chapter_id, text, explanation, difficulty,
         is_active, is_frequently_final, assigned_by,
         created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

foreach ($questions as $question) {
    $insertQuestion->execute([
        $chapterMap[$question['chapter_id']],
        $question['text'],
        $question['explanation'],
        $question['difficulty'],
        $question['is_active'],
        $question['is_final'],
        '1',  //assigned by user 1
        $question['created_at'],
        $question['updated_at'],
    ]);

    $questionMap[$question['id']] = $destination->lastInsertId();
}

// 3. انتقال گزینه‌ها با شناسه سؤال جدید
$options = $source->query(
    'SELECT o.*
     FROM options o
     JOIN questions q ON q.id = o.question_id
     JOIN chapters c ON c.id = q.chapter_id
     WHERE c.standard_id = 4
     ORDER BY o.id'
)->fetchAll(PDO::FETCH_ASSOC);

$insertOption = $destination->prepare(
    'INSERT INTO options
        (question_id, text, dir, is_correct, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?)'
);

foreach ($options as $option) {
    $insertOption->execute([
        $questionMap[$option['question_id']],
        $option['text'],
        $option['dir'],
        $option['is_correct'],
        $option['created_at'],
        $option['updated_at'],
    ]);
}

echo 'انتقال فصل‌ها، سؤال‌ها و گزینه‌ها با موفقیت انجام شد.';

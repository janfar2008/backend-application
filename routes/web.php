<?php


Route::get('/test-module', function () {
    $data = [
        'project' => [
            'view' => 'Project View',
            'edit' => 'Project Edit',
            'delete' => 'Project Delete',
        ],
        'task' => [
            'view' => 'Task View',
            'edit' => 'Task Edit',
            'delete' => 'Task Delete',
        ],
    ];


    // Return filtered value
    $a = Filter::process('answer.success.item.create.test', $data, 'asdas', 123);

    // Static event/action
    Event::fire('answer.success.item.create.test', $data);

    dd($a);
});


Route::get('/{any?}', function () {
    return view('welcome');
});

Route::get('/storage/images/{filename}', function ($filename) {
    $path = storage_path() . '/app/images/' . $filename;

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('/{any1?}/{any2?}', function () {
    return view('welcome');
});

Route::get('/{any1?}/{any2?}/{any3?}/{any4?}', function () {
    return view('welcome');
});

Route::get('/{any1?}/{any2?}/{any3?}/{any4?}/{any5?}', function () {
    return view('welcome');
});

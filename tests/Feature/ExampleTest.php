<?php

test('returns a successful response', function () {
<<<<<<< HEAD
    $response = $this->get('/');
=======
    $response = $this->get(route('home'));
>>>>>>> 39d8a93ad41414dfcb6cdcc58894db1308285e6a

    $response->assertOk();
});
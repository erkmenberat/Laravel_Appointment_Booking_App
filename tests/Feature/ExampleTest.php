<?php

// Einfacher Smoke-Test: Startseite sollte erfolgreich antworten.
test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

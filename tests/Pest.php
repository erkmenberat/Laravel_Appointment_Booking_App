<?php

/*
|--------------------------------------------------------------------------
| Test-Basisklasse
|--------------------------------------------------------------------------
|
| Definiert, welche Testklasse Pest standardmäßig verwendet.
| Hier können auch globale Traits für ganze Testgruppen aktiviert werden.
|
*/

pest()->extend(Tests\TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Eigene Erwartungen (Custom Expectations)
|--------------------------------------------------------------------------
|
| Erweiterungen für `expect()` können hier zentral registriert werden.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Globale Helferfunktionen
|--------------------------------------------------------------------------
|
| Wiederverwendbare Test-Helfer können hier definiert werden.
|
*/

function something()
{
    // ..
}

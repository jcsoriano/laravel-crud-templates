<?php

use JCSoriano\CrudTemplates\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Fix for Pest v4 compatibility with Laravel's error handling
// This prevents "Test code or tested code removed error handlers" warnings
// and the TypeError in HandleExceptions::flushState()
afterEach(function () {
    restore_error_handler();
    restore_exception_handler();
});

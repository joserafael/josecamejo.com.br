<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurações globais para todos os testes
        $this->withoutVite();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

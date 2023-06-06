<?php

namespace Tests\Unit;

use App\Helpers\CsrfHelper;
use Tests\TestCase;

class CsrfHelperTest extends TestCase
{

    public function test_csrf_token_can_be_added_to_headers() {

        $headers = [
            'Content-Type' => 'application/json'
        ];
        $headersWithCsrf = CsrfHelper::addCsrfTokenToHeaders($headers);

        $this->assertArrayHasKey('X-CSRF-Token', $headersWithCsrf);
    }
}

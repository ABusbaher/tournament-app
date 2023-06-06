<?php

namespace Tests;

use App\Helpers\CsrfHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function postWithCsrfToken($uri, array $data = [], array $headers = []): TestResponse
    {
        $headersWithCsrf = CsrfHelper::addCsrfTokenToHeaders($headers);

        $server = $this->transformHeadersToServerVars($headersWithCsrf);
        $cookies = $this->prepareCookiesForRequest();
        // Make the request with the updated headers
        return $this->call('POST', $uri, $data, $cookies, [], $server);
    }

    protected function signInUser($user = null)
    {
        $user = $user ?: User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
}

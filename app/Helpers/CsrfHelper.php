<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CsrfHelper {
    public static function addCsrfTokenToHeaders($headers)
    {
        Session::start();
        $headers['X-CSRF-Token'] = csrf_token();

        return $headers;
    }
}

<?php

namespace App\Services\Complementarios\Sofia;

use App\Services\Concerns\Complementarios\SofiaHttp\HandlesSofiaHttpLoggingHelpers;
use App\Services\Concerns\Complementarios\SofiaHttp\HandlesSofiaHttpResponseHelpers;
use App\Services\Concerns\Complementarios\SofiaHttp\HandlesSofiaHttpValidateActions;

class SofiaHttpClient
{
    use HandlesSofiaHttpLoggingHelpers;
    use HandlesSofiaHttpResponseHelpers;
    use HandlesSofiaHttpValidateActions;

    private const DEFAULT_TIMEOUT = 90;

    private const HEALTH_CHECK_TIMEOUT = 5;

    private const STATUS_OK = 'ok';

    private const STATUS_ERROR = 'error';

    private const RESPONSE_FIELD_STATUS = 'status';

    private const RESPONSE_FIELD_RESULTADO = 'resultado';

    private const RESPONSE_FIELD_MESSAGE = 'message';

    private const RESPONSE_FIELD_DETAIL = 'detail';

    private string $baseUrl;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.playwright.url', 'https://playwright:3000'), '/');
        $this->timeout = self::DEFAULT_TIMEOUT;
    }
}

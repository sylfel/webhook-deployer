<?php

namespace Tests\Feature;

use App\Http\Middleware\WhiteIpList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class WhiteIpListTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware(): void
    {
        $request = new Request();
        $request->server->add(['REMOTE_ADDR' => '1.1.1.1']);
        $middleWare = new WhiteIpList();
        $this->assertThrows(
            fn () => $middleWare->handle(
                $request,
                fn () => true,
                AccessDeniedHttpException::class
            )
        );
    }
}

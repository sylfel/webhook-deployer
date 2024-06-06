<?php

namespace App\Http\Middleware;

use App\Models\IpRange;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use IPLib\Factory;
use IPLib\Range\Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WhiteIpList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAdresse = Factory::parseAddressString($request->ip());
        if ($ipAdresse->getRangeType() == Type::T_PUBLIC) {
            $isValid = IpRange::where('adresseType', $ipAdresse->getAddressType())
                ->whereRaw('? between rangeFrom and rangeTo', [$ipAdresse->getComparableString()])
                ->count() > 0;
            if (!$isValid) {
                Log::notice('WhiteIpList : ip not in white list {ip}', ['ip' => $ipAdresse->toString()]);
                throw new AccessDeniedHttpException('Ip unauthorized');
            }
        }
        return $next($request);
    }
}

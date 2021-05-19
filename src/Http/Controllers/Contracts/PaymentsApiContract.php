<?php

namespace EscolaLms\Payments\Http\Controllers\Contracts;

use EscolaLms\Payments\Http\Requests\GatewayRequest;
use EscolaLms\Payments\Http\Requests\TransactionRegistrationRequest;
use Illuminate\Http\JsonResponse;

/**
 * SWAGGER_VERSION
 */
interface PaymentsApiContract
{
    /**
     * @OA\Post(
     *     path="/api/payments/gateway",
     *     @OA\RequestBody(
     *         description="Request body varies upon registered provider",
     *         required=true,
     *     ),
     * )
     * @param GatewayRequest $request
     * @return JsonResponse
     */
    public function gateway(GatewayRequest $request): JsonResponse;

    /**
     * @OA\Put(
     *     path="/api/payments/transaction",
     *     @OA\RequestBody(
     *         description="Register a new payment",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TransactionRegistrationRequest")
     *     ),
     * )
     * @param TransactionRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerTransaction(TransactionRegistrationRequest $request): JsonResponse;
}

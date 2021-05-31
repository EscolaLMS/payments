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
     *     @OA\Response(
     *         response=200,
     *         description="Response varies upon registered provider",
     *      ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Transaction had been registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TransactionRegistration")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="one of the parameters has invalid format",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     * @param TransactionRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerTransaction(TransactionRegistrationRequest $request): JsonResponse;
}

<?php

namespace EscolaLms\Payments\Http\Controllers\Contracts;

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
     * @param PageListingRequest $request
     * @return JsonResponse
     */
    public function webhook(PaymentsWebhookRequest $request): JsonResponse;

}

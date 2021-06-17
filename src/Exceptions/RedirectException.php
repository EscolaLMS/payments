<?php

namespace EscolaLms\Payments\Exceptions;

use Omnipay\Common\Message\RedirectResponseInterface;

class RedirectException extends \Exception
{
    private RedirectResponseInterface $redirectResponse;

    public function __construct(RedirectResponseInterface $redirectResponse)
    {
        $this->message = $redirectResponse->getRedirectUrl();
    }

    public function getRedirectResponse(): RedirectResponseInterface
    {
        return $this->redirectResponse;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectResponse->getRedirectUrl();
    }
}

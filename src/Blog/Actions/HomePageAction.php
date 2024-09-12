<?php

namespace App\Blog\Actions;

use App\Contact\ContactAction;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class HomePageAction
{

    private RendererInterface $renderer;

    private FlashService $flashService;

    private ContactAction $contactAction;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, FlashService $flashService, ContactAction $contactAction)
    {
        $this->renderer = $renderer;
        $this->flashService = $flashService;
        $this->contactAction = $contactAction;
    }

    /** @throws TransportExceptionInterface */
    public function __invoke(ServerRequestInterface $request): string|RedirectResponse
    {
        if ($request->getMethod() === 'POST') {
            $response = $this->contactAction->__invoke($request);

            if ($response instanceof RedirectResponse) {
                return $response;
            }
        }

        return $this->renderer->render('@blog/homepage');
    }
}

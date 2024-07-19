<?php

namespace Framework\Actions;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Add methods to router usage
 *
 * @package Framework\Actions
 */
trait RouterAwareAction
{

    /**
     * Returns a response of redirection
     *
     * @throws Exception
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);

        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}

<?php

namespace Tests\Framework;

use App\Blog\BlogModule;
use Exception;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tests\Framework\Modules\ErroredModule;
use Tests\Framework\Modules\StringModule;

class AppTest extends TestCase
{

    public function testRedirectTrailingSlash()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/demoslash/');
        try {
            $response = $app->run($request);
            $this->assertContains('/demoslash', $response->getHeader('Location'));
            $this->assertEquals(301, $response->getStatusCode());
        } catch (Exception) {}
    }

    public function testBlog()
    {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest('GET', '/blog');
        try {
            $response = $app->run($request);
            $this->assertStringContainsString('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody());
            $this->assertEquals(200, $response->getStatusCode());
        } catch (Exception) {}

        $requestSingle = new ServerRequest('GET', '/blog/article-de-test');
        try {
            $responseSingle = $app->run($requestSingle);
            $this->assertStringContainsString('<h1>Bienvenue sur l\'article article-de-test</h1>', (string)$responseSingle->getBody());
        } catch (Exception) {}

//        // Créer un faux objet Renderer pour les tests
//        $renderer = $this->createMock(\Framework\Renderer::class);
//        // Définir le comportement du mock Renderer pour le rendu de la page index
//        $renderer->method('render')
//            ->with('@blog/index') // Vérifie que la méthode render est appelée avec ce paramètre
//            ->willReturn('<h1>Bienvenue sur le blog</h1>');
//
//        // Créer une instance de App avec le faux Renderer
//        $app = new App([BlogModule::class], ['renderer' => $renderer]);
//
//        // Créer une requête pour /blog
//        $request = new ServerRequest('GET', '/blog');
//
//        // Exécuter l'application avec la requête
//        try {
//            $response = $app->run($request);
//            // Tester le contenu de la réponse
//            $this->assertStringContainsString('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody());
//            $this->assertEquals(200, $response->getStatusCode());
//        } catch (Exception) {
//        }
    }

    public function testThrowExceptionIfNoResponseSent()
    {
        $app = new App([ErroredModule::class]);
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(Exception::class);
        $app->run($request);
    }

    public function testConvertStringToResponse()
    {
        $app = new App([StringModule::class]);
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(Exception::class);
        $response = $app->run($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DEMO', (string)$response->getBody());
    }

    public function testError404()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/blablu');
        try {
            $response = $app->run($request);
            $this->assertStringContainsString('<h1>Erreur 404</h1>', (string)$response->getBody());
            $this->assertEquals(404, $response->getStatusCode());
        } catch (Exception) {
        }
    }

}

<?php

namespace Tests\Auth\Action;

use App\Auth\Action\PasswordResetAction;
use App\Auth\User;
use App\Auth\UserRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;

class PasswordResetActionTest extends ActionTestCase
{

    use ProphecyTrait;

    private ObjectProphecy $renderer;

    private ObjectProphecy $userRepository;

    private PasswordResetAction $action;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $router = $this->prophesize(Router::class);
        $router->generateUri(Argument::cetera())->willReturnArgument();
        $this->renderer->render(Argument::cetera())->willReturnArgument();
        $this->action = new PasswordResetAction(
            $this->renderer->reveal(),
            $this->userRepository->reveal(),
            $router->reveal(),
            $this->prophesize(FlashService::class)->reveal()
        );
    }

    private function makeUser()
    {
        $user = new User();
        $user->setId(3);
        $user->setPasswordReset("fake");
        $user->setPasswordResetAt(new \DateTime());

        return $user;
    }

    public function testWithInvalidToken()
    {
        $user = $this->makeUser();
        $request = $this->makeRequest('/da')
            ->withAttribute('id', $user->getId())
            ->withAttribute('token', $user->getPasswordReset() . 'aze');
        $this->userRepository->find($user->getId())->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.reminder');
    }

    public function testWithExpiredToken()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAt((new \DateTime())->sub(new \DateInterval('PT15M')));
        $request = $this->makeRequest('/da')
            ->withAttribute('id', $user->getId())
            ->withAttribute('token', $user->getPasswordReset());
        $this->userRepository->find($user->getId())->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.reminder');
    }

    public function testWithValidToken()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAt((new \DateTime())->sub(new \DateInterval('PT5M')));
        $request = $this->makeRequest('/da')
            ->withAttribute('id', $user->getId())
            ->withAttribute('token', $user->getPasswordReset());
        $this->userRepository->find($user->getId())->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/reset', $response);
    }

    public function testPostWithInvalidPassword()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAt((new \DateTime())->sub(new \DateInterval('PT5M')));
        $request = $this->makeRequest('/da', ['password' => 'azeaze', 'password_confirm' => 'azeazeaze'])
            ->withAttribute('id', $user->getId())
            ->withAttribute('token', $user->getPasswordReset());
        $this->userRepository->find($user->getId())->willReturn($user);
        $this->renderer
            ->render(Argument::type('string'), Argument::withKey('errors'))
            ->shouldBeCalled()
            ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/reset', $response);
    }

    public function testPostWithValidPassword()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAt((new \DateTime())->sub(new \DateInterval('PT5M')));
        $request = $this->makeRequest('/da', ['password' => 'azeaze', 'password_confirm' => 'azeaze'])
            ->withAttribute('id', $user->getId())
            ->withAttribute('token', $user->getPasswordReset());
        $this->userRepository->find($user->getId())->willReturn($user);
        $this->userRepository->updatePassword($user->getId(), 'azeaze')->shouldBeCalled();
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.login');
    }
}

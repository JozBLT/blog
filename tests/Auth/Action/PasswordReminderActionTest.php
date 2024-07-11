<?php

namespace Tests\Auth\Action;

use App\Auth\Action\PasswordReminderAction;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\User;
use App\Auth\UserRepository;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;

class PasswordReminderActionTest extends ActionTestCase
{

    use ProphecyTrait;

    private ObjectProphecy $renderer;

    private ObjectProphecy $userRepository;

    private ObjectProphecy $mailer;

    private PasswordReminderAction $action;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->mailer = $this->prophesize(PasswordResetMailer::class);
        $this->action = new PasswordReminderAction(
            $this->renderer->reveal(),
            $this->userRepository->reveal(),
            $this->mailer->reveal(),
            $this->prophesize(FlashService::class)->reveal()
        );
    }

    public function testInvalidMail()
    {
        $request = $this->makeRequest('/demo', ['email' => 'azeaze']);
        $this->renderer
            ->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
            ->shouldBeCalled()
            ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/reminder', $response);
    }

    public function testNoRecordMail()
    {
        $request = $this->makeRequest('/demo', ['email' => 'john@doe.fr']);
        $this->userRepository->findBy('email', 'john@doe.fr')->willThrow(new NoRecordException());
        $this->renderer
            ->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
            ->shouldBeCalled()
            ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/reminder', $response);
    }

    public function testWithGoodEmail()
    {
        $user = new User();
        $user->id = 3;
        $user->email = 'john@doe.fr';
        $token = "fake";
        $request = $this->makeRequest('/demo', ['email' => $user->email]);
        $this->userRepository->findBy('email', 'john@doe.fr')->willReturn($user);
        $this->userRepository->resetPassword(3)->willReturn($token);
        $this->mailer->send($user->email, [
            'id' => $user->id,
            'token' => $token
        ])->shouldBeCalled();
        $this->renderer->render()->shouldNotBeCalled();
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, '/demo');
    }
}

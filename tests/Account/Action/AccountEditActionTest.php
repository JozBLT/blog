<?php

namespace Tests\Account\Action;

use App\Account\Action\AccountEditAction;
use App\Account\User;
use App\Auth\UserRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;

class AccountEditActionTest extends ActionTestCase
{

    use ProphecyTrait;

    private ObjectProphecy $renderer;

    private AccountEditAction $action;

    private ObjectProphecy $auth;

    private User $user;

    private ObjectProphecy $userRepository;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->user = new User();
        $this->user->id = 3;
        $this->auth = $this->prophesize(Auth::class);
        $this->auth->getUser()->willReturn($this->user);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->action = new AccountEditAction(
            $this->renderer->reveal(),
            $this->auth->reveal(),
            $this->prophesize(FlashService::class)->reveal(),
            $this->userRepository->reveal()
        );
    }

    public function testValid()
    {
        $this->userRepository->update(3, [
            'firstname' => 'John',
            'lastname' => 'Doe'
        ])->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'John',
            'lastname' => 'Doe'
        ]));
        $this->assertRedirect($response, '/demo');
    }

    public function testValidWithPassword()
    {
        $this->userRepository->update(3, Argument::that(function ($params) {
            $this->assertEquals(['firstname', 'lastname', 'password'], array_keys($params));
            return true;
        }))->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'password' => '0000',
            'password_confirm' => '0000'
        ]));
        $this->assertRedirect($response, '/demo');
    }

    public function testInvalid()
    {
        $this->userRepository->update()->shouldNotBeCalled();
        $this->renderer->render('@account/account', Argument::that(function ($params) {
            $this->assertEquals(['password'], array_keys($params['errors']));
            return true;
        }));
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'password' => '0000',
            'password_confirm' => '1111'
        ]));
    }

}
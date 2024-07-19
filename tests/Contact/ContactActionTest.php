<?php

namespace Tests\Contact;

use App\Contact\ContactAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Tests\ActionTestCase;

class ContactActionTest extends ActionTestCase
{

    private ContactAction $action;

    private RendererInterface $renderer;

    private FlashService $flash;

    private Mailer $mailer;

    private string $to = 'demo@demo.fr';

    public function setUp():void
    {
        $this->renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $this->flash = $this->getMockBuilder(FlashService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->action = new ContactAction($this->to, $this->renderer, $this->flash, $this->mailer);
    }

    public function testGet()
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->with('@contact/contact')
            ->willReturn('');
        call_user_func($this->action, $this->makeRequest('/contact'));
    }

    public function testInvalidPost()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jonjon',
            'email' => 'qrevqe',
            'content' => 'sevczecqzecqzecqsz'
        ]);
        $this->renderer->expects($this->once())
            ->method('render')
            ->with(
                '@contact/contact',
                $this->callback(function ($params) {
                    $this->assertArrayHasKey('errors', $params);
                    $this->assertArrayHasKey('email', $params['errors']);

                    return true;
                })
            )
            ->willReturn('');
        $this->flash->expects($this->once())->method('error');
        call_user_func($this->action, $request);
    }

    public function testValidPost()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jonjon',
            'email' => 'demo@local.dev',
            'content' => 'sevcqsz'
        ]);
        $this->flash->expects($this->once())->method('success');
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) {
                $this->assertArrayHasKey($this->to, $email->getTo());
                $this->assertArrayHasKey('demo@local.dev', $email->getFrom());
                $this->assertStringContainsString('texttexttext', $email->toString());
                $this->assertStringContainsString('htmlhtmlhtml', $email->toString());

                return true;
            }));
        $this->renderer->expects($this->any())
            ->method('render')
            ->willReturn('texttexttext', 'htmlhtmlhtml');
        $response = call_user_func($this->action, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

}

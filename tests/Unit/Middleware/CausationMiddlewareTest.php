<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Simensen\SymfonyMessageTracingBundle\Middleware\CausationMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CausationTracedEnvelopeManager;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CausationMiddlewareTest extends TestCase
{
    private CausationMiddleware $middleware;
    private CausationTracedEnvelopeManager $envelopeManager;

    protected function setUp(): void
    {
        $this->envelopeManager = $this->createMock(CausationTracedEnvelopeManager::class);
        $this->middleware = new CausationMiddleware($this->envelopeManager);
    }

    public function testImplementsMiddlewareInterface(): void
    {
        $this->assertInstanceOf(MiddlewareInterface::class, $this->middleware);
    }

    public function testHandleCallsPushOnEnvelopeManager(): void
    {
        $originalEnvelope = new Envelope(new \stdClass());
        $pushedEnvelope = new Envelope(new \stdClass());
        $processedEnvelope = new Envelope(new \stdClass());
        $finalEnvelope = new Envelope(new \stdClass());

        $this->envelopeManager
            ->expects($this->once())
            ->method('push')
            ->with($originalEnvelope)
            ->willReturn($pushedEnvelope);

        $this->envelopeManager
            ->expects($this->once())
            ->method('pop')
            ->with($processedEnvelope)
            ->willReturn($finalEnvelope);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($pushedEnvelope)
            ->willReturn($processedEnvelope);

        $stack = $this->createMock(StackInterface::class);
        $stack
            ->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $result = $this->middleware->handle($originalEnvelope, $stack);

        $this->assertSame($finalEnvelope, $result);
    }

    public function testHandleCallsPopOnEnvelopeManager(): void
    {
        $envelope = new Envelope(new \stdClass());
        $pushedEnvelope = new Envelope(new \stdClass());
        $processedEnvelope = new Envelope(new \stdClass());
        $poppedEnvelope = new Envelope(new \stdClass());

        $this->envelopeManager
            ->method('push')
            ->willReturn($pushedEnvelope);

        $this->envelopeManager
            ->expects($this->once())
            ->method('pop')
            ->with($processedEnvelope)
            ->willReturn($poppedEnvelope);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware
            ->method('handle')
            ->willReturn($processedEnvelope);

        $stack = $this->createMock(StackInterface::class);
        $stack
            ->method('next')
            ->willReturn($nextMiddleware);

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertSame($poppedEnvelope, $result);
    }

    public function testHandlePreservesEnvelopeChain(): void
    {
        $originalMessage = new \stdClass();
        $originalEnvelope = new Envelope($originalMessage);
        $pushedEnvelope = new Envelope($originalMessage);
        $processedEnvelope = new Envelope($originalMessage);
        $finalEnvelope = new Envelope($originalMessage);

        $this->envelopeManager
            ->method('push')
            ->with($originalEnvelope)
            ->willReturn($pushedEnvelope);

        $this->envelopeManager
            ->method('pop')
            ->with($processedEnvelope)
            ->willReturn($finalEnvelope);

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware
            ->method('handle')
            ->with($pushedEnvelope)
            ->willReturn($processedEnvelope);

        $stack = $this->createMock(StackInterface::class);
        $stack
            ->method('next')
            ->willReturn($nextMiddleware);

        $result = $this->middleware->handle($originalEnvelope, $stack);

        $this->assertSame($finalEnvelope, $result);
    }

    public function testHandleWithException(): void
    {
        $envelope = new Envelope(new \stdClass());
        $pushedEnvelope = new Envelope(new \stdClass());
        $exception = new \RuntimeException('Test exception');

        $this->envelopeManager
            ->method('push')
            ->willReturn($pushedEnvelope);

        // Should NOT call pop when exception occurs
        $this->envelopeManager
            ->expects($this->never())
            ->method('pop');

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware
            ->method('handle')
            ->willThrowException($exception);

        $stack = $this->createMock(StackInterface::class);
        $stack
            ->method('next')
            ->willReturn($nextMiddleware);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test exception');

        $this->middleware->handle($envelope, $stack);
    }
}
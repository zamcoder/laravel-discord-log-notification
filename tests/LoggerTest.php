<?php

namespace ZamCoder\DiscordLogger\Tests;

use DateTime;
use InvalidArgumentException;
use ZamCoder\DiscordLogger\Contracts\DiscordWebHook;
use ZamCoder\DiscordLogger\Logger;
use ZamCoder\DiscordLogger\Tests\Support\FakeDiscordWebHook;
use RuntimeException;

class LoggerTest extends TestCase
{

    /** @var \ZamCoder\DiscordLogger\Tests\Support\FakeDiscordWebHook */
    private $discordFake;

    /** @var \ZamCoder\DiscordLogger\Logger */
    private $logger;

    /** @var \Monolog\Logger */
    private $monolog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discordFake = new FakeDiscordWebHook('http://example.com');
        $this->app->bind(DiscordWebHook::class, function () {
            return $this->discordFake;
        });

        $this->logger = $this->app->make(Logger::class);
        $this->monolog = ($this->logger)(['level' => 'INFO', 'url' => 'http://example.com']);
    }

    /** @test */
    public function throws_exception_if_url_missing_from_channel_configuration()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must set the `url` key in your discord channel configuration');

        ($this->logger)([]);
    }

    /** @test */
    public function log_is_sent_to_discord()
    {
        $this->monolog->warning('This is a test');
        $this->discordFake->assertSendCount(1);
    }
}

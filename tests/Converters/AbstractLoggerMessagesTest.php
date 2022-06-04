<?php

namespace ZamCoder\DiscordLogger\Tests\Converters;

use DateTime;
use ZamCoder\DiscordLogger\Contracts\RecordToMessage;
use ZamCoder\DiscordLogger\Tests\Support\MessageAssertions;
use ZamCoder\DiscordLogger\Tests\TestCase;
use Monolog\Logger;
use Throwable;
use function json_encode;

abstract class AbstractLoggerMessagesTest extends TestCase
{
    /** @var \ZamCoder\DiscordLogger\Contracts\RecordToMessage */
    protected $converter;

    /** @var \Illuminate\Contracts\Config\Repository */
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = $this->app['config'];
        $this->converter = $this->getConverter();
    }

    abstract protected function getConverter(): RecordToMessage;

    /** @test */
    public function message_is_sent_with_expected_from_fields()
    {
        $this->config->set('discord-logger.from.name', 'John');
        $this->config->set('discord-logger.from.avatar_url', 'http://example.com/avatar.png');

        $message = $this->warning('This is a test')[0];

        MessageAssertions::assertMessagePartialMatch([
            'username'   => 'John',
            'avatar_url' => 'http://example.com/avatar.png',
        ], $message);
    }

    protected function warning(string $message, array $context = [], array $extras = []): array
    {
        return $this->converter->buildMessages(
            $this->fakeRecord(Logger::WARNING, $message, $context, $extras)
        );
    }

    protected function exception(string $message, Throwable $exception): array
    {
        return $this->converter->buildMessages(
            $this->fakeRecord(Logger::CRITICAL, $message, ['exception' => $exception])
        );
    }

    protected function fakeRecord(string $level, string $message, array $context = [], array $extra = []): array
    {
        $timestamp = DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 12:13:14');
        $serializedContext = json_encode($context);
        $serializedExtras = json_encode($context);
        $levelName = Logger::getLevelName($level);
        $formatted = "[2000-01-01 12:13:14] Laravel.$levelName: $message $serializedContext $serializedExtras\n";

        return [
            'message'    => $message,
            'level'      => $level,
            'channel'    => 'Laravel',
            'level_name' => $levelName,
            'datetime'   => $timestamp,
            'formatted'  => $formatted,
            'extra'      => $extra,
            'context'    => $context,
        ];
    }
}

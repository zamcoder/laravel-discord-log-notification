<?php

namespace ZamCoder\DiscordLogger\Tests\Discord;

use ZamCoder\DiscordLogger\Discord\Embed;
use ZamCoder\DiscordLogger\Discord\Message;
use ZamCoder\DiscordLogger\Tests\Support\FakeDiscordWebHook;
use ZamCoder\DiscordLogger\Tests\TestCase;

class FakeDiscordWebHookTest extends TestCase
{
    /** @var \ZamCoder\DiscordLogger\Tests\Support\FakeDiscordWebHook */
    private $discord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discord = new FakeDiscordWebHook('http://example.com');
    }

    /** @test */
    public function asserts_last_message_sent_is_same_as_expected()
    {
        $this->discord->send(Message::make()->content('this is a test'));

        $this->discord->assertLastMessage(Message::make()->content('this is a test'));
    }

    /** @test */
    public function asserts_last_message_sent_matches_given_array()
    {
        $this->discord->send(Message::make()
            ->content('this is a test')
            ->from('John')
            ->tts()
            ->embed(Embed::make()->author('Joe')->description('This is an embed'))
            ->embed(Embed::make()->author('Bob')->description('This is another embed'))
            ->embed(Embed::make()->author('Bob')->description('This is a third embed')->field('foo', '1')
                ->field('bar', '2')));

        $this->discord->assertLastMessageMatches([
            'content' => 'this is a test',
            'tts'     => 'true',
            'embeds'  => [
                0 => ['description' => 'This is an embed'],
                2 => [
                    'description' => 'This is a third embed',
                    'fields'      => [
                        ['name' => 'foo'],
                        ['name' => 'bar'],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function asserts_that_no_message_has_been_sent()
    {
        $this->discord->assertNothingSent();
    }

    /** @test */
    public function asserts_that_N_messages_have_been_sent()
    {
        $this->discord->send(Message::make());
        $this->discord->send(Message::make());

        $this->discord->assertSendCount(2);
    }
}

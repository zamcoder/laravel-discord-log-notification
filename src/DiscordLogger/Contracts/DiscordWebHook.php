<?php

namespace ZamCoder\DiscordLogger\Contracts;

use ZamCoder\DiscordLogger\Discord\Message;

interface DiscordWebHook
{
    public const MAX_CONTENT_LENGTH = 2000;

    public function send(Message $message): void;

    public function getUrl(): string;
}

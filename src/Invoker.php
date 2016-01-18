<?php

namespace SlackBot;

use Slack\Message\Message;
use SlackBot\Command\CommandInterface;

class Invoker
{
    const COMMAND_HELP = 'help';

    protected static $nativeCommands = [
        self::COMMAND_HELP
    ];

    /**
     * @var CommandInterface[]
     */
    protected $commandList = [];

    public function __construct()
    {

    }

    /**
     * @param CommandInterface $command
     * @return $this
     */
    public function addCommand(CommandInterface $command)
    {
        if ($this->isCommandExists($command->getName())) {
            throw new \RuntimeException(
                sprintf(
                    "Command '%s' is already exists.",
                    $command->getName()
                )
            );
        }

        if ($this->checkName($command->getName())) {
            $this->commandList[$command->getName()] = $command;
        } else {
            throw new \RuntimeException("Command name can not be empty.");
        }

        return $this;
    }

    /**
     * @param Message $message
     * @return string
     */
    public function execute(Message $message)
    {
        $commandName = explode(' ', $message->getText())[0];

        if ($this->isCommandNative($commandName)) {
            return $this->processNativeCommands($commandName);
        }

        if ($this->isCommandExists($commandName)) {
            $command = $this->commandList[$commandName];
            return $command->execute($message);
        } else {
            return sprintf(
                "Command '%s' is not exists.",
                $commandName
            );
        }
    }

    /**
     * @param $commandName
     * @return bool
     */
    protected function checkName($commandName)
    {
        return !empty($commandName);
    }

    /**
     * @param $commandName
     * @return bool
     */
    protected function isCommandExists($commandName)
    {
        return isset($this->commandList[$commandName]);
    }

    /**
     * @param $commandName
     * @return bool
     */
    protected function isCommandNative($commandName)
    {
        return in_array($commandName, self::$nativeCommands);
    }

    /**
     * @param string $commandName
     * @return string
     */
    protected function processNativeCommands($commandName)
    {
        switch($commandName)
        {
            case self::COMMAND_HELP:
                return $this->getCommandsHelp();
        }
    }

    /**
     * @return string
     */
    protected function getCommandsHelp()
    {
        $cList = [
            "Welcome to Invoker.Slackbot!:spock-hand:\n",
            "Usage:",
            "\tcommand [arguments]\n",
            "Available commands:"
        ];

        foreach ($this->commandList as $command) {
            $cList[] = sprintf(
                "\t%-17s%s",
                $command->getName(),
                $command->getHelp()
            );
        }

        return implode("\n", $cList);
    }
}

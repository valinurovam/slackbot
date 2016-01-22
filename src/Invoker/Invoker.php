<?php

namespace SlackBot\Invoker;

use Slack\Message\Message;
use SlackBot\Command;
use SlackBot\Command\CommandInterface;
use SlackBot\Command\PeriodicCommandInterface;

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

    /**
     * @var PeriodicCommandInterface[]
     */
    protected $periodicCommandList = [];

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
     * @param PeriodicCommandInterface $command
     * @return $this
     */
    public function addPeriodicCommand(PeriodicCommandInterface $command)
    {
        if ($this->isCommandExists($command->getName(), true)) {
            throw new \RuntimeException(
                sprintf(
                    "Command '%s' is already exists.",
                    $command->getName()
                )
            );
        }

        if ($this->checkName($command->getName())
            && $this->checkInterval($command->getInterval())
        ) {
            $this->periodicCommandList[$command->getName()] = $command;
        } else {
            throw new \RuntimeException("Command name or interval can not be empty.");
        }

        return $this;
    }

    /**
     * @return Command\PeriodicCommandInterface[]
     */
    public function getPeriodicCommands()
    {
        return $this->periodicCommandList;
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

    protected function checkInterval($interval)
    {
        return (int) $interval > 0;
    }

    /**
     * @param $commandName
     * @param bool $periodic
     * @return bool
     */
    protected function isCommandExists($commandName, $periodic = false)
    {
        if ($periodic) {
            return isset($this->periodicCommandList[$commandName]);
        } else {
            return isset($this->commandList[$commandName]);
        }
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
            "Available commands:",
        ];

        foreach ($this->commandList as $command) {
            $cList[] = sprintf(
                "\t%-17s%s",
                $command->getName(),
                $command->getHelp()
            );
        }

        $cList[] = sprintf(
            "\t%-17s%s",
            'help',
            'Show this help message'
        );

        return implode("\n", $cList);
    }
}

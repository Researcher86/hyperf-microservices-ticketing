<?php

declare(strict_types=1);

namespace Auth\Command;

use Auth\Model\User;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

#[Command]
class UsersFixtureCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('users:fixture');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Users fixture Command');
    }

    public function handle()
    {
        $this->line('Load users.', 'info');

        User::truncate();

        User::create([
            'email' => 'admin@admin.com',
            'password' => password_hash('admin', PASSWORD_DEFAULT)
        ]);
    }
}

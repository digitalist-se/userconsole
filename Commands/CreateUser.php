<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\UserConsole\Commands;

use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Piwik\Plugins\UsersManager\API as APIUsersManager;

/**
 * This class lets you define a new command. To read more about commands have a look at our Piwik Console guide on
 * http://developer.piwik.org/guides/piwik-on-the-command-line
 *
 * As Piwik Console is based on the Symfony Console you might also want to have a look at
 * http://symfony.com/doc/current/components/console/index.html
 */
class CreateUser extends ConsoleCommand
{
    /**
     * This methods allows you to configure your command. Here you can define the name and description of your command
     * as well as all options and arguments you expect when executing it.
     */
    protected function configure()
    {
        $this->setName('user:create');
        $this->setDescription('Create user');
        $this->addOption(
            'super',
            null,
            InputOption::VALUE_NONE,
            'Create the user as a super user'
        );
        $this->addOption(
            'login',
            null,
            InputOption::VALUE_OPTIONAL,
            'User login name',
            null
        );
        $this->addOption(
            'email',
            null,
            InputOption::VALUE_OPTIONAL,
            'User email',
            null
        );
        $this->addOption(
            'password',
            null,
            InputOption::VALUE_OPTIONAL,
            'User password',
            null
        );
    }

    /**
     * Create an user, with option to create super user.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $super = $input->getOption('super') ? true : false;
        $login = $input->getOption('login');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $api = APIUsersManager::getInstance();

        if (!$api->userExists($login) && !$api->userEmailExists($email)) {
            $api->addUser(
                $login,
                $password,
                $email
            );
            if ($super === true) {
                $api->setSuperUserAccess($login, true);
            }
        } else {
            $output->writeln("<error>User with login name $login or/and email $email already exists.</error>");
            exit;
        }
        $output->writeln("<info>User $login created</info>");
    }
}

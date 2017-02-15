<?php

namespace Caramia\AdminBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Caramia\AdminBundle\Entity\User;

class CreateUserCommand extends ContainerAwareCommand
{
    protected static $defaultEmail = 'admin@caramia.fr';

    public function configure()
    {
        $this
            ->setName('caramia-admin:create-user')
            ->setDescription('Creates a new back-office user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            '<bg=cyan>                                                              </>',
            '<bg=cyan>                                                              </>',
            '<bg=cyan>    Welcome to the Caramia Admin Bundle User Creator 3000!    </>',
            '<bg=cyan>                                                              </>',
            '<bg=cyan>                                                              </>',
            ''
        ]);

        $helper = $this->getHelper('question');

        $emailQuestion = new Question(
            sprintf('What is the user e-mail address? (<fg=green>%s</>) ', static::$defaultEmail),
            static::$defaultEmail
        );

        $email = $helper->ask($input, $output, $emailQuestion);

        while (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $output->writeln([
                '',
                '... this is not an e-mail... Please enter an e-mail so we can pass to something else...',
                '',
            ]);

            $email = $helper->ask($input, $output, $emailQuestion);
        }

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $exists = $manager->getRepository(User::class)->findOneByEmail($email);

        while ($exists !== null) {
            $output->writeln([
                '',
                'You know a user with this e-mail already exists, right?',
                '',
            ]);

            $email = $helper->ask($input, $output, $emailQuestion);
            $exists = $manager->getRepository(User::class)->findOneByEmail($email);
        }

        if ($email === static::$defaultEmail) {
            $answer = '... Yeah, the default e-mail address, really inventive...';
        }
        else {
            $answer = '... that\'s original, but okay...';
        }

        $output->writeln([
            '',
            $answer,
            '',
        ]);

        $passwordQuestion = new Question('What is the user password? ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $passwordQuestion);

        while ($password === null) {
            $output->writeln([
                '',
                'Yeah, no, it can\'t be an empty string...',
                '',
            ]);
            $passwordQuestion = new Question('What is the user password? ');
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $passwordQuestion);
        }

        if ($password === 'caramia') {
            $answer = '... Yeah please think about changing it in production, okay?';
        }
        else {
            $answer = 'LOL yeah, super hard to guess...';
        }

        $output->writeln([
            '',
            $answer . ' Anyway, let\'s do this...',
            '',
        ]);


        $user = new User();
        $user->setEmail($email);
        $user->setPlainPassword(trim($password));
        $user->setIsActive(true);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_SONATA_ADMIN']);

        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $user->encodePassword($encoder);

        $manager->persist($user);
        $manager->flush();

        sleep(1);

        $output->writeln([
            'Okay it\'s done, now go back to work.',
            '',
        ]);
    }
}

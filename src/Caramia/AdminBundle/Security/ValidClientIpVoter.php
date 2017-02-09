<?php

namespace Caramia\AdminBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\IpUtils;

use Caramia\AdminBundle\Entity\User;
use Caramia\AdminBundle\Entity\IpAdmin;

class ValidClientIpVoter extends AuthenticatedVoter
{
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $request = $this->container->get('request');
        $user = $token->getUser();
        // vote on instances of our User class
        if ($user instanceof User && ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')) && $this->isServiceEnabled()) {
            $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: false;

            $orm = $this->container->get('doctrine')->getManager();
            $allowedIps = $orm->getRepository('Caramia\AdminBundle\Entity\IpAdmin')->findAll();

            $this->logger->debug(sprintf('ValidClientIpVoter: Validating allowed IPs for user #%d', $user->getId()));

            $allowedIpsAsArray = array_map(function($adminIp){
                return $adminIp->getIp();
            }, $allowedIps);

            // Add fixed allowed IPs
            if ($this->container->hasParameter('allowed_ips')) {
                $allowedIpsAsArray = array_merge($allowedIpsAsArray, $this->container->getParameter('allowed_ips'));
            }

            if (!IpUtils::checkIp($user_ip, $allowedIpsAsArray)) {
                $this->logger->notice(sprintf('ValidClientIpVoter: Invalid client IP for user #%d', $user->getId()));
                return VoterInterface::ACCESS_DENIED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    protected function isServiceEnabled()
    {
        return $this->container->hasParameter('enabled_check_ip') && $this->container->getParameter('enabled_check_ip');
    }

}

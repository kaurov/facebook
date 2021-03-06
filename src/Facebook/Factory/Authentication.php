<?php
namespace Facebook\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Facebook\FacebookCanvasLoginHelper;
use Facebook\FacebookSession;
use Facebook\FacebookRequestException;
use LosLog\Log\StaticLogger;

class Authentication extends AbstractFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Facebook\FacebookSession
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $config = $config[self::CONFIG_KEY];

        if (is_null($config))
            throw new \RuntimeException(_('Config could not be found!'));

        FacebookSession::setDefaultApplication($config['appId'], $config['secret']);

        try {
            $helper = new FacebookCanvasLoginHelper();
            $session = $helper->getSession();

            $this->setAccessToken($session->getAccessToken());
        } catch (FacebookRequestException $ex) {
            StaticLogger::save($ex->__toString(), 'facebook.log');
        } catch (\Exception $ex) {
            StaticLogger::save($ex->__toString(), 'facebook.log');
        }

        return $session;
    }
}

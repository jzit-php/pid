<?php

declare(strict_types=1);

namespace JzIT\Pid;

use Http\Factory\Diactoros\ResponseFactory;
use Di\Container;
use JzIT\Container\ServiceProvider\AbstractServiceProvider;
use JzIT\Container\ServiceProvider\ServiceProviderInterface;
use JzIT\Pid\Business\PidFacade;
use JzIT\Pid\Business\PidFacadeInterface;
use JzIT\Pid\Persistence\PidRepository;
use JzIT\Pid\Persistence\PidRepositoryInterface;

/**
 * Class PidServiceProvider
 *
 * @package JzIT\Pid
 *
 * @method \JzIT\Pid\PidFactory getFactory(?string $className = null)
 */
class PidServiceProvider extends AbstractServiceProvider
{
    /**
     * @param \Di\Container $container
     */
    public function register(Container $container): void
    {
        $this->addFacade($container);

    }

    /**
     * @param \Di\Container $container
     *
     * @return \JzIT\Container\ServiceProvider\ServiceProviderInterface
     */
    protected function addFacade(Container $container): ServiceProviderInterface
    {
        $self = $this;
        $container->set(PidConstants::FACADE, function () use ($self) {
            return $self->getFactory()->createFacade();
        });

        return $this;
    }
}

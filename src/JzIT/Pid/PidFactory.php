<?php

namespace JzIT\Pid;

use JzIT\Db\DbConstants;
use JzIT\Kernel\AbstractFactory;
use JzIT\Pid\Business\Model\Writer\PidStatWriter;
use JzIT\Pid\Business\Model\Writer\PidStatWriterInterface;
use JzIT\Pid\Business\PidFacade;
use JzIT\Pid\Business\PidFacadeInterface;
use JzIT\Pid\Persistence\PidEntityManager;
use JzIT\Pid\Persistence\PidEntityManagerInterface;
use JzIT\Pid\Persistence\PidRepository;
use JzIT\Pid\Persistence\PidRepositoryInterface;
use JzIT\Serializer\SerializerConstants;

/**
 * Class PidFactory
 *
 * @package JzIT\Pid
 * @method \JzIT\Pid\PidConfig getConfig()
 */
class PidFactory extends AbstractFactory
{
    /**
     * @return \JzIT\Pid\Business\PidFacadeInterface
     */
    public function createFacade(): PidFacadeInterface
    {
        //ToDo: replace with facade resolve if available
        return new PidFacade(
            $this->createPidStatWriter()
        );
    }

    /**
     * @return \JzIT\Pid\Persistence\PidEntityManagerInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function createEntityManager(): PidEntityManagerInterface
    {
        return new PidEntityManager(
            $this->container->get(DbConstants::ENTITY_MANAGER),
            $this->createRepository(),
            $this->container->get(SerializerConstants::CONTAINER_SERVICE_NAME)
        );
    }

    /**
     * @return \JzIT\Pid\Persistence\PidRepositoryInterface
     */
    protected function createRepository(): PidRepositoryInterface
    {
        return new PidRepository(
            $this->container->get(DbConstants::ENTITY_MANAGER),
            $this->container->get(SerializerConstants::CONTAINER_SERVICE_NAME)
        );
    }

    /**
     * @return \JzIT\Pid\Business\Model\Writer\PidStatWriterInterface
     */
    protected function createPidStatWriter(): PidStatWriterInterface
    {
        return new PidStatWriter(
            $this->createRepository(),
            $this->createEntityManager()
        );
    }
}

<?php

namespace JzIT\Pid\Persistence;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Generated\Transfer\Pid\PidMachineTransfer;
use Generated\Transfer\Pid\PidStatsTransfer;
use Generated\Transfer\Pid\PidUserTransfer;
use JzIT\Pid\Exception\MachineAlreadyExistsException;
use JzIT\Pid\Exception\MissingDataException;
use JzIT\Pid\Exception\UserAlreadyExistsException;
use JzIT\Serializer\Wrapper\SerializerInterface;
use PidUser;

class PidEntityManager implements PidEntityManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \JzIT\Serializer\Wrapper\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \JzIT\Pid\Persistence\PidRepositoryInterface
     */
    protected $repository;

    /**
     * PidEntityManager constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \JzIT\Pid\Persistence\PidRepositoryInterface $repository
     * @param \JzIT\Serializer\Wrapper\SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, PidRepositoryInterface $repository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * @param \Generated\Transfer\Pid\PidUserTransfer $pidUserTransfer
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     */
    public function createPidUser(PidUserTransfer $pidUserTransfer): ?PidUserTransfer
    {
        $userHash = $pidUserTransfer->getHash();
        if (empty($userHash) === true) {
            throw new MissingDataException(sprintf('Missing "hash" for %s', get_class($pidUserTransfer)));
        }
        $pidMachineTransfer = $pidUserTransfer->getMachine();
        if ($pidMachineTransfer === null) {
            throw new MissingDataException(sprintf('Missing "machine" for %s', get_class($pidUserTransfer)));
        }

        $user = $this->repository->findPidUserByHash($userHash);

        if ($user !== null) {
            throw new UserAlreadyExistsException(sprintf('User with hash "%s" already exists!', $userHash));
        }

        $pidUser = new PidUser();
        $pidUser->setHash($userHash);
        $pidUser->setName($pidUserTransfer->getName());
        $pidUser->setMachine($this->findOrCreateMachine($pidMachineTransfer));

        $this->entityManager->persist($pidUser);
        $this->entityManager->flush();

        //ToDo: create and use mapper instead
        return $this->repository->findPidUserById($pidUser->getId());
    }

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    public function createPidMachine(PidMachineTransfer $pidMachineTransfer): PidMachineTransfer
    {
        $machine = $this->findMachineByNameAndModel($pidMachineTransfer);

        if ($machine !== null) {
            throw new MachineAlreadyExistsException(sprintf('Machine with name "%s" and model "%s" already exists!', $pidMachineTransfer->getName(), $pidMachineTransfer->getModel()));
        }

        $pidMachine = new \PidMachine();
        $pidMachine->setName($pidMachineTransfer->getName());
        $pidMachine->setModel($pidMachineTransfer->getModel());

        $this->entityManager->persist($pidMachine);
        $this->entityManager->flush();

        return $pidMachineTransfer->setId($pidMachine->getId());
    }

    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    public function createPidStat(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer
    {
        $pidMachineTransfer = $pidStatsTransfer->getMachine();
        $pidUserTransfer = $pidStatsTransfer->getUser();

        if ($pidMachineTransfer === null || $pidUserTransfer === null){
            throw new MissingDataException(sprintf('User and machine are required!'));
        }

        $pidStat = new \PidStat();
        $pidStat
            ->setUser($this->findOrCreateUser($pidUserTransfer))
            ->setMachine($this->findOrCreateMachine($pidMachineTransfer))
            ->setTemp($pidStatsTransfer->getTemp())
            ->setSollTemp($pidStatsTransfer->getSollTemp())
            ->setOutput($pidStatsTransfer->getOutput())
            ->setKi($pidStatsTransfer->getKi())
            ->setKp($pidStatsTransfer->getKp())
            ->setKd($pidStatsTransfer->getKd());

        $this->entityManager->persist($pidStat);
        $this->entityManager->flush();

        //ToDo: create and use entity to transfer mapper
        return $this->repository->findStatById($pidStat->getId());
    }

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \PidMachine|null
     * @throws \JzIT\Pid\Exception\MissingDataException
     */
    protected function findMachineByNameAndModel(PidMachineTransfer $pidMachineTransfer): ?\PidMachine
    {
        if (empty($pidMachineTransfer->getName()) === true || empty($pidMachineTransfer->getModel()) === true) {
            throw new MissingDataException(
                sprintf('Name (%s) and model (%s) must not be empty!', $pidMachineTransfer->getName(), $pidMachineTransfer->getModel())
            );
        }
        $repo = $this->entityManager->getRepository('PidMachine');

        $machine = $repo->findOneBy([
            'name' => $pidMachineTransfer->getName(),
            'model' => $pidMachineTransfer->getModel(),
        ]);

        if ($machine instanceof \PidMachine) {
            return $machine;
        }

        return null;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \PidMachine
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    protected function findOrCreateMachine(PidMachineTransfer $pidMachineTransfer): \PidMachine
    {
        $machine = $this->findMachineByNameAndModel($pidMachineTransfer);
        if ($machine === null) {
            $this->createPidMachine($pidMachineTransfer);
            $machine = $this->findMachineByNameAndModel($pidMachineTransfer);
        }
        return $machine;
    }


    /**
     * @param \Generated\Transfer\Pid\PidUserTransfer $pidUserTransfer
     *
     * @return \PidUser
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    protected function findOrCreateUser(PidUserTransfer $pidUserTransfer): PidUser
    {
        $user = $this->findPidUserEntityByHash($pidUserTransfer);
        if ($user === null) {
            $this->createPidUser($pidUserTransfer);
            $user = $this->findPidUserEntityByHash($pidUserTransfer);
        }
        return $user;
    }

    /**
     * @param \Generated\Transfer\Pid\PidUserTransfer $pidUserTransfer
     *
     * @return PidUser|null
     * @throws \JzIT\Pid\Exception\MissingDataException
     */
    protected function findPidUserEntityByHash(PidUserTransfer $pidUserTransfer): ?PidUser
    {
        $userHash = $pidUserTransfer->getHash();
        if ($userHash === null || $userHash === '') {
            throw new MissingDataException(sprintf('The hash in transfer "%s" is required', get_class($pidUserTransfer)));
        }

        /** @var PidUser $user */
        $user = $this->entityManager->getRepository('PidUser')->findOneBy(['hash' => $userHash]);
        return $user;
}
}

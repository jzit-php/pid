<?php

namespace JzIT\Pid\Persistence;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Generated\Transfer\Pid\PidMachineTransfer;
use Generated\Transfer\Pid\PidStatsTransfer;
use Generated\Transfer\Pid\PidUserTransfer;
use JzIT\Serializer\Wrapper\SerializerInterface;

class PidRepository implements PidRepositoryInterface
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
     * PidRepository constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \JzIT\Serializer\Wrapper\SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @param int $idPidUser
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     */
    public function findPidUserById(int $idPidUser): ?PidUserTransfer
    {
        $user = $this->entityManager->find('PidUser', $idPidUser);

        if ($user === null) {
            return null;
        }

        /** @var PidUserTransfer $userTransfer */
        $userTransfer = $this->serializer->deserialize(
            $this->serializer->serialize($user, 'json'),
            PidUserTransfer::class,
            'json'
        );

        return $userTransfer;
    }

    /**
     * @param string $hash
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPidUserByHash(string $hash): ?PidUserTransfer
    {
        $user = $this->entityManager->getRepository('PidUser')->findOneBy(['hash' => $hash]);
        if ($user === null) {
            return null;
        }

        /** @var PidUserTransfer $machineTransfer */
        $userTransfer = $this->serializer->deserialize(
            $this->serializer->serialize($user, 'json'),
            PidUserTransfer::class,
            'json'
        );

        return $userTransfer;
    }

    /**
     * @param int $idPidMachine
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer|null
     */
    public function findPidMachineById(int $idPidMachine): ?PidMachineTransfer
    {
        $machine = $this->entityManager->find('PidMachine', $idPidMachine);

        if ($machine === null) {
            return null;
        }

        /** @var PidMachineTransfer $machineTransfer */
        $machineTransfer = $this->serializer->deserialize(
            $this->serializer->serialize($machine, 'json'),
            PidMachineTransfer::class,
            'json'
        );

        return $machineTransfer;
    }

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMachine(PidMachineTransfer $pidMachineTransfer): ?PidMachineTransfer
    {
        $query = $this->getQueryBuilder()
            ->select('id')
            ->from('PidMachine', 'm')
            ->where(sprintf('m.name = %s', $pidMachineTransfer->getName()))
            ->andWhere(sprintf('m.model = %s', $pidMachineTransfer->getModel()))
            ->getQuery();

        $id = $query->getSingleResult();

        if ($id === null) {
            return null;
        }

        return $this->findPidMachineById($id);
    }

    /**
     * @param int $idStat
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer|null
     */
    public function findStatById(int $idStat): ?PidStatsTransfer
    {
        $repo = $this->entityManager->getRepository('PidStat');

        /** @var \PidStat $result */
        $result = $repo->findOneBy(['id' => $idStat]);

        if ($result === null){
            return null;
        }

        $stat = $this->serializer->deserialize(
            $this->serializer->serialize($result, 'json'),
            PidStatsTransfer::class,
            'json'
        );

        if ($stat instanceof PidStatsTransfer){
            return $stat;
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
}

<?php

namespace JzIT\Pid\Persistence;

use Generated\Transfer\Pid\PidMachineTransfer;
use Generated\Transfer\Pid\PidStatsTransfer;
use Generated\Transfer\Pid\PidUserTransfer;

interface PidRepositoryInterface
{
    /**
     * @param int $idPidUser
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     */
    public function findPidUserById(int $idPidUser): ?PidUserTransfer;

    /**
     * @param string $hash
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPidUserByHash(string $hash): ?PidUserTransfer;

    /**
     * @param int $idPidMachine
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer|null
     */
    public function findPidMachineById(int $idPidMachine): ?PidMachineTransfer;

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMachine(PidMachineTransfer $pidMachineTransfer): ?PidMachineTransfer;

    /**
     * @param int $idStat
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer|null
     */
    public function findStatById(int $idStat): ?PidStatsTransfer;
}

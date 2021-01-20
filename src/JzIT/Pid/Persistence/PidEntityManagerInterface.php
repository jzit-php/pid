<?php

namespace JzIT\Pid\Persistence;


use Generated\Transfer\Pid\PidMachineTransfer;
use Generated\Transfer\Pid\PidStatsTransfer;
use Generated\Transfer\Pid\PidUserTransfer;

interface PidEntityManagerInterface
{
    /**
     * @param \Generated\Transfer\Pid\PidUserTransfer $pidUserTransfer
     *
     * @return \Generated\Transfer\Pid\PidUserTransfer|null
     */
    public function createPidUser(PidUserTransfer $pidUserTransfer): ?PidUserTransfer;

    /**
     * @param \Generated\Transfer\Pid\PidMachineTransfer $pidMachineTransfer
     *
     * @return \Generated\Transfer\Pid\PidMachineTransfer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    public function createPidMachine(PidMachineTransfer $pidMachineTransfer): PidMachineTransfer;

    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JzIT\Pid\Exception\MissingDataException
     * @throws \JzIT\Pid\Exception\UserAlreadyExistsException
     */
    public function createPidStat(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer;
}

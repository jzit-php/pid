<?php

namespace JzIT\Pid\Business\Model\Writer;

use Generated\Transfer\Pid\PidStatsTransfer;
use JzIT\Pid\Persistence\PidEntityManagerInterface;
use JzIT\Pid\Persistence\PidRepositoryInterface;

interface PidStatWriterInterface
{
    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     */
    public function writeStats(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer;
}

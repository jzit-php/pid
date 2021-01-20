<?php

namespace JzIT\Pid\Business;

use Generated\Transfer\Pid\PidStatsTransfer;

interface PidFacadeInterface
{
    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     */
    public function writePidStat(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer;
}

<?php

namespace App\Core\Common\Models\Steps;

use App\Core\Common\Serialization\Serializa;

class PassoTicagem extends Serializa
{
    protected int $idNo;

    /**
     * @return int
     */
    public function getIdNo(): int
    {
        return $this->idNo;
    }

    /**
     * @param  int  $idNo
     * @return void
     */
    public function setIdNo(int $idNo): void
    {
        $this->idNo = $idNo;
    }
}

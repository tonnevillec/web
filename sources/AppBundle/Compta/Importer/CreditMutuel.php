<?php

namespace AppBundle\Compta\Importer;

use AppBundle\Model\ComptaCompte;

class CreditMutuel implements Importer
{
    const CODE = 'CMUT';

    public function initialize($filePath)
    {
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return false;
    }

    /**
     * @return Operation[]
     */
    public function extract()
    {
        return [];
    }

    public function getCompteId()
    {
        return ComptaCompte::COURANT_CMUT;
    }
}

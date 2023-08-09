<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula;

enum PredicadoTipoEnum
{
    case CONDICIONAL;
    case BICONDICIONAL;
    case DISJUNÇÃO;
    case CONJUNÇÃO;
    case PREDICATIVO;
}

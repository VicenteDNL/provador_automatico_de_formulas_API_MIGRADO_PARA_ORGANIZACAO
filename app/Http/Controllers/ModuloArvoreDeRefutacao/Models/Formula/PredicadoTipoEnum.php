<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula;

enum PredicadoTipoEnum
{
    case CONDICIONAL;
    case BICONDICIONAL;
    case DISJUNÇÃO;
    case CONJUNÇÃO;
    case PREDICATIVO;
}

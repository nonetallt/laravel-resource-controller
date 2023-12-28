<?php

namespace Nonetallt\LaravelResourceBoiler;

enum ResourceControllerAction : string
{
    case Index   = 'index';
    case Create  = 'create';
    case Show    = 'show';
    case Store   = 'store';
    case Edit    = 'edit';
    case Update  = 'update';
    case Destroy = 'destroy';
}

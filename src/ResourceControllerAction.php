<?php

namespace Nonetallt\LaravelResourceBoiler;

enum ResourceControllerAction : string
{
    case Index   = 'index';
    case Create  = 'create';
    case Show    = 'show';
    case Edit    = 'edit';
    case Store   = 'store';
    case Update  = 'update';
    case Destroy = 'destroy';
}

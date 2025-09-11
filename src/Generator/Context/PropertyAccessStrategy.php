<?php

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

enum PropertyAccessStrategy
{
    case CONSTRUCTOR;
    case PROPERTY;
    case SETTER;
}

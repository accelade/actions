<?php

declare(strict_types=1);

namespace Accelade\Actions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Accelade\Actions\Action make(?string $name = null)
 *
 * @see \Accelade\Actions\Action
 */
class Action extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Accelade\Actions\Action::class;
    }
}

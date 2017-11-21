<?php

namespace Nutrition\Test\Fixture;

use Nutrition\Console\Command;
use Base;

class MyCommand extends Command
{
    public static function registerSelf(Base $app)
    {
        $app->route('GET /cmd/write [cli]', self::class . '->writeAction');
        $app->route('GET /cmd/writeln [cli]', self::class . '->writelnAction');
        $app->route('GET /cmd/writetable [cli]', self::class . '->writeTableAction');
        $app->route('GET /cmd/withoption [cli]', self::class . '->withOptionAction');
        $app->route('GET /cmd/hasoption [cli]', self::class . '->hasOptionAction');
    }

    public function writeAction(Base $app)
    {
        $this->write('Line');
    }

    public function writelnAction(Base $app)
    {
        $this->writeln('Line');
    }

    public function writeTableAction(Base $app)
    {
        $this->writeTable(['Col 1', 'Col 2'], [
            ['C1R1','C2R1'],
            ['C1R2','C2R2'],
        ]);
    }

    public function withOptionAction(Base $app)
    {
        $this->write($this->getOption('option'));
    }

    public function hasOptionAction(Base $app)
    {
        $this->write($this->hasOption('option')?'TRUE':'FALSE');
    }
}

<?php

declare(strict_types=1);

namespace Monooso\Bolt;

use craft\test\Craft;

final class BoltModule extends Craft
{
    public function _beforeSuite($settings = []): void
    {
        if ($this->_getConfig('fullMock') === true) {
            return;
        }

        parent::setupDb();
    }

    /**
     * No-op
     */
    public function setupDb(): void
    {
        return;
    }
}

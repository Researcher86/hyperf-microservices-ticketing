<?php

namespace Orders\Model;

use Hyperf\Database\Model\Events\Saving;

trait Version
{
    public function saving(Saving $event)
    {
        $version = $this->version;
        if (!$version) {
            $version = 1;
        } else {
            ++$version;
        }

        // Проверяем установил ли кто-то уже такую же версию, если запись с такой версией существует,
        // значит наши данные устарели, и их не нужно сохранять
        $isModifiedRecord = $this::query()
            ->where('id', '=', $this->id)
            ->where('version', '=', $version)
            ->exists();

        if ($isModifiedRecord) {
            throw new \RuntimeException(sprintf('This entry id [%d] has already been changed version [%d]', $this->id, $version));
        }

        $this->version = $version;
    }
}
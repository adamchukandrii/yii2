<?php declare(strict_types=1);

namespace app\models;

use function array_map;
use function in_array;

enum Status: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case FAILED = 'failed';
    case FINISHED = 'finished';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::IN_PROGRESS => 'In Progress',
            self::FAILED => 'Failed',
            self::FINISHED => 'Finished',
        };
    }

    /**
     * @return array
     */
    public static function getStatusesRange(): array
    {
        return array_map(static fn($case) => $case->value, self::cases());
    }

    /**
     * @param Status $newStatus
     *
     * @return bool
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::NEW => in_array($newStatus, [self::IN_PROGRESS], true),
            self::IN_PROGRESS => in_array($newStatus, [self::FINISHED, self::FAILED], true),
            default => false
        };
    }
}

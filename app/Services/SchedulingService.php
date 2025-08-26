<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Cache\Repository as Cache;

class SchedulingService
{
    public function __construct(protected readonly Cache $cache) {}

    public function scheduleOperation(Carbon $initialDateTime, int $totalMinutes, array $dates): Carbon
    {
        $currentDateTime = $initialDateTime->copy();

        while ($totalMinutes > 0) {
            // Get the current date (YYYY-MM-DD)
            $currentDate = $currentDateTime->format('Y-m-d');

            // If the day is not specified, treat it as a full 24-hour working day.
            if (! isset($dates[$currentDate])) {
                $dayData = [
                    'shift1_start' => '00:00',
                    'shift1_end' => '00:00',
                ];
            } else {
                $dayData = $dates[$currentDate];

                // If the day is flagged as non‑working, skip to the previous day.
                if (isset($dayData['nwd']) && $dayData['nwd'] === 1) {
                    $currentDateTime->subDay()->endOfDay();
                    continue;
                }
            }

            // Build the shifts array (supporting up to 4 shifts)
            $shifts = [];
            for ($i = 1; $i <= 4; $i++) {
                if (isset($dayData["shift{$i}_start"], $dayData["shift{$i}_end"])) {
                    $shiftStart = Carbon::parse("{$currentDate} ".$dayData["shift{$i}_start"]);
                    $shiftEnd = Carbon::parse("{$currentDate} ".$dayData["shift{$i}_end"]);
                    // If the shift end time is "00:00", treat it as the start of the next day.
                    if ($dayData["shift{$i}_end"] === '00:00') {
                        $shiftEnd->addDay();
                    }
                    $shifts[] = [
                        'start' => $shiftStart,
                        'end' => $shiftEnd,
                    ];
                }
            }

            $allocated = false;
            // Process shifts in reverse order (from the last shift backwards)
            foreach (array_reverse($shifts) as $shift) {
                // Clamp the current time to the shift's end if it's later.
                if ($currentDateTime->gt($shift['end'])) {
                    $currentDateTime = $shift['end']->copy();
                }
                // If current time is within the shift...
                if ($currentDateTime->between($shift['start'], $shift['end'])) {
                    // Determine the available minutes from the shift start up to current time.
                    $availableMinutes = $currentDateTime->diffInMinutes($shift['start']);
                    if ($availableMinutes > 0) {
                        $minutesToSubtract = min($availableMinutes, $totalMinutes);
                        $currentDateTime->subMinutes($minutesToSubtract);
                        $totalMinutes -= $minutesToSubtract;
                        $allocated = true;

                        if ($totalMinutes <= 0) {
                            break;
                        }
                    }
                }
            }
            // If no minutes were allocated on this day, move to the previous day.
            if ($totalMinutes > 0 && ! $allocated) {
                $currentDateTime->subDay()->endOfDay();
            }
        }

        return $currentDateTime;
    }

    /**
     * @throws Exception
     */
    public function scheduleBackward(string $operation, array $successors, array &$scheduledTimes, $depth = 0)
    {
        if ($depth > 1000) {
            throw new Exception('Exceeded maximum scheduling depth! Possible infinite loop.');
        }

        if (isset($scheduledTimes[$operation])) {
            return $scheduledTimes[$operation];
        }

        $nextOps = $successors[$operation] ?? [];
        if (empty($nextOps)) {
            $scheduledTimes[$operation] = $this->scheduleOperation($operation, null);
            return $scheduledTimes[$operation];
        }

        $successorStartTimes = [];
        foreach ($nextOps as $nextOp) {
            $schedForSuccessor = $this->scheduleBackward($nextOp, $successors, $scheduledTimes, $depth + 1);
            $successorStartTimes[] = $schedForSuccessor['start'];
        }

        $earliestSuccessorStart = min($successorStartTimes);
        $scheduledTimes[$operation] = $this->scheduleOperation($operation, $earliestSuccessorStart);

        return $scheduledTimes[$operation];
    }

    function hasCycleDFS($node, &$successors, &$visited, &$stack) {
        // If the node is already in the recursion stack, there's a cycle.
        if (isset($stack[$node])) {
            return true;
        }

        // If already visited (but not in the stack), no cycle.
        if (isset($visited[$node])) {
            return false;
        }

        // Mark this node as visited and add to recursion stack.
        $visited[$node] = true;
        $stack[$node] = true;

        // Recursively visit successors.
        foreach ($successors[$node] ?? [] as $nextOp) {
            if ($this->hasCycleDFS($nextOp, $successors, $visited, $stack)) {
                return true;
            }
        }

        // Remove from recursion stack (we’re done with this path).
        unset($stack[$node]);

        return false;
    }
}

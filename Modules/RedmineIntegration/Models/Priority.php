<?php

namespace Modules\RedmineIntegration\Models;

use App\Models\Priority as PriorityModel;
use Illuminate\Support\Arr;

class Priority extends CompanyProperty
{
    protected const REDMINE_PRIORITIES = 'redmine_priorities';

    protected ClientFactory $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function synchronize(): void
    {
        $client = $this->clientFactory->createCompanyClient();
        $redminePriorities = $client->issue_priority->all()['issue_priorities'];
        $savedPriorities = $this->getAll();

        // Merge priorities info from the redmine with the stored priorities
        $priorities = array_map(static function (array $priority) use ($savedPriorities) {
            // Try find saved priority with the same ID
            $savedPriority = Arr::first($savedPriorities, function ($savedPriority) use ($priority) {
                return $savedPriority['id'] === $priority['id'];
            });

            if (isset($savedPriority) && PriorityModel::find($savedPriority['priority_id'])) {
                // If priority already synchronized, use the stored value
                $priority['priority_id'] = $savedPriority['priority_id'];
            } else {
                // If priority is not synchronized
                if (PriorityModel::find($priority['id'])) {
                    // Use internal priority with the same ID, if it is exist
                    $priority['priority_id'] = $priority['id'];
                } else {
                    // Use internal priority with the maximum ID otherwise
                    $priority['priority_id'] = PriorityModel::max('id');
                }
            }

            return $priority;
        }, $redminePriorities);

        $this->setAll($priorities);
    }

    public function getAll(): array
    {
        $property = $this->get(static::REDMINE_PRIORITIES);

        return isset($property) ? (json_decode($property->value, true) ?: []) : [];
    }

    public function setAll(array $value): void
    {
        $this->set(static::REDMINE_PRIORITIES, json_encode($value));
    }
}

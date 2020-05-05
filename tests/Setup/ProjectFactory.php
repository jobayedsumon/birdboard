<?php


namespace Tests\Setup;


use App\Project;
use App\Task;

class ProjectFactory
{
    protected $tasksCount = 0;

    public function withTasks($count)
    {
        $this->tasksCount = $count;

        return $this;
    }

    public function create()
    {
        $project = factory(Project::class)->create();

        factory(Task::class, $this->tasksCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }

}

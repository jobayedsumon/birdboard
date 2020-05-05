<?php

namespace Tests\Unit;

use App\Project;
use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_a_project()
    {
        $task = factory(Task::class)->create();

        $this->assertInstanceOf(Project::class, $task->project);
    }

    public function test_it_has_a_path()
    {
        $task = factory(Task::class)->create();

        $this->assertEquals('/projects/'.$task->project->id.'/tasks/'.$task->id, $task->path());
    }

    public function test_it_can_be_completed()
    {
        $task = factory(Task::class)->create();

        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->completed);
    }

    public function test_it_can_be_marked_as_incomplete()
    {
        $this->withoutExceptionHandling();

        $task = factory(Task::class)->create(['completed' => true]);

        $this->assertTrue($task->completed);

        $task->incomplete();

        $this->assertFalse($task->completed);
    }
}

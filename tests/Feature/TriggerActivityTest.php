<?php

namespace Tests\Feature;

use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);

        $this->assertEquals('created', $project->activity->first()->description);
    }

    /** @test */
    public function updating_a_project()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'Changed']);

        $this->assertCount(2, $project->activity);

        $this->assertEquals('updated', $project->activity->last()->description);
    }

    /** @test */
    public function creating_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->assertCount(2, $project->activity);

        $this->assertEquals('created_task', $project->activity->last()->description);
    }

    /** @test */
    public function completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'foobar',
            'completed' => true
        ]);

        $this->assertCount(3, $project->activity);

        $this->assertEquals('completed_task', $project->activity->last()->description);
    }

    /** @test */
    public function uncompleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'foobar',
            'completed' => true
        ]);

        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'foobar',
            'completed' => false
        ]);

        $this->assertCount(4, $project->activity);

        $this->assertEquals('uncompleted_task', $project->activity->last()->description);
    }

    /** @test */
    public function deleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $project->tasks[0]->delete();

        $this->assertCount(3, $project->activity);

        $this->assertEquals('deleted_task', $project->activity->last()->description);
    }

}

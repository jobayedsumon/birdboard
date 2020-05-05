<?php

namespace Tests\Feature;

use App\Project;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    /** @test */
    public function only_the_owner_of_project_can_add_task()
    {
        $project = ProjectFactory::create();

        $this->post($project->path() . '/tasks', ['body' => 'Test Task']);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test Task']);
    }

    /** @test */
    public function only_project_owner_can_update_task()
    {

        $project = ProjectFactory::withTasks(1)->create();

        $this->patch($project->tasks[0]->path(), [
                'body' => 'Changed',
            ]);

        $this->assertDatabaseMissing('tasks', [
            'body' => 'Changed',
        ]);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    /** @test */
    public function task_can_be_updated()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'Changed',
            ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'Changed',
        ]);
    }

    /** @test */
    public function task_can_be_completed()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'Changed',
                'completed' => true,
            ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'Changed',
            'completed' => true,
        ]);
    }

    /** @test */
    public function task_can_be_marked_as_incomplete()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'Changed',
                'completed' => true,
            ]);

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'Changed',
                'completed' => false,
            ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'Changed',
            'completed' => false,
        ]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $project = ProjectFactory::create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
    }
}

<div class="card mt-3">
    <ul class="list-reset">
        @foreach($project->activity as $activity)
            <li class="text-xs mb-1">
                @include('projects.activity.' . $activity->description)
                <span class="text-grey">{{ $activity->created_at->diffForHumans(null, true) }}</span>
            </li>
        @endforeach
    </ul>
</div>

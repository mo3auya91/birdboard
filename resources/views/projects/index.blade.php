<ul>

    @forelse($projects as $project)
        <li>
            <a href="{{ $project->path() }}">{{ $project->title }}</a>
        </li>
    @empty
        no projects
    @endforelse
</ul>

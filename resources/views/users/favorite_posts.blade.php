@if (count($favorites) > 0)
    <ul class="list-unstyled">
        @foreach ($favorites as $favorite)
            <li class="media pb-2 pt-1 border-bottom">
                <img class="mr-2 rounded" src="{{ Gravatar::src($favorite->user->email, 50) }}" alt="">
                <div class="media-body">
                    <div>
                        {!! link_to_route('users.show', $favorite->user->name, ['id' => $favorite->user->id]) !!} <span class="text-muted">posted at {{ $favorite->created_at }}</span>
                    </div>
                    <div>
                        <p class="mb-0">{!! nl2br(e($favorite->content)) !!}</p>
                    </div>
                    <div>
                        @include('favorite.favorite_btn', ['micropost' => $favorite])
                    </div> 
                 </div>
            </li>
        @endforeach
    </ul>
@endif
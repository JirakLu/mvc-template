@extends("layout.layout")

@section("content")
    <div>Testing</div>

    <a href="{{$createLink("articles")}}">Adam je omegapepega</a>

    <div class="flex flex-row gap-5">
        @foreach($data as $dat)
            <div class="p-5 rounded-xl bg-blue-500 flex flex-col space-y-2">
                <h1>{{$dat->jmeno}}</h1>
                <h1>{{$dat->prijmeni}}</h1>
                <p>{{$dat->popis}}</p>
            </div>
        @endforeach
    </div>
@endsection
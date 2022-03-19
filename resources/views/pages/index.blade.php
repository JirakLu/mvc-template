@extends("layout.layout")

@section("content")
    <div>Testing</div>

    <a href="{{$createLink("articles")}}">Adam je omegapepega</a>

    @foreach($data as $dat)
        <h1>{{$dat->jmeno}}</h1>
        <h1>{{$dat->prijmeni}}</h1>
        <p>{{$dat->popis}}</p>
    @endforeach
@endsection
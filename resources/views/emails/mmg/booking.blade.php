@component("mail::message")

    <ul style="list-style: none;">
        <li> Subject Line:- {{ $subject }}  </li>
        <li> Detail needed to Ship the Kits  </li>
        <li> Customer Name:- {{ $user->first_name }} {{ $user->last_name }}</li>
        <li> Phone No:- {{ is_null($user->contact_no) ? "NIL" : $user->contact_no }}</li>
        <li> Email Address:- {{ $user->email }}</li>
    </ul>
    {{--@component('mail::table')--}}
        | Test Name       | Test Price         |
        | ------------- |:-------------:|
        @foreach($testNamesArr as $testName)
            |{{ $testName["test_name"] }}     | INR {{ $testName["price"] }}   |
        @endforeach
    {{--@endcomponent--}}
    Thanks,
    {{ config('app.name') }}

@endcomponent
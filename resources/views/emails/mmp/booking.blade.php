@component("mail::message")

    Hello, Application user name <b>{{ $name }}</b> has registered for new tests. <br/>

    <ul>
        <li> Name: {{ $name }}</li>
        <li> Email Address: {{ $email }}</li>
    </ul>
    @component('mail::table')
        | Test Name       | Test Price         |
        | ------------- |:-------------:|
        @foreach($testNamesArr as $testName)
            |{{ $testName["test_name"] }}     | INR {{ $testName["price"] }}   |
        @endforeach
    @endcomponent
    Thanks,
    {{ config('app.name') }}

@endcomponent
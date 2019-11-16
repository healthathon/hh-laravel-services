
<div>
    Hello,<br/>
    <ul style="list-style: none;padding-left: 0;">

        <li style="padding-left: 0;"> Detail needed to Ship the Kits  </li>
        <li style="padding-left: 0;"> Customer Name:- {{ $user->first_name }} {{ $user->last_name }}</li>
        <li style="padding-left: 0;"> Phone No:- {{ is_null($user->contact_no) ? "NIL" : $user->contact_no }}</li>
        <li style="padding-left: 0;"> Email Address:- {{ $user->email }}</li>

        @if($data['address']!='')<li style="padding-left: 0;"> Address:- {{ $data['address'] }}</li>@endif
        @if($data['pincode']!='')<li style="padding-left: 0;"> Pincode:- {{ $data['pincode'] }}</li>@endif


        <li style="padding-left: 0;"><br/><strong>Test Information:-</strong></li>

        <li style="padding-left: 0;">

            <ul>
                @foreach($data['testNamesArr'] as $testName)
                    <li>
                        {{ $testName["test_name"] }} (INR {{ $testName["price"] }})
                    </li>

                @endforeach

            </ul>
        </li>
    </ul>
    
    <br/>
    <strong>Thanks,</strong><br/>
    {{ config('app.name') }}
</div>
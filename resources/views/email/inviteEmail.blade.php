<h3>Hallo, {{ $email }} !</h3>
 
<p>Welcome in Smartstar LMS <a href="{{env('FRONT_URL')}}">smartstar.bintangpassa.com</a></p>
<p>You are invited as a {{ $role }} at {{ ucwords($sch) }} (school ID: {{ $schid }}), please <a href="{{env('FRONT_URL')}}/login">login</a> to confirm with password below</p>
<p>Your password: {{ $password }}</p>
<p>Your school ID: {{ $schid }}</p>
<br>
<p>Thankyou</p>
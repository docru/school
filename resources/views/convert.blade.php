<form method="post" xmlns="http://www.w3.org/1999/html">
    @csrf
    <textarea name="text" id="" cols="30" rows="10">{{$text}}</textarea>
    <input type="submit"/>
</form>

<hr>
<pre>
{{$convertText}}
</pre>

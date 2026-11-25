<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">log uit</button>
</form>

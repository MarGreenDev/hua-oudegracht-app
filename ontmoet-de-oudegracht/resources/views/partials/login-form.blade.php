<div class="flex items-center justify-center bg-theme-3 p-5">

    <div class="form-container">
        <form action="/cms-login" method="post" class="flex flex-col gap-4">
            @csrf
            <div class="form-element">
                <label for="username">Username:</label>
                <input type="text" name="name" id="username" placeholder="Username" value="{{ old('name' )}}">
            </div>
            <div class="form-element">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="••••••••">
            </div>
            <button type="submit" class="bg-theme-1 text-white">Log in (✿◠‿◠)</button>
        </form>
    </div>
</div>
